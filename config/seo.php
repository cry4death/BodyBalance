<?php

return [

    'site_name' => env('APP_NAME', 'Body Balance'),

    // Appended to default titles: "Массаж для тела — Body Balance".
    // Titles entered in the admin panel are used verbatim.
    'title_separator' => ' — ',

    // Placeholder until the client approves the copy; editable per page in the admin panel.
    'default_description' => 'Студия массажа и косметологии Body Balance в Копище: массаж и уходы для лица и тела, пилатес на реформере, профессиональная косметика.',

    // Absolute URL of the default Open Graph image.
    'default_image' => env('SEO_DEFAULT_IMAGE'),

    /*
    | Fixed site pages whose SEO is edited in the admin panel («SEO страниц»).
    | Key — route name; label — shown in the admin panel; title/h1 — defaults.
    | Add pages here as their routes appear (shop.index, cart, contacts...).
    */
    'pages' => [
        'home' => [
            'label' => 'Главная',
            'title' => 'Body Balance — студия массажа и косметологии в Копище',
            'h1' => 'Body Balance',
        ],
    ],

    'robots' => [
        // Production only; every other environment gets "Disallow: /".
        'disallow' => [
            '/admin',
            '/kabinet',
            '/korzina',
            '/oformlenie',
            '/zakaz',
            '/login',
            '/register',
            '/forgot-password',
            '/reset-password',
        ],
        'extra' => [
            // Yandex: ignore UTM parameters to avoid duplicate pages.
            'Clean-param: utm_source&utm_medium&utm_campaign&utm_content&utm_term',
        ],
    ],

    'sitemap' => [
        'path' => public_path('sitemap.xml'),

        // Route names of fixed pages included in the sitemap.
        'routes' => ['home'],

        // Model classes implementing Spatie\Sitemap\Contracts\Sitemapable
        // with a static sitemapQuery(): Builder (products, service categories...).
        'models' => [],
    ],

];
