<?php

return [
    'server' => env('MAILCHIMP_SERVER', ''),
    'apiKey' => env('MAILCHIMP_API_KEY', ''),
    'servers' => env('MAILCHIMP_SERVERS', ''),
    'apiKeys' => env('MAILCHIMP_API_KEYS', ''),
    'storeSites' => env('MAILCHIMP_API_STORE_SITES', ''),
    'storeIds' => env('MAILCHIMP_API_STORE_IDS', ''),
    'redisDbs' => env('MAILCHIMP_API_REDIS_DBS', '')
];
