<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Chatbot UI Settings
    |--------------------------------------------------------------------------
    |
    | These values control the sticky chatbot widget that appears across the
    | GroceMate admin panel. The widget itself lives in a Blade partial, but
    | keeping the defaults here makes the behaviour easy to tune later.
    |
    */
    'ui' => [
        // Browser storage key used to keep the local chat history on refresh.
        'storage_key' => env('ADMIN_CHATBOT_STORAGE_KEY', 'grocemate_admin_chatbot_messages'),

        // Suggested prompt buttons shown when the panel first opens.
        'starter_prompts' => [
            'Which products are low stock?',
            'Show customer due summary',
            'Show supplier due summary',
            'Which business has the highest sales?',
            'Show products expiring in 30 days',
            'Calculate 2450 + 18%',
            'Give me a business report',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Vanna Sidecar Settings
    |--------------------------------------------------------------------------
    |
    | Laravel does not run Vanna directly. Instead, Laravel forwards broader
    | questions to a small Python service when it is enabled. The Laravel app
    | still handles a few "safe" business helpers locally, such as low stock,
    | expiry lookups, and calculator-style questions.
    |
    */
    'vanna' => [
        'enabled' => (bool) env('ADMIN_CHATBOT_VANNA_ENABLED', false),
        'base_url' => env('ADMIN_CHATBOT_VANNA_URL', 'http://127.0.0.1:8001'),
        'timeout_seconds' => (int) env('ADMIN_CHATBOT_VANNA_TIMEOUT', 20),
        'chat_endpoint' => env('ADMIN_CHATBOT_VANNA_CHAT_ENDPOINT', '/api/chat'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Result Limits
    |--------------------------------------------------------------------------
    |
    | These limits keep the widget readable. The chatbot can always be expanded
    | later, but short default answers make the first version friendlier.
    |
    */
    'limits' => [
        'low_stock_rows' => (int) env('ADMIN_CHATBOT_LOW_STOCK_ROWS', 10),
        'expiry_rows' => (int) env('ADMIN_CHATBOT_EXPIRY_ROWS', 10),
        'customer_due_rows' => (int) env('ADMIN_CHATBOT_CUSTOMER_DUE_ROWS', 10),
        'supplier_due_rows' => (int) env('ADMIN_CHATBOT_SUPPLIER_DUE_ROWS', 10),
        'demand_rows' => (int) env('ADMIN_CHATBOT_DEMAND_ROWS', 10),
        'business_rows' => (int) env('ADMIN_CHATBOT_BUSINESS_ROWS', 5),
    ],
];
