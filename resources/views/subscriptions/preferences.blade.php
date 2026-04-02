@extends('layouts.app')

@section('title', '订阅偏好 - AI 副业情报局')

@section('content')
<div class="container" style="max-width: 700px; margin: 60px auto;">
    <div class="card" style="padding: 40px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="font-size: 48px; margin-bottom: 16px;">📧</div>
            <h1 style="font-size: 24px; font-weight: 700; margin-bottom: 8px;">邮件订阅偏好</h1>
            <p style="color: var(--gray-light); font-size: 15px;">
                管理你希望接收的邮件类型（保存时无需刷新整页）
            </p>
        </div>

        <div id="prefs-feedback" style="display: none; margin-bottom: 20px; padding: 12px 16px; border-radius: 12px; font-size: 14px; font-weight: 600;"></div>

        @if(session('success'))
            <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 12px; background: rgba(16,185,129,0.15); color: #6ee7b7;">{{ session('success') }}</div>
        @endif

        <form id="subscription-preferences-form" method="POST" action="{{ route('subscriptions.update') }}">
            @csrf

            <div style="display: grid; gap: 16px; margin-bottom: 30px;">
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px; background: rgba(255,255,255,0.05); border-radius: 12px;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 4px;">
                            <span style="font-size: 24px;">📅</span>
                            <div>
                                <div style="font-weight: 600; font-size: 16px;">每日资讯日报</div>
                                <div style="font-size: 13px; color: var(--gray-light);">每天早上 10:00 发送，包含 AI 项目、副业灵感、学习资源</div>
                            </div>
                        </div>
                    </div>
                    <label style="position: relative; display: inline-block; width: 60px; height: 34px;">
                        <input type="checkbox" name="subscribed_to_daily" value="1" {{ $subscription->subscribed_to_daily ? 'checked' : '' }} style="opacity: 0; width: 0; height: 0;">
                        <span class="toggle-track" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #334155; transition: .4s; border-radius: 34px;">
                            <span class="toggle-thumb" style="position: absolute; height: 26px; width: 26px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                        </span>
                    </label>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px; background: rgba(255,255,255,0.05); border-radius: 12px;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 4px;">
                            <span style="font-size: 24px;">📊</span>
                            <div>
                                <div style="font-weight: 600; font-size: 16px;">每周精选汇总</div>
                                <div style="font-size: 13px; color: var(--gray-light);">每周一发送，汇总上周最优质内容</div>
                            </div>
                        </div>
                    </div>
                    <label style="position: relative; display: inline-block; width: 60px; height: 34px;">
                        <input type="checkbox" name="subscribed_to_weekly" value="1" {{ $subscription->subscribed_to_weekly ? 'checked' : '' }} style="opacity: 0; width: 0; height: 0;">
                        <span class="toggle-track" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #334155; transition: .4s; border-radius: 34px;">
                            <span class="toggle-thumb" style="position: absolute; height: 26px; width: 26px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                        </span>
                    </label>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px; background: rgba(255,255,255,0.05); border-radius: 12px;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 4px;">
                            <span style="font-size: 24px;">🔔</span>
                            <div>
                                <div style="font-weight: 600; font-size: 16px;">系统通知</div>
                                <div style="font-size: 13px; color: var(--gray-light);">关闭后：不再接收此类邮件，且<strong>站内系统通知</strong>也不会生成（点赞/收藏/评论提醒、后台官方通知等）；日报/周报仍由上方两项单独控制</div>
                            </div>
                        </div>
                    </div>
                    <label style="position: relative; display: inline-block; width: 60px; height: 34px;">
                        <input type="checkbox" name="subscribed_to_notifications" value="1" {{ $subscription->subscribed_to_notifications ? 'checked' : '' }} style="opacity: 0; width: 0; height: 0;">
                        <span class="toggle-track" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #334155; transition: .4s; border-radius: 34px;">
                            <span class="toggle-thumb" style="position: absolute; height: 26px; width: 26px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                        </span>
                    </label>
                </div>
            </div>

            <button type="submit" id="prefs-save-btn" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 15px;">
                💾 保存偏好设置
            </button>
        </form>

        <div style="text-align: center; margin-top: 30px; padding-top: 30px; border-top: 1px solid rgba(255,255,255,0.1);">
            <p style="color: var(--gray-light); font-size: 14px; margin-bottom: 16px;">
                订阅邮箱：<strong>{{ $subscription->email }}</strong>
            </p>
            <a href="{{ route('unsubscribe.show', $subscription->unsubscribe_token) }}" style="color: #ef4444; text-decoration: none; font-size: 14px;">
                ❌ 退订所有邮件
            </a>
        </div>
    </div>
</div>

<style>
#subscription-preferences-form input:checked + .toggle-track {
    background-color: #6366f1 !important;
}
#subscription-preferences-form input:checked + .toggle-track .toggle-thumb {
    transform: translateX(26px);
}
#prefs-save-btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}
</style>

<script>
(function () {
    const form = document.getElementById('subscription-preferences-form');
    const btn = document.getElementById('prefs-save-btn');
    const feedback = document.getElementById('prefs-feedback');
    const url = form.getAttribute('action');
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function showFeedback(type, message) {
        feedback.style.display = 'block';
        feedback.textContent = message;
        if (type === 'success') {
            feedback.style.background = 'rgba(16,185,129,0.15)';
            feedback.style.color = '#6ee7b7';
        } else {
            feedback.style.background = 'rgba(239,68,68,0.15)';
            feedback.style.color = '#fca5a5';
        }
    }

    function boolVal(name) {
        const el = form.querySelector('[name="' + name + '"]');
        return !!(el && el.checked);
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!token) {
            showFeedback('error', '缺少 CSRF，请刷新页面重试');
            return;
        }

        btn.disabled = true;
        feedback.style.display = 'none';

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                subscribed_to_daily: boolVal('subscribed_to_daily'),
                subscribed_to_weekly: boolVal('subscribed_to_weekly'),
                subscribed_to_notifications: boolVal('subscribed_to_notifications'),
            }),
        })
            .then(function (res) {
                return res.json().then(function (data) {
                    return { ok: res.ok, status: res.status, data: data };
                });
            })
            .then(function (result) {
                if (result.ok && result.data.success) {
                    showFeedback('success', result.data.message || '已保存');
                } else {
                    const msg = (result.data && result.data.message) ? result.data.message : '保存失败';
                    showFeedback('error', msg);
                }
            })
            .catch(function () {
                showFeedback('error', '网络异常，请稍后重试');
            })
            .finally(function () {
                btn.disabled = false;
            });
    });
})();
</script>
@endsection
