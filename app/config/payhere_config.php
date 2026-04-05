<?php
/**
 * PayHere sandbox configuration for CMA.
 * Keep only sandbox values in development.
 */

define('PAYHERE_ENABLED', true);
define('PAYHERE_MODE', 'sandbox');
define('PAYHERE_MERCHANT_ID', '1234933');
define('PAYHERE_MERCHANT_SECRET', 'Mjk5ODI0NDMyODczODg5MjUwMzE5MTgyODgxMDEzNTYwODc5MTY=');
define('PAYHERE_API_URL', 'https://sandbox.payhere.lk/pay/checkout');
define('PAYHERE_CURRENCY', 'LKR');

define('PAYHERE_RETURN_URL', URLROOT . '/paymentGateway/return');
define('PAYHERE_CANCEL_URL', URLROOT . '/paymentGateway/cancel');
define('PAYHERE_NOTIFY_URL', URLROOT . '/paymentGateway/notify');
