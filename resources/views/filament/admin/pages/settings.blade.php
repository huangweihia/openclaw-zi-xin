<x-filament-panels::page>
    @php
        $enabled = \App\Models\Setting::getValue('register_default_vip_enabled', false);
        $days = (int) (\App\Models\Setting::getValue('register_default_vip_days', 7) ?? 7);
        $days = max(0, $days);
    @endphp

    <div style="display: grid; gap: 16px; max-width: 720px;">
        <div style="padding: 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.12); background: rgba(255,255,255,0.03);">
            <div style="font-weight: 800; margin-bottom: 8px;">注册赠送 VIP 设置</div>
            <div style="color: rgba(148,163,184,1); font-size: 13px; margin-bottom: 14px;">
                开启后，新注册用户将自动成为 VIP，并按配置赠送对应天数。
                （终身/永久 VIP 仍由支付逻辑决定。）
            </div>

            @if(session('success'))
                <div style="margin-bottom: 12px; padding: 10px 12px; border-radius: 10px; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.35); color: #6ee7b7;">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.settings.register-vip') }}">
                @csrf
                <div style="display: grid; gap: 10px;">
                    <label style="display:flex; align-items:center; gap:10px;">
                        <input type="checkbox" name="enabled" value="1" {{ $enabled ? 'checked' : '' }}>
                        <span style="font-weight: 700;">启用：注册后自动赠送 VIP</span>
                    </label>

                    <div style="display:flex; gap: 12px; align-items:center;">
                        <label style="min-width: 120px; color: rgba(148,163,184,1); font-size: 13px;">赠送天数</label>
                        <input type="number"
                               name="days"
                               value="{{ $days }}"
                               min="0"
                               max="3650"
                               step="1"
                               style="flex:1; padding: 10px 12px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.12); background: rgba(15,23,42,0.4); color: inherit;">
                    </div>

                    <button type="submit"
                            class="fi-button fi-button-primary"
                            style="justify-content:center; padding: 10px 16px; border-radius: 10px; font-weight: 800; cursor:pointer;">
                        保存设置
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-filament-panels::page>
