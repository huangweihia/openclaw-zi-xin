<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * The headers should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;

    /**
     * 置于 Nginx / CDN 后时需信任转发头，否则 APP_URL 为 https 时 Livewire 上传等请求的 URL / 协议可能不一致。
     * .env：TRUSTED_PROXIES=*（默认）或逗号分隔 IP；留空则不信任任何代理。
     */
    protected function proxies()
    {
        $v = env('TRUSTED_PROXIES', '*');
        if ($v === null || $v === '') {
            return null;
        }
        if ($v === '*') {
            return '*';
        }

        return array_values(array_filter(array_map('trim', explode(',', (string) $v))));
    }
}