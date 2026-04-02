<?php

namespace App\Services\WechatPay;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;

/**
 * 微信支付 API v3（Native 扫码等），仅依赖 Guzzle + OpenSSL，无需额外 Composer 包。
 *
 * @see https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_4_1.shtml
 */
class WechatPayV3Client
{
    protected const BASE_URI = 'https://api.mch.weixin.qq.com';

    public function __construct(
        protected string $appId,
        protected string $mchId,
        protected string $mchSecretKeyV3,
        protected string $mchSerialNo,
        protected string $mchPrivateKeyPem,
    ) {
    }

    public static function fromConfig(): self
    {
        $keyPath = self::resolveCertPath((string) config('wechat_pay.mch_private_key_path'));
        if (! is_readable($keyPath)) {
            throw new WechatPayException('商户私钥文件不可读：' . $keyPath);
        }

        return new self(
            (string) config('wechat_pay.app_id'),
            (string) config('wechat_pay.mch_id'),
            (string) config('wechat_pay.mch_secret_key'),
            (string) config('wechat_pay.mch_serial_no'),
            (string) file_get_contents($keyPath),
        );
    }

    /**
     * Native 下单，返回 code_url
     *
     * @return array{code_url: string}
     */
    public function nativePay(string $outTradeNo, string $description, int $totalFen, string $notifyUrl): array
    {
        $path = '/v3/pay/transactions/native';
        $body = json_encode([
            'appid' => $this->appId,
            'mchid' => $this->mchId,
            'description' => $description,
            'out_trade_no' => $outTradeNo,
            'notify_url' => $notifyUrl,
            'amount' => [
                'total' => $totalFen,
                'currency' => 'CNY',
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $response = $this->request('POST', $path, $body);
        if (empty($response['code_url'])) {
            throw new WechatPayException('微信返回缺少 code_url：' . json_encode($response, JSON_UNESCAPED_UNICODE));
        }

        return ['code_url' => $response['code_url']];
    }

    /**
     * @return array<string, mixed>
     */
    protected function request(string $method, string $path, string $body = ''): array
    {
        $client = new Client(['base_uri' => self::BASE_URI, 'timeout' => 30]);
        $authorization = $this->buildAuthorization($method, $path, $body);

        try {
            $res = $client->request($method, $path, [
                'headers' => [
                    'Authorization' => $authorization,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'ai-side-laravel-wechatpay-v3',
                ],
                'body' => $body,
                'http_errors' => false,
            ]);
        } catch (GuzzleException $e) {
            throw new WechatPayException('请求微信接口失败：' . $e->getMessage(), 0, $e);
        }

        $json = (string) $res->getBody();
        $status = $res->getStatusCode();
        if ($status >= 400) {
            $err = json_decode($json, true);
            $msg = is_array($err)
                ? (($err['message'] ?? '') . ' (' . ($err['code'] ?? $status) . ')')
                : $json;

            throw new WechatPayException('微信接口错误 HTTP ' . $status . '：' . $msg);
        }
        $data = json_decode($json, true);
        if (! is_array($data)) {
            throw new WechatPayException('微信响应非 JSON：' . $json);
        }

        if (isset($data['code_url'])) {
            return $data;
        }

        if (($data['code'] ?? null) && ($data['code'] !== 'SUCCESS')) {
            throw new WechatPayException(
                ($data['message'] ?? '微信错误') . ' [' . ($data['code'] ?? '') . ']'
            );
        }

        return $data;
    }

    protected function buildAuthorization(string $method, string $urlPath, string $body): string
    {
        $timestamp = (string) time();
        $nonce = Str::random(32);
        $message = $method . "\n" . $urlPath . "\n" . $timestamp . "\n" . $nonce . "\n" . $body . "\n";
        $signature = $this->sign($message);

        return sprintf(
            'WECHATPAY2-SHA256-RSA2048 mchid="%s",nonce_str="%s",signature="%s",timestamp="%s",serial_no="%s"',
            $this->mchId,
            $nonce,
            $signature,
            $timestamp,
            $this->mchSerialNo
        );
    }

    protected function sign(string $message): string
    {
        $key = openssl_pkey_get_private($this->mchPrivateKeyPem);
        if ($key === false) {
            throw new WechatPayException('解析商户私钥失败，请确认 PEM 格式正确');
        }

        $ok = openssl_sign($message, $signature, $key, OPENSSL_ALGO_SHA256);
        if (! $ok) {
            throw new WechatPayException('RSA 签名失败');
        }

        return base64_encode($signature);
    }

    /**
     * 验证支付通知（请求体为 JSON，验签使用平台证书公钥）
     */
    public function verifyNotificationSignature(string $timestamp, string $nonce, string $body, string $signatureB64): bool
    {
        $certPath = self::resolveCertPath((string) config('wechat_pay.platform_cert_path'));
        if ($certPath === '' || ! is_readable($certPath)) {
            throw new WechatPayException('未配置或不可读 platform_cert_path，无法验签回调');
        }

        $pem = (string) file_get_contents($certPath);
        $pub = openssl_pkey_get_public($pem);
        if ($pub === false) {
            throw new WechatPayException('解析微信平台证书失败');
        }

        $message = $timestamp . "\n" . $nonce . "\n" . $body . "\n";
        $sig = base64_decode($signatureB64, true);
        if ($sig === false) {
            return false;
        }

        $ok = openssl_verify($message, $sig, $pub, OPENSSL_ALGO_SHA256);

        return $ok === 1;
    }

    /**
     * 解密通知 resource 中的 ciphertext（AES-256-GCM）
     *
     * @return array<string, mixed>
     */
    public function decryptNotifyResource(array $resource): array
    {
        $ciphertext = $resource['ciphertext'] ?? '';
        $nonce = $resource['nonce'] ?? '';
        $associatedData = $resource['associated_data'] ?? '';

        $aesKey = $this->mchSecretKeyV3;
        if (strlen($aesKey) !== 32) {
            throw new WechatPayException('mch_secret_key 须为 32 位 APIv3 密钥');
        }

        $binary = base64_decode($ciphertext, true);
        if ($binary === false || strlen($binary) < 16) {
            throw new WechatPayException('通知 ciphertext 无效');
        }

        $tag = substr($binary, -16);
        $ctext = substr($binary, 0, -16);

        $plain = openssl_decrypt(
            $ctext,
            'aes-256-gcm',
            $aesKey,
            OPENSSL_RAW_DATA,
            $nonce,
            $tag,
            $associatedData
        );
        if ($plain === false) {
            throw new WechatPayException('解密通知失败');
        }

        $data = json_decode($plain, true);
        if (! is_array($data)) {
            throw new WechatPayException('解密后非 JSON');
        }

        return $data;
    }

    public static function yuanToFen(float $yuan): int
    {
        return (int) round($yuan * 100);
    }

    /**
     * 将 .env 中的相对路径（如 storage/certs/...）解析为项目根目录下的绝对路径。
     */
    protected static function resolveCertPath(string $path): string
    {
        $path = trim($path);
        if ($path === '') {
            return '';
        }
        if (self::isAbsolutePath($path)) {
            return $path;
        }

        return base_path(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path));
    }

    protected static function isAbsolutePath(string $path): bool
    {
        if (str_starts_with($path, '/') || str_starts_with($path, '\\')) {
            return true;
        }

        return strlen($path) > 2 && ctype_alpha($path[0]) && $path[1] === ':';
    }
}
