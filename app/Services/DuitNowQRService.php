<?php

namespace App\Services;

use Illuminate\Support\Str;
use Carbon\Carbon;

class DuitNowQRService
{
    protected string $merchantId;
    protected string $secretKey;
    protected bool $isProduction;

    public function __construct()
    {
        $this->merchantId = config('services.duitnow.merchant_id', '');
        $this->secretKey = config('services.duitnow.secret_key', '');
        $this->isProduction = config('services.duitnow.production', false);
    }

    public function generateDynamicQR(float $amount, string $merchantOrderId, ?string $customerName = null): array
    {
        $expiryMinutes = config('services.duitnow.expiry_minutes', 60);
        $expiryTime = Carbon::now()->addMinutes($expiryMinutes)->format('Y-m-d H:i:s');

        $payload = [
            'merchantId' => $this->merchantId,
            'merchantOrderId' => $merchantOrderId,
            'amount' => number_format($amount, 2, '.', ''),
            'currency' => 'MYR',
            'expiryTime' => $expiryTime,
            'type' => 'DYNAMIC_QR',
        ];

        if ($customerName) {
            $payload['customerName'] = $customerName;
        }

        $signature = $this->generateSignature($payload);
        $payload['signature'] = $signature;

        return [
            'qr_string' => $this->buildQRString($payload),
            'qr_url' => $this->buildQRUrl($payload),
            'expiry_time' => $expiryTime,
            'merchant_order_id' => $merchantOrderId,
            'amount' => $amount,
        ];
    }

    public function generateStaticQR(float $amount): array
    {
        $payload = [
            'merchantId' => $this->merchantId,
            'amount' => number_format($amount, 2, '.', ''),
            'currency' => 'MYR',
            'type' => 'STATIC_QR',
        ];

        $signature = $this->generateSignature($payload);
        $payload['signature'] = $signature;

        return [
            'qr_string' => $this->buildQRString($payload),
            'qr_url' => $this->buildQRUrl($payload),
            'merchant_id' => $this->merchantId,
            'amount' => $amount,
        ];
    }

    public function verifyPayment(string $merchantOrderId, float $amount): array
    {
        $signature = $this->generateVerifySignature($merchantOrderId, $amount);

        return [
            'merchant_order_id' => $merchantOrderId,
            'amount' => $amount,
            'signature' => $signature,
            'status' => 'pending_verification',
        ];
    }

    protected function generateSignature(array $payload): string
    {
        $data = implode('|', [
            $payload['merchantId'] ?? '',
            $payload['merchantOrderId'] ?? '',
            $payload['amount'] ?? '',
            $payload['currency'] ?? '',
            $payload['expiryTime'] ?? '',
        ]);

        return hash_hmac('sha256', $data, $this->secretKey);
    }

    protected function generateVerifySignature(string $orderId, float $amount): string
    {
        $data = implode('|', [
            $this->merchantId,
            $orderId,
            number_format($amount, 2, '.', ''),
        ]);

        return hash_hmac('sha256', $data, $this->secretKey);
    }

    protected function buildQRString(array $payload): string
    {
        $baseUrl = $this->isProduction 
            ? 'https://duitnow.compay.com.my' 
            : 'https://sandbox.duitnow.compay.com.my';

        return $baseUrl . '?' . http_build_query($payload);
    }

    protected function buildQRUrl(array $payload): string
    {
        return $this->buildQRString($payload);
    }

    public function isConfigured(): bool
    {
        return !empty($this->merchantId) && !empty($this->secretKey);
    }

    public static function generateMerchantOrderId(): string
    {
        return 'ORD-' . strtoupper(Str::random(12)) . '-' . Carbon::now()->format('His');
    }
}
