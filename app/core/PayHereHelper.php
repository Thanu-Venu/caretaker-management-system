<?php

class PayHereHelper
{
    public static function isConfigured(): bool
    {
        return defined('PAYHERE_ENABLED')
            && PAYHERE_ENABLED === true
            && defined('PAYHERE_MERCHANT_ID')
            && defined('PAYHERE_MERCHANT_SECRET')
            && defined('PAYHERE_API_URL');
    }

    public static function formatAmount($amount): string
    {
        return number_format((float)$amount, 2, '.', '');
    }

    public static function buildCheckoutHash(string $merchantId, string $orderId, string $amount, string $currency, string $merchantSecret): string
    {
        $secretHash = strtoupper(md5($merchantSecret));
        return strtoupper(md5($merchantId . $orderId . $amount . $currency . $secretHash));
    }

    public static function verifyNotifySignature(array $payload, string $merchantSecret): bool
    {
        $merchantId = (string)($payload['merchant_id'] ?? '');
        $orderId = (string)($payload['order_id'] ?? '');
        $payhereAmount = (string)($payload['payhere_amount'] ?? '');
        $payhereCurrency = (string)($payload['payhere_currency'] ?? '');
        $statusCode = (string)($payload['status_code'] ?? '');
        $md5Sig = strtoupper((string)($payload['md5sig'] ?? ''));

        if ($merchantId === '' || $orderId === '' || $payhereAmount === '' || $payhereCurrency === '' || $statusCode === '' || $md5Sig === '') {
            return false;
        }

        $secretHash = strtoupper(md5($merchantSecret));
        $localSig = strtoupper(md5($merchantId . $orderId . $payhereAmount . $payhereCurrency . $statusCode . $secretHash));

        return hash_equals($localSig, $md5Sig);
    }
}
