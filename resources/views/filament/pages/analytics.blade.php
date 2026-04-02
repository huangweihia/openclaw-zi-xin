<x-filament-panels::page>
    <div style="display: grid; gap: 32px;">
        
        <!-- 用户增长 -->
        <x-filament::section>
            <x-slot name="heading">
                📈 用户增长
            </x-slot>
            
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px;">
                <div style="background: #1e293b; padding: 20px; border-radius: 12px;">
                    <div style="color: #94a3b8; font-size: 14px; margin-bottom: 8px;">总用户数</div>
                    <div style="font-size: 32px; font-weight: bold; color: #6366f1;">{{ $this->getStats()['users']['total'] }}</div>
                </div>
                <div style="background: #1e293b; padding: 20px; border-radius: 12px;">
                    <div style="color: #94a3b8; font-size: 14px; margin-bottom: 8px;">今日新增</div>
                    <div style="font-size: 32px; font-weight: bold; color: #10b981;">+{{ $this->getStats()['users']['today'] }}</div>
                </div>
                <div style="background: #1e293b; padding: 20px; border-radius: 12px;">
                    <div style="color: #94a3b8; font-size: 14px; margin-bottom: 8px;">本周新增</div>
                    <div style="font-size: 32px; font-weight: bold; color: #3b82f6;">+{{ $this->getStats()['users']['week'] }}</div>
                </div>
                <div style="background: #1e293b; padding: 20px; border-radius: 12px;">
                    <div style="color: #94a3b8; font-size: 14px; margin-bottom: 8px;">本月新增</div>
                    <div style="font-size: 32px; font-weight: bold; color: #8b5cf6;">+{{ $this->getStats()['users']['month'] }}</div>
                </div>
            </div>
        </x-filament::section>

        <!-- VIP 会员 -->
        <x-filament::section>
            <x-slot name="heading">
                ⭐ VIP 会员
            </x-slot>
            
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px;">
                <div style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); padding: 24px; border-radius: 12px;">
                    <div style="color: rgba(255,255,255,0.9); font-size: 14px; margin-bottom: 8px;">VIP 用户总数</div>
                    <div style="font-size: 36px; font-weight: bold; color: white;">{{ $this->getStats()['vips']['total'] }}</div>
                    <div style="color: rgba(255,255,255,0.8); font-size: 13px; margin-top: 8px;">
                        占比 {{ number_format($this->getStats()['vips']['total'] / max($this->getStats()['users']['total'], 1) * 100, 1) }}%
                    </div>
                </div>
                <div style="background: #1e293b; padding: 24px; border-radius: 12px; border: 2px dashed #ef4444;">
                    <div style="color: #94a3b8; font-size: 14px; margin-bottom: 8px;">即将到期（7 天内）</div>
                    <div style="font-size: 36px; font-weight: bold; color: #ef4444;">{{ $this->getStats()['vips']['expiring_soon'] }}</div>
                    <div style="color: #94a3b8; font-size: 13px; margin-top: 8px;">需要跟进续费</div>
                </div>
            </div>
        </x-filament::section>

        <!-- 项目库 -->
        <x-filament::section>
            <x-slot name="heading">
                📊 项目库
            </x-slot>
            
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px;">
                <div style="background: #1e293b; padding: 20px; border-radius: 12px;">
                    <div style="color: #94a3b8; font-size: 14px; margin-bottom: 8px;">项目总数</div>
                    <div style="font-size: 32px; font-weight: bold; color: #ec4899;">{{ $this->getStats()['projects']['total'] }}</div>
                </div>
                <div style="background: #1e293b; padding: 20px; border-radius: 12px;">
                    <div style="color: #94a3b8; font-size: 14px; margin-bottom: 8px;">本周新增</div>
                    <div style="font-size: 32px; font-weight: bold; color: #14b8a6;">+{{ $this->getStats()['projects']['this_week'] }}</div>
                </div>
            </div>
        </x-filament::section>

        <!-- 邮件发送 -->
        <x-filament::section>
            <x-slot name="heading">
                📧 邮件发送
            </x-slot>
            
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
                <div style="background: #1e293b; padding: 20px; border-radius: 12px;">
                    <div style="color: #94a3b8; font-size: 14px; margin-bottom: 8px;">累计发送</div>
                    <div style="font-size: 32px; font-weight: bold; color: #3b82f6;">{{ $this->getStats()['emails']['total_sent'] }}</div>
                </div>
                <div style="background: #1e293b; padding: 20px; border-radius: 12px;">
                    <div style="color: #94a3b8; font-size: 14px; margin-bottom: 8px;">今日发送</div>
                    <div style="font-size: 32px; font-weight: bold; color: #10b981;">{{ $this->getStats()['emails']['today'] }}</div>
                </div>
                <div style="background: #1e293b; padding: 20px; border-radius: 12px; border: 2px solid #ef4444;">
                    <div style="color: #94a3b8; font-size: 14px; margin-bottom: 8px;">发送失败</div>
                    <div style="font-size: 32px; font-weight: bold; color: #ef4444;">{{ $this->getStats()['emails']['failed'] }}</div>
                </div>
            </div>
        </x-filament::section>

        <!-- 快捷操作 -->
        <x-filament::section>
            <x-slot name="heading">
                ⚡ 快捷操作
            </x-slot>
            
            <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                <a href="{{ route('filament.admin.resources.users.index') }}" class="btn" style="background: #6366f1; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                    <span>👥</span> 管理用户
                </a>
                <a href="{{ route('filament.admin.resources.projects.index') }}" class="btn" style="background: #ec4899; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                    <span>📁</span> 管理项目
                </a>
                <a href="{{ route('filament.admin.resources.email-logs.index') }}" class="btn" style="background: #3b82f6; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                    <span>📧</span> 查看邮件日志
                </a>
                <a href="{{ url('/admin/ai-projects:send-daily') }}" class="btn" style="background: #10b981; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                    <span>🚀</span> 立即发送日报
                </a>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
