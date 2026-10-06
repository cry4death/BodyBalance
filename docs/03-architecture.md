# 03. Архитектура

## Подход

**Модульный монолит** на Laravel. Без микросервисов и без SPA: серверный рендер проще всего даёт SEO и PageSpeed ≥ 90.

## Стек

| Слой | Выбор | Комментарий |
|---|---|---|
| Ядро | PHP 8.4, **Laravel 13** | Вышел 17.03.2026; PHP 8.3–8.5; bugfix до Q3 2027, security до Q1 2028 |
| Админка | **Filament 5** | = Filament 4 + Livewire 4 (вышел 16.01.2026). CRUD, таблицы, фильтры, импорт/экспорт, MFA, русская локализация |
| Витрина | Blade-компоненты + **Livewire 4** (фильтры, корзина, чекаут, ЛК) + Alpine.js | Один стек с Filament |
| CSS | **Tailwind CSS 4** + Vite | Палитра из ТЗ — токены в `@theme`; `.btn-primary`, `.btn-ghost`, `.chip` — Blade-компоненты |
| БД | MySQL 8 (или PostgreSQL — что даёт хостинг в РБ) | |
| Кэш/сессии/очереди | Redis + Laravel Horizon | |
| Тесты/качество | Pest 4 (вкл. браузерные тесты), Larastan, Pint, GitHub Actions, Lighthouse CI | |
| Инфраструктура | VPS **в РБ**, Nginx + PHP-FPM, Supervisor, cron; деплой Deployer (zero-downtime) или Forge | Хостинг в РБ обязателен (Указ №60) |
| Локально | Laravel Herd (Windows) или Docker/Sail | На машине: PHP 8.4, Composer 2.10, Node 24 |

## Структура модулей

Контроллеры, Livewire-компоненты и ресурсы Filament — тонкие, вызывают Actions/сервисы из модулей.

```
app/Domain/
  Studio/      категории услуг, процедуры, специалисты, отзывы о студии, FAQ, преимущества, настройки главной
  Booking/     клиент YCLIENTS, синхронизация справочника, конфиг виджета, health-check
  Catalog/     товары, бренды, категории, медиа, отзывы о товарах, связи «покупают вместе»/«похожие»
  Cart/        корзина (гость по cookie + пользователь, слияние при входе), расчёт итогов
  Promotions/  промокоды и правила скидок
  Shipping/    способы доставки, тарифы по зонам, порог бесплатной доставки
  Checkout/    матрица доставка×оплата, резерв остатков, создание заказа в транзакции
  Orders/      заказ + позиции-снимки, статусы и переходы, события, письма, экспорт
  Payments/    интерфейс PaymentGateway → BePaidGateway / WebpayGateway, вебхуки, сверка
  Customers/   пользователи, адреса, соц. аккаунты, настройки уведомлений, анонимизация
  Seo/         SEO-поля (morph), шаблоны мета, JSON-LD, sitemap, robots, 301-редиректы
app/Filament/  ресурсы админки
app/Http/, app/Livewire/  публичная часть
```

## Ключевые решения

1. **Деньги — целые числа в копейках BYN** (cast в модели), никаких `float`. Форматирование — `Number::currency(..., 'BYN', 'ru')`.
2. **Позиции заказа — снимки**: название, бренд, артикул, цена, количество, скидка; адрес и контакты — тоже копией. Правки каталога не меняют старые заказы.
3. **Два статуса заказа**:
   - `payment_status`: `pending`, `paid`, `failed`, `refunded` (+ `awaiting` для ЕРИП);
   - `status`: `new`, `processing`, `shipped`, `ready_for_pickup`, `completed`, `cancelled`.
   Оба — PHP enum с картой разрешённых переходов; в ЛК маппим на формулировки ТЗ (оплачен / выполнен / отменён…).
4. **Внешние сервисы за интерфейсами** (`PaymentGateway`, `BookingProvider`) — смена эквайера без переписывания чекаута; в тестах `Http::fake()`.
5. **Побочные эффекты — события + очереди**: `OrderPlaced` → письма клиенту и админу, Telegram админу, аналитика; `OrderStatusChanged` → письмо клиенту.
6. **Контент главной — структурированный** (модели + `spatie/laravel-settings`), без свободного конструктора страниц: заказчик правит тексты/фото, но не ломает вёрстку.
7. **Одна таблица брендов** для магазина и блока на главной (флаг `show_on_home`, `is_priority`).
8. **Безопасность доступа**: Policies на заказы/адреса/отзывы (защита от IDOR); подтверждение гостевого заказа — по UUID/подписанной ссылке, не `/orders/123`.
9. **Цена всегда пересчитывается на сервере** (корзина, промокод, доставка) — клиенту не доверяем.
10. **Резерв остатков** при создании заказа в транзакции с `lockForUpdate()`; TTL резерва для неоплаченных онлайн-заказов, джоба освобождения.

