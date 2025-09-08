<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Wikidata service configuration
    'wikidata' => [
        'base'   => env('WIKIDATA_BASE', 'https://www.wikidata.org'),
        'sparql' => env('WIKIDATA_SPARQL', 'https://query.wikidata.org/sparql'),
        'ua'     => env('WIKIDATA_USER_AGENT', 'IndexApp/0.1 (contact@example.com)'),
        'ttl'    => (int) env('WIKIDATA_CACHE_TTL', 43200), // 12h
    ],

];
