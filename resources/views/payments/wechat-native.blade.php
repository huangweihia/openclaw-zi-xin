@extends('layouts.app')

@section('title', '微信扫码支付 - VIP')

@section('content')
<div class="container" style="max-width: 480px; margin: 40px auto; padding: 0 20px; text-align: center;">
    <div class="card" style="padding: 32px;">
        <h1 style="font-size: 20px; font-weight: 800; margin: 0 0 8px;">微信扫码支付</h1>
        <p style="color: var(--gray-light); font-size: 14px; margin-bottom: 8px;">订单号 {{ $order->order_no }}</p>
        <p style="color: var(--gray-light); font-size: 14px; margin-bottom: 24px;">支付 <strong style="color:#fbbf24;">¥{{ number_format($order->amount, 2) }}</strong></p>

        <div style="display: inline-block; padding: 16px; background: #fff; border-radius: 16px; margin-bottom: 20px;">
            <img
                src="https://api.qrserver.com/v1/create-qr-code/?size=240x240&data={{ urlencode($codeUrl) }}"
                alt="微信支付二维码"
                width="240"
                height="240"
                style="display: block;"
            />
        </div>

        <p style="color: var(--gray-light); font-size: 13px; line-height: 1.6; margin-bottom: 20px;">
            请使用微信「扫一扫」完成支付。支付成功后页面将自动跳转（也可手动刷新）。
        </p>

        <p id="pay-status" style="font-size: 13px; color: var(--primary-light); min-height: 20px;"></p>

        <a href="{{ route('dashboard') }}" style="display: inline-block; margin-top: 12px; color: var(--gray-light); font-size: 13px;">稍后在「个人中心」查看会员状态</a>
    </div>
</div>

<script>
(function () {
    const statusUrl = @json(route('payments.wechat.status', ['orderNo' => $order->order_no]));
    const statusEl = document.getElementById('pay-status');
    let stopped = false;

    function poll() {
        if (stopped) return;
        fetch(statusUrl, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.paid && data.redirect_url) {
                    stopped = true;
                    statusEl.textContent = '支付成功，正在跳转…';
                    window.location.href = data.redirect_url;
                    return;
                }
            })
            .catch(function () { /* 忽略单次失败 */ });
    }

    setInterval(poll, 2500);
    poll();
})();
</script>
@endsection
