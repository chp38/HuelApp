<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Shopify
    |--------------------------------------------------------------------------
    |
    | This file contains all the variables needed for the Shopify API
    |
    */
    'auth_token' => env('SHOPIFY_AUTH_TOKEN'),
    'auth_password' => env('SHOPIFY_AUTH_PASSWORD'),
    'store' => env('SHOPIFY_STORE'),
    'slug_prefix' => env('SHOPIFY_SLUG_PREFIX', '/admin/api/2020-07/'),
    'app_secret' => env('SHOPIFY_APP_SECRET'),
];