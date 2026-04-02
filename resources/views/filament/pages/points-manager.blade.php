<x-filament-panels::page>
    <div style="display: grid; gap: 24px;">
        
        {{-- 积分统计 --}}
        <x-filament::section>
            <x-slot name="heading">
                📊 积分统计
            </x-slot>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div style="padding: 20px; background: rgba(16, 185, 129, 0.1); border-radius: 12px; border: 1px solid rgba(16, 185, 129, 0.3);">
                    <div style="color: #10b981; font-size: 14px; margin-bottom: 8px;">💰 累计发放积分</div>
                    <div style="color: white; font-size: 28px; font-weight: 700;">
                        {{ number_format($this->stats['total_earned']) }}
                    </div>
                </div>
                
                <div style="padding: 20px; background: rgba(239, 68, 68, 0.1); border-radius: 12px; border: 1px solid rgba(239, 68, 68, 0.3);">
                    <div style="color: #ef4444; font-size: 14px; margin-bottom: 8px;">🔥 累计消耗积分</div>
                    <div style="color: white; font-size: 28px; font-weight: 700;">
                        {{ number_format($this->stats['total_spent']) }}
                    </div>
                </div>
                
                <div style="padding: 20px; background: rgba(234, 179, 8, 0.1); border-radius: 12px; border: 1px solid rgba(234, 179, 8, 0.3);">
                    <div style="color: #eab308; font-size: 14px; margin-bottom: 8px;">👥 有积分用户数</div>
                    <div style="color: white; font-size: 28px; font-weight: 700;">
                        {{ number_format($this->stats['users_with_points']) }}
                    </div>
                </div>
                
                <div style="padding: 20px; background: rgba(99, 102, 241, 0.1); border-radius: 12px; border: 1px solid rgba(99, 102, 241, 0.3);">
                    <div style="color: #6366f1; font-size: 14px; margin-bottom: 8px;">📈 今日新增流水</div>
                    <div style="color: white; font-size: 28px; font-weight: 700;">
                        {{ number_format($this->stats['today_transactions']) }}
                    </div>
                </div>
            </div>
        </x-filament::section>

        {{-- 积分流水 --}}
        <x-filament::section>
            <x-slot name="heading">
                📝 积分流水记录
            </x-slot>
            <x-slot name="description">
                查看所有用户的积分获得和消耗记录
            </x-slot>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <th style="text-align: left; padding: 12px; color: #94a3b8; font-size: 13px;">用户</th>
                            <th style="text-align: left; padding: 12px; color: #94a3b8; font-size: 13px;">类型</th>
                            <th style="text-align: left; padding: 12px; color: #94a3b8; font-size: 13px;">积分</th>
                            <th style="text-align: left; padding: 12px; color: #94a3b8; font-size: 13px;">说明</th>
                            <th style="text-align: left; padding: 12px; color: #94a3b8; font-size: 13px;">时间</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->transactions as $transaction)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td style="padding: 12px; color: white;">{{ $transaction->user?->name ?? '未知' }}</td>
                                <td style="padding: 12px;">
                                    <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; background: rgba(99, 102, 241, 0.2); color: #818cf8;">
                                        {{ match($transaction->type) {
                                            'sign' => '签到',
                                            'share' => '分享',
                                            'like' => '点赞',
                                            'favorite' => '收藏',
                                            'comment' => '评论',
                                            'unlock' => '解锁',
                                            'admin_gift' => '赠送',
                                            'vip' => 'VIP',
                                            default => $transaction->type
                                        } }}
                                    </span>
                                </td>
                                <td style="padding: 12px; color: {{ $transaction->amount > 0 ? '#10b981' : '#ef4444' }}; font-weight: 600;">
                                    {{ $transaction->amount > 0 ? '+' : '' }}{{ $transaction->amount }}
                                </td>
                                <td style="padding: 12px; color: #94a3b8;">{{ $transaction->description }}</td>
                                <td style="padding: 12px; color: #94a3b8;">{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px; color: #64748b;">
                                    暂无积分流水记录
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 分页 --}}
            <div style="margin-top: 20px; display: flex; justify-content: center; gap: 8px;">
                @if($this->transactions->onFirstPage())
                    <span style="padding: 8px 16px; background: rgba(255,255,255,0.05); color: #64748b; border-radius: 6px; font-size: 13px;">上一页</span>
                @else
                    <a href="?page={{ $this->transactions->currentPage() - 1 }}" style="padding: 8px 16px; background: rgba(255,255,255,0.05); color: white; border-radius: 6px; font-size: 13px; text-decoration: none;">上一页</a>
                @endif

                <span style="padding: 8px 16px; background: rgba(99, 102, 241, 0.2); color: #818cf8; border-radius: 6px; font-size: 13px;">
                    第 {{ $this->transactions->currentPage() }} / {{ $this->transactions->lastPage() }} 页
                </span>

                @if($this->transactions->hasMorePages())
                    <a href="?page={{ $this->transactions->currentPage() + 1 }}" style="padding: 8px 16px; background: rgba(255,255,255,0.05); color: white; border-radius: 6px; font-size: 13px; text-decoration: none;">下一页</a>
                @else
                    <span style="padding: 8px 16px; background: rgba(255,255,255,0.05); color: #64748b; border-radius: 6px; font-size: 13px;">下一页</span>
                @endif
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
