<?php
return [    
    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN',''),
        'chat_id' => env('TELEGRAM_CHAT_ID',''),
    ],
    'morpromt' => [
        'secretkey' => env('MORPROMT_SECRET',''),
        'username' => env('MORPROMT_USERNAME',''),
        'password' => env('MORPROMT_PASSWORD',''),
        'hoscode' => env('MORPROMT_HOS_CODE','')    
    ]
];