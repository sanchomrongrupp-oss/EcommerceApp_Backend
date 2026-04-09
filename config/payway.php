<?php

return [
    'merchant_id' => env('ABA_PAYWAY_MERCHANT_ID', ''),
    'api_key' => env('ABA_PAYWAY_API_KEY', ''),
    'api_url' => env('ABA_PAYWAY_API_URL', 'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/checkout'),
];
