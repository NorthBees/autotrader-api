<?php

return [
    /**
     * Use either sandbox or live
     */
    'environment' => env('AUTOTRADER_ENVIRONMENT', 'sandbox'),

    /**
     * Authentication details
     */
    'key' => env('AUTOTRADER_KEY', ''),
    'secret' => env('AUTOTRADER_SECRET', ''),

    /**
     * Use either sync to dispatch immediately, or queue to push to a queue
     */
    'dispatch_type' => env('AUTOTRADER_DISPATCH_TYPE', 'sync'),

    /**
     * If dispatch_type is set to a queue, you can specify a queue here
     */
    'queue' => env('AUTOTRADER_QUEUE', 'autotrader'),
];