## Черновик модели данных

**Студия**
- `service_categories`: slug, name, short_description, hero_text, approach_text, specialist_id, sort, yclients_category_ids (json), seo
- `services` (процедуры): service_category_id, name, description, duration_min, price, price_is_from, yclients_service_id, is_active, sort
- `specialists`: name, position, qualification, bio, photo, yclients_staff_id, sort
- `testimonials`: author_name, text, is_published, sort
- `faqs`: question, answer, service_category_id (null = главная), sort
- `advantages`: title, text, icon, sort
- `pages`: slug, title, body (юр. страницы), seo
- settings: контакты, соцсети, hero, «о студии» + галерея, реквизиты (УНП, торговый реестр), YCLIENTS (company_id, form_id, режим записи), магазин (TTL резерва, email уведомлений)

**Каталог**
- `brands`: name, slug, logo, description, show_on_home, is_priority, sort, seo
- `product_categories`: name, slug, sort, seo
- `products`: brand_id, category_id, sku, name, slug, short_description, description, composition, usage, benefits (json), characteristics (json), price, old_price, stock, is_active, is_bestseller, is_new, popularity_weight, sales_count, rating_avg, reviews_count, seo
- `product_relations`: product_id, related_id, type (`cross_sell` / `similar`)
- `product_reviews`: product_id, user_id, rating, text, status (pending/approved/rejected), is_verified_purchase
- media — `spatie/laravel-medialibrary`

**Магазин**
- `carts` / `cart_items`: user_id или session token, product_id, qty
- `promo_codes`: code, type (percent/fixed), value, min_total, starts_at, ends_at, usage_limit, per_user_limit, used_count, is_active
- `shipping_methods` / `shipping_rates`: зона, цена, порог бесплатной доставки
- `orders`: uuid, number (BB-2026-000123), user_id (null для гостя), contact_* (снимок), shipping_method, shipping_address (json снимок), payment_method, status, payment_status, subtotal, discount, shipping_cost, total, promo_code, comment, reserved_until, placed_at
- `order_items`: order_id, product_id, snapshot (name, brand, sku, price), qty, total
- `payments`: order_id, provider, provider_transaction_id (unique), amount, currency, status, raw_payload, processed_at
- `order_status_history`: order_id, from, to, user_id, comment

**Покупатели**
- `users`: name, email (unique, null), phone (unique E.164, null), password (null для OAuth-only), birth_date, email_verified_at, phone_verified_at, pd_consent_at + версия политики, marketing_consent_at, deleted_at / anonymized_at
- `social_accounts`: user_id, provider (google/yandex), provider_user_id, email
- `addresses`: user_id, label, recipient, phone, city, street, house, apartment, is_default
- `notification_preferences`: order_status, booking_reminders ⚠, marketing, sms_duplicate

**Сервисные**
- `seo_meta` (morph): title, description, h1, og_image, canonical, noindex
- `redirects`: from_path, to_path, code
- `activity_log` (spatie)

## URL-схема (черновик)

```
/                                  главная
/uslugi/{slug}                     категория услуг (massazh-tela, ukhody-dlya-tela, ...)
/magazin                           каталог (?brand=&category=&sort=&page=)
/magazin/{category-slug}           категория каталога
/brendy/{brand-slug}               бренд (опц.)
/tovar/{product-slug}              товар
/korzina                           корзина
/oformlenie                        чекаут
/zakaz/{uuid}                      подтверждение заказа
/login, /register, /forgot-password
/kabinet, /kabinet/zakazy, /kabinet/adresa, /kabinet/profil
/politika-konfidencialnosti, /oferta, /dostavka-i-oplata
/sitemap.xml, /robots.txt
/webhooks/bepaid, /webhooks/webpay
```
Фильтры каталога — query-параметры с canonical на базовый URL категории; поиск — `noindex`.

## Окружения

- **local** — Herd/Sail, Telescope, Debugbar.
- **staging** — закрыт basic auth, `robots.txt: Disallow: /`, `X-Robots-Tag: noindex`, песочница эквайера.
- **production** — VPS в РБ, бэкапы (БД + медиа) с проверкой восстановления, Sentry, Pulse, uptime-мониторинг.
