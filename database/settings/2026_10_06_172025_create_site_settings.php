<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

/**
 * Initial values come from the spec; everything else is filled in by the client in the admin panel.
 */
return new class extends SettingsMigration
{
    public function up(): void
    {
        // Контакты и соцсети
        $this->migrator->add('contacts.phone', null);
        $this->migrator->add('contacts.email', null);
        $this->migrator->add('contacts.address', 'д. Копище, ул. Леонардо да Винчи, 2');
        $this->migrator->add('contacts.map_url', null);
        $this->migrator->add('contacts.latitude', null);
        $this->migrator->add('contacts.longitude', null);
        $this->migrator->add('contacts.opening_hours', []);
        $this->migrator->add('contacts.telegram_username', null);
        $this->migrator->add('contacts.viber_phone', null);
        $this->migrator->add('contacts.instagram_username', null);

        // Реквизиты продавца
        $this->migrator->add('company.legal_name', null);
        $this->migrator->add('company.unp', null);
        $this->migrator->add('company.legal_address', null);
        $this->migrator->add('company.registration_info', null);
        $this->migrator->add('company.trade_register_number', null);
        $this->migrator->add('company.trade_register_date', null);

        // Главная страница
        $this->migrator->add('home.hero_title', 'Body Balance');
        $this->migrator->add('home.hero_slogan', 'Баланс тела и кожи в одной студии');
        $this->migrator->add('home.hero_image', null);
        $this->migrator->add('home.hero_image_alt', null);
        $this->migrator->add('home.about_title', 'О студии');
        $this->migrator->add('home.about_text', null);
        $this->migrator->add('home.about_gallery', []);

        // Онлайн-запись
        $this->migrator->add('booking.mode', 'widget');
        $this->migrator->add('booking.yclients_company_id', null);
        $this->migrator->add('booking.widget_url', null);
        $this->migrator->add('booking.client_cabinet_url', null);
        $this->migrator->add('booking.fallback_message', 'Онлайн-запись временно недоступна. Запишитесь по телефону или напишите нам в мессенджер — подберём удобное время.');
    }
};
