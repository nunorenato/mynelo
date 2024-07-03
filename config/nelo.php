<?php

return [
    'nelo_api_url' => env('NELO_API_URL', 'https://api.nelo.eu'),

    'emails' => [
      'internal_from' => 'fabrica@nelo.eu',
      'from_name' => 'My Nelo',
      'admins' => env('APP_ENV')=='local'?['nuno.rammos@gmail.com']:['nuno.ramos@nelo.eu', 'andre.santos@nelo.eu'],
      'external_from' => 'noreply@nelo.eu',
    ],

    'shop' => [
        'base_product_url' => 'https://paddle-lab.com/',
    ],

    'magento' => [
        'api' => [
            'url' => env('MAGENTO_API_URL', 'https://paddlesportsdesign.com/rest/V1'),
            'token' => env('MAGENTO_API_TOKEN'),
        ],
        'coupon_rule' => env('MAGENTO_COUPON_RULE'),
    ],

    'layups' => ['E', 'F', 'G', 'SCS', 'WWR', 'FC', 'P1'],

    'myorder' => [
        'base_url' => 'https://myorder.nelo.eu/',
    ]
];
