<?php

declare(strict_types=1);

return [
    'username' => env('NETGSM_USERNAME'),
    'password' => env('NETGSM_PASSWORD'),
    'msgheader' => env('NETGSM_MSGHEADER'),
    'appname' => env('NETGSM_APPNAME', env('APP_NAME', 'Laravel')),

    'base_url' => env('NETGSM_BASE_URL', 'https://api.netgsm.com.tr'),

    'endpoints' => [
        'sms_send' => env('NETGSM_SMS_SEND_ENDPOINT', '/sms/rest/v2/send'),
        'otp' => env('NETGSM_OTP_ENDPOINT', '/sms/rest/v2/otp'),
    ],

    'default_encoding' => env('NETGSM_DEFAULT_ENCODING', 'TR'),
    'default_iysfilter' => env('NETGSM_DEFAULT_IYSFILTER', '0'),
    'partnercode' => env('NETGSM_PARTNERCODE'),

    'timeout' => (int) env('NETGSM_TIMEOUT', 30),

    'queue' => env('NETGSM_QUEUE', 'netgsm-sms'),
    'queue_connection' => env('NETGSM_QUEUE_CONNECTION'),
    'queue_tries' => (int) env('NETGSM_QUEUE_TRIES', 3),
    'queue_timeout' => (int) env('NETGSM_QUEUE_TIMEOUT', 60),

    'safe_error_message' => env(
        'NETGSM_SAFE_ERROR_MESSAGE',
        'SMS gonderimi tamamlanamadi. Lutfen daha sonra tekrar deneyin.'
    ),
];
