<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | This file controls how your Laravel API can be accessed from
    | different origins (like your React frontend on Netlify).
    |
    */

    // Allow API routes + sanctum
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // Allow all HTTP methods (GET, POST, PUT, DELETE, etc.)
    'allowed_methods' => ['*'],

    // Allow only your frontend URLs
    'allowed_origins' => [
        'http://localhost:3000',          // local dev
        'http://127.0.0.1:3000',          // local dev
        'https://pawsitivity-pets.netlify.app/home', // ✅ replace with your actual Netlify URL
    ],

    'allowed_origins_patterns' => [],

    // Allow all headers
    'allowed_headers' => ['*'],

    // No special exposed headers
    'exposed_headers' => [],

    // Cache preflight response (seconds)
    'max_age' => 0,

    // Support cookies/auth tokens if needed
    'supports_credentials' => true,
];
