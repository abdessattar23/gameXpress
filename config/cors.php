<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_origins' => ['http://localhost:3000', 'http://127.0.0.1:3001', 'http://127.0.0.1:3000', 'http://localhost:3001'], // Your React app URL
    'allowed_methods' => ['*'],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Critical for authentication cookies
];
