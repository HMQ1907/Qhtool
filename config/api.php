<?php

return [
    'webike' => [
        'user' => env('WEBIKE_USER'),
        'token' => env('WEBIKE_TOKEN'),
        'endpoints' => [
            'fetch_product_order' => env('WEBIKE_FETCH_PRODUCT_ORDER_ENDPOINT'),
            'fetch_product' => env('WEBIKE_FETCH_PRODUCT_ENDPOINT'),
        ],
    ],
];
