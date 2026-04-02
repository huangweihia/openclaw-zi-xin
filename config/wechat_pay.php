<?php

return [

    /*
    |--------------------------------------------------------------------------
    | 总开关
    |--------------------------------------------------------------------------
    */
    'enabled' => env('WECHAT_PAY_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | 商户与 App（微信商户平台 / 开放平台）
    |--------------------------------------------------------------------------
    */
    'app_id' => env('WECHAT_PAY_APP_ID', ''),

    'mch_id' => env('WECHAT_PAY_MCH_ID', ''),

    /*
    | API v3 密钥（32 字节，商户平台「API 安全」中设置）
    */
    'mch_secret_key' => env('WECHAT_PAY_MCH_SECRET_V3_KEY', ''),

    /*
    | 商户 API 证书序列号（与 apiclient_key.pem 对应的那把）
    */
    'mch_serial_no' => env('WECHAT_PAY_MCH_SERIAL_NO', ''),

    /*
    | 商户 API 私钥 PEM 文件路径（apiclient_key.pem）
    */
    'mch_private_key_path' => env('WECHAT_PAY_MCH_PRIVATE_KEY_PATH', storage_path('certs/wechat/apiclient_key.pem')),

    /*
    | 微信支付平台证书 PEM（用于验证回调签名，从商户平台下载或接口拉取）
    */
    'platform_cert_path' => env('WECHAT_PAY_PLATFORM_CERT_PATH', storage_path('certs/wechat/wechatpay_platform.pem')),

    /*
    | 支付结果通知完整 URL（须公网 HTTPS，与商户平台配置一致）
    | 示例：https://你的域名/payments/wechat/notify
    */
    'notify_url' => env('WECHAT_PAY_NOTIFY_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | 会员套餐（单位：元，代码内会转为分）
    |--------------------------------------------------------------------------
    */
    'plans' => [
        'monthly' => [
            'label' => '月度会员',
            'amount_yuan' => (float) env('WECHAT_PAY_PLAN_MONTHLY_YUAN', 9.9),
            'original_amount_yuan' => (float) env('WECHAT_PAY_PLAN_MONTHLY_ORIGINAL_YUAN', 29),
        ],
        'yearly' => [
            'label' => '年度会员',
            'amount_yuan' => (float) env('WECHAT_PAY_PLAN_YEARLY_YUAN', 88),
            'original_amount_yuan' => (float) env('WECHAT_PAY_PLAN_YEARLY_ORIGINAL_YUAN', 288),
        ],
        'lifetime' => [
            'label' => '终身会员',
            'amount_yuan' => (float) env('WECHAT_PAY_PLAN_LIFETIME_YUAN', 388),
            'original_amount_yuan' => (float) env('WECHAT_PAY_PLAN_LIFETIME_ORIGINAL_YUAN', 888),
        ],
    ],

];
