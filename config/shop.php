<?php

return [
    'currency' => strtoupper(env('SHOP_CURRENCY', 'PEN')),
    'supported_currencies' => ['PEN'],

    'business' => [
        // La identidad comercial no debe depender del nombre técnico de la aplicación.
        'name' => env('BUSINESS_NAME') ?: 'Cursos de Ingeniería Online',
        'description' => env('BUSINESS_DESCRIPTION') ?: 'Cursos y contenidos digitales de ingeniería orientados al aprendizaje y desarrollo profesional.',
        'email' => env('BUSINESS_EMAIL'),
        'country' => strtoupper(env('BUSINESS_COUNTRY', 'PE')),
        'country_name' => match (strtoupper(env('BUSINESS_COUNTRY', 'PE'))) {
            'PE' => 'Perú',
            default => strtoupper(env('BUSINESS_COUNTRY', 'PE')),
        },
        'whatsapp' => env('WHATSAPP_NUMBER', '51929765265'),
        'support_text' => env('BUSINESS_SUPPORT_TEXT') ?: 'Contáctanos para consultas sobre los cursos y el acceso al contenido digital.',
        'support_hours' => env('BUSINESS_SUPPORT_HOURS'),
    ],
];
