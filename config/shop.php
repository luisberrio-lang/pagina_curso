<?php

return [
    'currency' => strtoupper(env('SHOP_CURRENCY', 'PEN')),
    'supported_currencies' => ['PEN'],

    'business' => [
        'name' => env('BUSINESS_NAME', env('APP_NAME', 'Cursos de Ingeniería Online')),
        'description' => env('BUSINESS_DESCRIPTION'),
        'email' => env('BUSINESS_EMAIL'),
        'country' => env('BUSINESS_COUNTRY', 'PE'),
        'whatsapp' => env('WHATSAPP_NUMBER', '51929765265'),
        'support_text' => env('BUSINESS_SUPPORT_TEXT', 'Contáctanos para consultas sobre los cursos y el acceso al contenido digital.'),
        'support_hours' => env('BUSINESS_SUPPORT_HOURS'),
    ],
];
