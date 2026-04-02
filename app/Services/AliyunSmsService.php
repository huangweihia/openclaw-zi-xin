<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AliyunSmsService
{
    protected string $accessKeyId;
    protected string $accessKeySecret;
    protected string $signName;
    protected string $templateCode;

    public function __construct()
    {
        $this->accessKeyId = config('services.aliyun.sms_access_key_id');
        $this->accessKeySecret = config('services.aliyun.sms_access_key_secret');
        $this->signName = config('services.aliyun.sms_sign_name', 'AI 副业情报局');
        $this->templateCode = config('services.aliyun.sms_template_code');
    }

    /**
     * 发送验证码短信
     */
    public function sendVerificationCode(string $phone, string $code): bool
    {
        return $this->send($phone, ['code' => $code]);
    }

    /**
     * 发送普通短信
     */
    public function send(string $phone, array $templateParam): bool
    {
        try {
            $timestamp = now()->format('Ymd\\THis\\Z');
            
            // 生成签名
            $signMethod = 'HMAC-SHA1';
            $stringToSign = $this->buildStringToSign($timestamp);
            $signature = base64_encode(hash_hmac($signMethod, $stringToSign, $this->accessKeySecret . '&', true));
            
            // 请求参数
            $params = [
                'AccessKeyId' => $this->accessKeyId,
                'Action' => 'SendSms',
                'Format' => 'JSON',
                'PhoneNumbers' => $phone,
                'RegionId' => 'cn-hangzhou',
                'SignName' => $this->signName,
                'Signature' => $signature,
                'SignatureMethod' => $signMethod,
                'SignatureNonce' => uniqid(),
                'SignatureVersion' => '1.0',
                'TemplateCode' => $this->templateCode,
                'TemplateParam' => json_encode($templateParam),
                'Timestamp' => $timestamp,
                'Version' => '2017-05-25',
            ];
            
            $response = Http::withOptions([
                'verify' => false,
            ])->get('https://dysmsapi.aliyuncs.com/', $params);
            
            $result = $response->json();
            
            if (($result['Code'] ?? '') === 'OK') {
                Log::info('✅ 阿里云短信发送成功', [
                    'phone' => $phone,
                    'bizId' => $result['BizId'] ?? '',
                ]);
                return true;
            }
            
            Log::error('❌ 阿里云短信发送失败', [
                'phone' => $phone,
                'code' => $result['Code'] ?? '',
                'message' => $result['Message'] ?? '',
            ]);
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('❌ 阿里云短信发送异常', [
                'phone' => $phone,
                'message' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * 构建签名字符串
     */
    protected function buildStringToSign(string $timestamp): string
    {
        $params = [
            'AccessKeyId' => $this->accessKeyId,
            'Action' => 'SendSms',
            'Format' => 'JSON',
            'PhoneNumbers' => '',
            'RegionId' => 'cn-hangzhou',
            'SignName' => $this->signName,
            'SignatureMethod' => 'HMAC-SHA1',
            'SignatureNonce' => uniqid(),
            'SignatureVersion' => '1.0',
            'TemplateCode' => $this->templateCode,
            'TemplateParam' => '',
            'Timestamp' => $timestamp,
            'Version' => '2017-05-25',
        ];
        
        ksort($params);
        
        $canonicalizedQueryString = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        
        return 'GET&%2F&' . rawurlencode($canonicalizedQueryString);
    }
}
