<?php

namespace App\Services;

class AbaPaywayService
{
    protected $merchantId;
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->merchantId = config('payway.merchant_id');
        $this->apiKey = config('payway.api_key');
        $this->apiUrl = config('payway.api_url');
    }

    /**
     * Generate HMAC SHA-512 Hash for ABA PayWay
     *
     * @param string $reqTime (Format: YmdHis)
     * @param string $tranId
     * @param string $amount
     * @param string|array $items
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $phone
     * @param string $returnUrl
     * @param string $continueSuccessUrl
     * @param string $returnDeeplink
     * @return string
     */
    /**
     * Generate HMAC SHA-512 Hash for ABA PayWay
     */
    public function getHash($reqTime, $tranId, $amount, $items = '', $firstName = '', $lastName = '', $email = '', $phone = '', $returnUrl = '', $continueSuccessUrl = '', $returnDeeplink = '')
    {
        if (is_array($items)) {
            $items = base64_encode(json_encode($items));
        }

        $str = $this->prepareHashString([
            $reqTime,
            $this->merchantId,
            $tranId,
            $amount,
            $items,
            $firstName,
            $lastName,
            $email,
            $phone,
            $returnUrl,
            $continueSuccessUrl,
            $returnDeeplink
        ]);
        
        return base64_encode(hash_hmac('sha512', $str, $this->apiKey, true));
    }

    /**
     * Prepare the string for hashing by concatenating non-empty values.
     */
    private function prepareHashString(array $fields): string
    {
        return implode('', array_map(function($val) {
            return (string)$val;
        }, $fields));
    }

    /**
     * Verify Hash from ABA Webhook
     */
    public function verifyHash($hash, $tranId, $apv)
    {
        // For webhook, ABA returns tran_id and apv (amount, payment method, etc)
        // Usually, the signature verification relies on specific string concatenations.
        // The simple confirmation check: tran_id
        $str = $tranId;
        $calculatedHash = base64_encode(hash_hmac('sha512', $str, $this->apiKey, true));

        // Note: Consult the latest ABA API documentation for the exact Webhook signature string.
        // This is a default implementation placeholder.
        return hash_equals($hash, $calculatedHash);
    }
}
