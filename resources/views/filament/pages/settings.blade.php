<x-filament-panels::page>
    <style>
        .settings-primary-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 22px;
            border-radius: 10px;
            border: none;
            font-weight: 700;
            cursor: pointer;
            color: #fff !important;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.45);
        }
        .settings-primary-btn:hover {
            filter: brightness(1.08);
            box-shadow: 0 6px 18px rgba(99, 102, 241, 0.55);
        }
        .settings-field {
            width: 100%;
            max-width: 480px;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid rgba(148, 163, 184, 0.35);
            background: rgba(51, 65, 85, 0.65);
            color: #f1f5f9;
        }
        .settings-field:focus {
            outline: 2px solid #818cf8;
            outline-offset: 1px;
        }
        .settings-select-wrap {
            max-width: 480px;
        }
        .settings-select-wrap select.settings-field {
            max-width: none;
            display: block;
        }
    </style>
    <div style="display: grid; gap: 24px;">
        @php
            $registerVipEnabled = \App\Models\Setting::getValue('register_default_vip_enabled', false);
            $registerVipDays = (int) (\App\Models\Setting::getValue('register_default_vip_days', 7) ?? 7);
            $registerVipDays = max(0, $registerVipDays);
            $emailSendTime = \App\Models\EmailSetting::get('email_send_time', '10:00');
            $digestTplKey = \App\Models\EmailSetting::getDigestTemplateKey();
            $weeklyTplKey = \App\Models\EmailSetting::getWeeklyTemplateKey();
            $emailTemplates = \App\Models\EmailTemplate::query()->where('is_active', true)->orderBy('name')->get(['key', 'name']);
        @endphp

        <!-- 邮件设置 -->
        <x-filament::section>
            <x-slot name="heading">
                📧 邮件配置
            </x-slot>
            <x-slot name="description">
                配置 QQ 邮箱 SMTP 发送设置
            </x-slot>
            
            <div style="display: grid; gap: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">SMTP 服务器</label>
                    <code style="background: #1e293b; padding: 12px; border-radius: 8px; display: block;">smtp.qq.com:465 (SSL)</code>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">发件邮箱</label>
                    <code style="background: #1e293b; padding: 12px; border-radius: 8px; display: block;">2801359160@qq.com</code>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">状态</label>
                    <span style="color: #10b981;">✅ 配置正常</span>
                </div>
            </div>
        </x-filament::section>

        <!-- 定时任务设置（与 schedule:run + emails:send-scheduled 一致） -->
        <x-filament::section>
            <x-slot name="heading">
                ⏰ 定时推送（订阅邮件）
            </x-slot>
            <x-slot name="description">
                每日按下方时间在「北京时间」触发一次投递；非周一发日报模板，周一发周报模板。收件人来自「邮件管理」中的订阅用户。
            </x-slot>

            @if(session('success'))
                <p style="color: #34d399; margin-bottom: 8px;">{{ session('success') }}</p>
            @endif
            @if(isset($errors) && $errors->any())
                <p style="color: #f87171; margin-bottom: 8px;">{{ $errors->first() }}</p>
            @endif

            @if($emailTemplates->isEmpty())
                <p style="color: #fbbf24;">请先在「邮件模板」中创建并启用至少一个模板后再配置。</p>
            @else
            <form method="POST" action="{{ url('/admin/settings/email-schedule') }}" style="display: grid; gap: 16px;">
                @csrf
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">每日推送时间</label>
                    <input type="time"
                           name="email_send_time"
                           value="{{ old('email_send_time', $emailSendTime) }}"
                           required
                           class="settings-field"
                           style="max-width: 200px;">
                    <p style="color: #94a3b8; font-size: 13px; margin-top: 6px;">时区固定为 Asia/Shanghai，与 Docker 中每分钟执行的 <code>php artisan schedule:run</code> 配合。</p>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">日报模板</label>
                    <div class="settings-select-wrap">
                        <select name="email_digest_template_key" required class="settings-field" size="1" style="max-height: none;">
                        @foreach($emailTemplates as $t)
                            <option value="{{ $t->key }}" @selected(old('email_digest_template_key', $digestTplKey) === $t->key)>{{ $t->name }}（{{ $t->key }}）</option>
                        @endforeach
                        </select>
                    </div>
                    <p style="color: #94a3b8; font-size: 13px; margin-top: 6px;">周二至周日及周一「单邮箱测试」时使用的模板 key。</p>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">周报模板（仅周一）</label>
                    <p style="color: #94a3b8; font-size: 12px; margin: 0 0 6px;">下方为可滚动列表，直接点选一行即可（选项多时不占满屏）。</p>
                    <div class="settings-select-wrap" style="max-height: 220px; overflow-y: auto; padding-right: 4px; border-radius: 10px; border: 1px solid rgba(148, 163, 184, 0.25); background: rgba(51, 65, 85, 0.45);">
                        <select name="email_weekly_template_key" required class="settings-field" size="{{ min(12, max(4, $emailTemplates->count())) }}" style="max-width: none; width: 100%; border: none; background: transparent;">
                        @foreach($emailTemplates as $t)
                            <option value="{{ $t->key }}" @selected(old('email_weekly_template_key', $weeklyTplKey) === $t->key)>{{ $t->name }}（{{ $t->key }}）</option>
                        @endforeach
                        </select>
                    </div>
                    <p style="color: #94a3b8; font-size: 13px; margin-top: 6px;">每周一全量发送时使用的模板（需含 <code>week_range</code>、<code>top_projects</code> 等变量）。</p>
                </div>
                <button type="submit" class="settings-primary-btn">
                    保存推送配置
                </button>
            </form>
            @endif
        </x-filament::section>

        <!-- 注册赠送 VIP 设置 -->
        <x-filament::section>
            <x-slot name="heading">
                👑 注册赠送 VIP 设置
            </x-slot>
            <x-slot name="description">
                开启后，用户注册成功将自动设置为 VIP，并按天数赠送有效期。
            </x-slot>

            <form method="POST" action="{{ route('admin.settings.register-vip') }}" style="margin-top: 12px;">
                @csrf
                <div style="display: grid; gap: 12px;">
                    <label style="display:flex; align-items:center; gap:10px; font-weight: 700;">
                        <input type="checkbox" name="enabled" value="1" {{ $registerVipEnabled ? 'checked' : '' }}>
                        启用：注册后自动赠送 VIP
                    </label>

                    <div style="display:flex; gap: 12px; align-items:center;">
                        <label style="min-width: 120px; color: rgba(148,163,184,1); font-size: 13px; font-weight: 700;">赠送天数</label>
                        <input type="number"
                               name="days"
                               value="{{ $registerVipDays }}"
                               min="0"
                               max="3650"
                               step="1"
                               class="settings-field">
                    </div>

                    <button type="submit" class="settings-primary-btn">
                        保存设置
                    </button>
                </div>
            </form>
        </x-filament::section>

        <!-- 系统信息 -->
        <x-filament::section>
            <x-slot name="heading">
                ℹ️ 系统信息
            </x-slot>
            <x-slot name="description">
                当前系统配置和版本
            </x-slot>
            
            <div style="display: grid; gap: 12px;">
                <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <span style="color: #94a3b8;">Laravel 版本</span>
                    <span>{{ app()->version() }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <span style="color: #94a3b8;">PHP 版本</span>
                    <span>{{ phpversion() }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <span style="color: #94a3b8;">Filament 版本</span>
                    <span>v3.x</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                    <span style="color: #94a3b8;">数据库</span>
                    <span>MySQL 8.0</span>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
