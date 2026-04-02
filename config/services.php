<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'wechat_bot' => [
        'webhook_url' => env('WECHAT_BOT_WEBHOOK_URL'),
    ],

    'openclaw' => [
        'webhook_token' => env('OPENCLAW_WEBHOOK_TOKEN', 'openclaw-ai-fetcher-2026'),
    ],

    'aliyun' => [
        'sms_access_key_id' => env('ALIYUN_ACCESS_KEY_ID'),
        'sms_access_key_secret' => env('ALIYUN_ACCESS_KEY_SECRET'),
        'sms_sign_name' => env('ALIYUN_SMS_SIGN_NAME', 'AI 副业情报局'),
        'sms_template_code' => env('ALIYUN_SMS_TEMPLATE_CODE'),
    ],
];
