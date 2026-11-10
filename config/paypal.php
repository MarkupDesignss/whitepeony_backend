<?php

return [
    'client_id' => env('PAYPAL_CLIENT_ID', 'Aeqoi5aZwS6rGCClrQRLbdo0h4PBpADRx4eFa9dKJ-Z77n7LIL2U4AgOkeOP2ZBTXXeKoaSZUH65flFx'),
    'secret' => env('PAYPAL_SECRET', 'ED2we-Lnq5OCHlSe-5piLBYz_aTExcRCjzHBOu5voVklJ6ZSoCURVrfS0KDNWmSIC0Vwmfsv9p4ru_PD'),
    'settings' => [
        'mode' => env('PAYPAL_MODE', 'sandbox'), // sandbox or live
        'http.ConnectionTimeOut' => 1000,
        'log.LogEnabled' => true,
        'log.FileName' => storage_path('logs/paypal.log'),
        'log.LogLevel' => 'FINE'
    ]
];