<x-filament-panels::page>
    <div style="display: grid; gap: 24px;">
        
        {{-- 任务统计 --}}
        <x-filament::section>
            <x-slot name="heading">
                📊 任务统计
            </x-slot>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div style="padding: 20px; background: rgba(99, 102, 241, 0.1); border-radius: 12px; border: 1px solid rgba(99, 102, 241, 0.3);">
                    <div style="color: #6366f1; font-size: 14px; margin-bottom: 8px;">🔄 执行中</div>
                    <div style="color: white; font-size: 28px; font-weight: 700;">
                        {{ \App\Models\AsyncTask::where('status', 'running')->count() }}
                    </div>
                </div>
                
                <div style="padding: 20px; background: rgba(16, 185, 129, 0.1); border-radius: 12px; border: 1px solid rgba(16, 185, 129, 0.3);">
                    <div style="color: #10b981; font-size: 14px; margin-bottom: 8px;">✅ 已完成</div>
                    <div style="color: white; font-size: 28px; font-weight: 700;">
                        {{ \App\Models\AsyncTask::where('status', 'completed')->whereDate('created_at', today())->count() }}
                    </div>
                    <div style="color: #94a3b8; font-size: 12px; margin-top: 4px;">今日</div>
                </div>
                
                <div style="padding: 20px; background: rgba(239, 68, 68, 0.1); border-radius: 12px; border: 1px solid rgba(239, 68, 68, 0.3);">
                    <div style="color: #ef4444; font-size: 14px; margin-bottom: 8px;">❌ 失败</div>
                    <div style="color: white; font-size: 28px; font-weight: 700;">
                        {{ \App\Models\AsyncTask::where('status', 'failed')->whereDate('created_at', today())->count() }}
                    </div>
                    <div style="color: #94a3b8; font-size: 12px; margin-top: 4px;">今日</div>
                </div>
                
                <div style="padding: 20px; background: rgba(234, 179, 8, 0.1); border-radius: 12px; border: 1px solid rgba(234, 179, 8, 0.3);">
                    <div style="color: #eab308; font-size: 14px; margin-bottom: 8px;">⏳ 等待中</div>
                    <div style="color: white; font-size: 28px; font-weight: 700;">
                        {{ \App\Models\AsyncTask::where('status', 'pending')->count() }}
                    </div>
                </div>
            </div>
        </x-filament::section>

        {{-- 任务列表 --}}
        <x-filament::section>
            <x-slot name="heading">
                📋 任务执行记录
            </x-slot>
            <x-slot name="description">
                查看所有异步任务的执行状态和进度
            </x-slot>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <th style="text-align: left; padding: 12px; color: #94a3b8; font-size: 13px;">ID</th>
                            <th style="text-align: left; padding: 12px; color: #94a3b8; font-size: 13px;">任务名称</th>
                            <th style="text-align: left; padding: 12px; color: #94a3b8; font-size: 13px;">类型</th>
                            <th style="text-align: left; padding: 12px; color: #94a3b8; font-size: 13px;">状态</th>
                            <th style="text-align: left; padding: 12px; color: #94a3b8; font-size: 13px;">进度</th>
                            <th style="text-align: left; padding: 12px; color: #94a3b8; font-size: 13px;">成功/失败</th>
                            <th style="text-align: left; padding: 12px; color: #94a3b8; font-size: 13px;">时间</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->tasks as $task)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td style="padding: 12px; color: #64748b;">#{{ $task->id }}</td>
                                <td style="padding: 12px; color: white; max-width: 200px; overflow: hidden; text-overflow: ellipsis;">{{ $task->name }}</td>
                                <td style="padding: 12px;">
                                    <span style="padding: 4px 8px; background: rgba(99, 102, 241, 0.2); color: #818cf8; border-radius: 4px; font-size: 12px;">
                                        {{ match($task->type) {
                                            'fetch_articles' => '📝 文章',
                                            'fetch_projects' => '🐙 项目',
                                            'fetch_jobs' => '💼 职位',
                                            'knowledge_fetch' => '📚 知识库',
                                            'send_email' => '📧 邮件',
                                            default => $task->type
                                        } }}
                                    </span>
                                </td>
                                <td style="padding: 12px;">
                                    <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;
                                        {{ $task->status === 'completed' ? 'background: rgba(16, 185, 129, 0.2); color: #10b981;' : '' }}
                                        {{ $task->status === 'running' ? 'background: rgba(99, 102, 241, 0.2); color: #6366f1;' : '' }}
                                        {{ $task->status === 'failed' ? 'background: rgba(239, 68, 68, 0.2); color: #ef4444;' : '' }}
                                        {{ $task->status === 'pending' ? 'background: rgba(100, 116, 139, 0.2); color: #64748b;' : '' }}
                                    ">
                                        {{ match($task->status) {
                                            'pending' => '⏳ 等待',
                                            'running' => '🔄 执行',
                                            'completed' => '✅ 完成',
                                            'failed' => '❌ 失败',
                                            default => $task->status
                                        } }}
                                    </span>
                                </td>
                                <td style="padding: 12px; color: #94a3b8;">
                                    @if($task->total > 0)
                                        {{ $task->progress }}% ({{ $task->processed }}/{{ $task->total }})
                                    @else
                                        -
                                    @endif
                                </td>
                                <td style="padding: 12px;">
                                    <span style="color: #10b981;">{{ $task->success }}</span> / 
                                    <span style="color: #ef4444;">{{ $task->failed }}</span>
                                </td>
                                <td style="padding: 12px; color: #64748b; font-size: 13px;">
                                    {{ $task->created_at->format('m-d H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #64748b;">
                                    暂无任务记录
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- 快速操作（采集）：暂时隐藏，需要时在 TaskManager 与视图中恢复 --}}
    </div>
</x-filament-panels::page>
