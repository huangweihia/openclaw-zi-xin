<x-filament-panels::page>
    <div style="max-width: 800px;">

        {{-- 快捷操作按钮 --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 32px;">
            <button wire:click="quickFetch('articles')"
                    wire:loading.attr="disabled"
                    style="
                        padding: 16px;
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        color: white;
                        border: none;
                        border-radius: 12px;
                        font-weight: 700;
                        font-size: 14px;
                        cursor: pointer;
                        transition: all 0.3s;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        gap: 8px;
                    "
                    onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 32px rgba(102, 126, 234, 0.4)'"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
            >
                <span style="font-size: 24px;">📝</span>
                <span>采集文章</span>
            </button>

            <button wire:click="quickFetch('projects')"
                    wire:loading.attr="disabled"
                    style="
                        padding: 16px;
                        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
                        color: white;
                        border: none;
                        border-radius: 12px;
                        font-weight: 700;
                        font-size: 14px;
                        cursor: pointer;
                        transition: all 0.3s;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        gap: 8px;
                    "
                    onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 32px rgba(72, 187, 120, 0.4)'"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
            >
                <span style="font-size: 24px;">💻</span>
                <span>采集项目</span>
            </button>

            <button wire:click="quickFetch('jobs')"
                    wire:loading.attr="disabled"
                    style="
                        padding: 16px;
                        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
                        color: white;
                        border: none;
                        border-radius: 12px;
                        font-weight: 700;
                        font-size: 14px;
                        cursor: pointer;
                        transition: all 0.3s;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        gap: 8px;
                    "
                    onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 32px rgba(237, 137, 54, 0.4)'"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
            >
                <span style="font-size: 24px;">💼</span>
                <span>采集职位</span>
            </button>

            <button wire:click="quickFetch('knowledge')"
                    wire:loading.attr="disabled"
                    style="
                        padding: 16px;
                        background: linear-gradient(135deg, #ecc94b 0%, #d69e2e 100%);
                        color: white;
                        border: none;
                        border-radius: 12px;
                        font-weight: 700;
                        font-size: 14px;
                        cursor: pointer;
                        transition: all 0.3s;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        gap: 8px;
                    "
                    onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 32px rgba(236, 201, 75, 0.4)'"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
            >
                <span style="font-size: 24px;">📚</span>
                <span>生成知识库</span>
            </button>
        </div>

        {{-- 自定义采集表单（与暗色后台一致，避免白底低对比） --}}
        <div style="
            background: rgba(24, 24, 27, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.35);
            margin-bottom: 24px;
        ">
            <h3 style="font-size: 18px; font-weight: 700; color: #fafafa; margin: 0 0 20px; letter-spacing: 0.02em;">
                🔧 自定义采集
            </h3>

            <div style="display: grid; gap: 16px;">
                {{-- 采集类型 --}}
                <div>
                    <label style="display: block; font-weight: 700; color: #e4e4e7; margin-bottom: 8px; font-size: 14px;">
                        采集类型
                    </label>
                    <select wire:model="selectedType"
                            style="
                                width: 100%;
                                padding: 12px;
                                background: #18181b;
                                color: #fafafa;
                                border: 2px solid #3f3f46;
                                border-radius: 8px;
                                font-size: 14px;
                                font-weight: 500;
                                transition: border-color 0.3s;
                            "
                            onfocus="this.style.borderColor='#a78bfa'"
                            onblur="this.style.borderColor='#3f3f46'"
                    >
                        <option value="articles">📝 AI 文章</option>
                        <option value="projects">💻 GitHub 项目</option>
                        <option value="jobs">💼 AI 职位</option>
                        <option value="knowledge">📚 知识库文档</option>
                    </select>
                </div>

                {{-- 采集主题 --}}
                <div>
                    <label style="display: block; font-weight: 700; color: #e4e4e7; margin-bottom: 8px; font-size: 14px;">
                        采集主题
                    </label>
                    <input type="text"
                           wire:model="topic"
                           placeholder="例如：GPT-5 最新动态"
                           style="
                               width: 100%;
                               padding: 12px;
                               background: #18181b;
                               color: #fafafa;
                               border: 2px solid #3f3f46;
                               border-radius: 8px;
                               font-size: 14px;
                               font-weight: 500;
                               transition: border-color 0.3s;
                           "
                           onfocus="this.style.borderColor='#a78bfa'"
                           onblur="this.style.borderColor='#3f3f46'"
                    />
                </div>

                {{-- 采集数量 --}}
                <div>
                    <label style="display: block; font-weight: 700; color: #e4e4e7; margin-bottom: 8px; font-size: 14px;">
                        采集数量
                    </label>
                    <input type="number"
                           wire:model="limit"
                           min="1"
                           max="20"
                           style="
                               width: 100%;
                               max-width: 200px;
                               padding: 12px;
                               background: #18181b;
                               color: #fafafa;
                               border: 2px solid #3f3f46;
                               border-radius: 8px;
                               font-size: 14px;
                               font-weight: 500;
                               transition: border-color 0.3s;
                           "
                           onfocus="this.style.borderColor='#a78bfa'"
                           onblur="this.style.borderColor='#3f3f46'"
                    />
                </div>

                {{-- 开始采集按钮 --}}
                <button wire:click="startFetch"
                        wire:loading.attr="disabled"
                        style="
                            padding: 14px 32px;
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: white;
                            border: none;
                            border-radius: 12px;
                            font-weight: 700;
                            font-size: 16px;
                            cursor: pointer;
                            transition: all 0.3s;
                            display: inline-flex;
                            align-items: center;
                            gap: 8px;
                        "
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(102, 126, 234, 0.4)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
                >
                    @if($isProcessing)
                        <span>⏳ 采集中...</span>
                    @else
                        <span>🚀 开始采集</span>
                    @endif
                </button>

                {{-- 状态显示（成功/错误高对比） --}}
                @if($status)
                    <div style="
                        padding: 16px 18px;
                        border-radius: 10px;
                        border-left: 4px solid {{ Str::contains($status, '✅') ? '#22c55e' : (Str::contains($status, '❌') ? '#f87171' : '#a78bfa') }};
                        background: {{ Str::contains($status, '✅') ? 'rgba(34, 197, 94, 0.18)' : (Str::contains($status, '❌') ? 'rgba(248, 113, 113, 0.2)' : 'rgba(167, 139, 250, 0.15)') }};
                        color: {{ Str::contains($status, '✅') ? '#bbf7d0' : (Str::contains($status, '❌') ? '#fecaca' : '#e4e4e7') }};
                        font-weight: 600;
                        font-size: 15px;
                        line-height: 1.55;
                        margin-top: 16px;
                        box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);
                    ">
                        {!! $status !!}
                        @if($resultCount > 0)
                            <div style="margin-top: 10px; font-size: 14px; font-weight: 500; color: #d4d4d8;">
                                数据已保存到数据库，可在对应管理页面查看
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- 使用说明 --}}
        <div style="
            background: rgba(39, 39, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 20px;
        ">
            <h4 style="font-size: 16px; font-weight: 700; color: #fafafa; margin: 0 0 12px;">
                💡 使用说明
            </h4>
            <ul style="color: #d4d4d8; font-size: 14px; line-height: 1.85; margin: 0; padding-left: 20px;">
                <li>点击快捷按钮快速采集默认主题</li>
                <li>自定义采集可以指定主题和数量</li>
                <li>采集的数据会自动保存到数据库</li>
                <li>可在文章/项目/职位管理页面查看和编辑</li>
                <li>定时任务会在每日凌晨 2 点自动执行</li>
                <li>API Token: <code style="background: rgba(0,0,0,0.35); color: #fde68a; padding: 3px 8px; border-radius: 4px; font-weight: 600;">openclaw-ai-fetcher-2026</code></li>
            </ul>
        </div>

    </div>
</x-filament-panels::page>
