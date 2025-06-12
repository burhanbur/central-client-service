<?php 

return [
    'auth_url' => env('CENTRAL_AUTH_URL', 'https://central.universitaspertamina.ac.id'),
    'api_url' => env('CENTRAL_API_URL', 'https://centralapi.universitaspertamina.ac.id'),
    'client_app_id' => env('CENTRAL_APP_ID', null),
    'cookie_name' => env('CENTRAL_COOKIE_NAME', 'central_access_token'),
];