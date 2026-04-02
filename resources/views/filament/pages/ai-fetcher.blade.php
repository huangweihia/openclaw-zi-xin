<x-filament-panels::page>
    <style>
        .fi-ai-fetcher-panel {
            background: rgb(17 24 39);
            border: 1px solid rgb(51 65 85 / 0.85);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 24px rgb(0 0 0 / 0.35);
        }
        .fi-ai-fetcher-panel label,
        .fi-ai-fetcher-panel h3,
        .fi-ai-fetcher-panel h4 {
            color: rgb(226 232 240);
        }
        .fi-ai-fetcher-panel select,
        .fi-ai-fetcher-panel input[type="text"],
        .fi-ai-fetcher-panel input[type="number"] {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            background: rgb(30 41 59);
            color: rgb(241 245 249);
            border: 2px solid rgb(71 85 105);
        }
        .fi-ai-fetcher-panel select:focus,
        .fi-ai-fetcher-panel input:focus {
            outline: none;
            border-color: rgb(99 102 241);
        }
        .fi-ai-fetcher-panel select option {
            background: rgb(30 41 59);
            color: rgb(241 245 249);
        }
        .fi-ai-fetcher-panel input::placeholder {
            color: rgb(100 116 139);
        }
        .fi-ai-fetcher-hint {
            background: rgb(15 23 42 / 0.92);
            border: 1px solid rgb(99 102 241 / 0.35);
            border-radius: 12px;
            padding: 20px;
        }
        .fi-ai-fetcher-hint h4 {
            color: rgb(226 232 240);
            margin: 0 0 12px;
        }
        .fi-ai-fetcher-hint ul {
            color: rgb(203 213 225);
            font-size: 14px;
            line-height: 1.8;
            margin: 0;
            padding-left: 20px;
        }
    </style>
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

        {{-- 自定义采集表单（深色卡片，与 Filament 后台暗色主题一致，避免白底 + 继承浅色文字） --}}
        <div class="fi-ai-fetcher-panel">
            <h3 style="font-size: 18px; font-weight: 700; margin: 0 0 20px;">
                🔧 自定义采集
            </h3>
            
            <div style="display: grid; gap: 16px;">
                {{-- 采集类型 --}}
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">
                        采集类型
                    </label>
                    <select wire:model="selectedType">
                        <option value="articles">📝 AI 文章</option>
                        <option value="projects">💻 GitHub 项目</option>
                        <option value="jobs">💼 AI 职位</option>
                        <option value="knowledge">📚 知识库文档</option>
                    </select>
                </div>
                
                {{-- 采集主题 --}}
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">
                        采集主题
                    </label>
                    <input type="text" 
                           wire:model="topic"
                           placeholder="例如：GPT-5 最新动态"
                    />
                </div>
                
                {{-- 采集数量 --}}
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">
                        采集数量
                    </label>
                    <input type="number" 
                           wire:model="limit"
                           min="1"
                           max="20"
                           style="max-width: 200px;"
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
                
                {{-- 状态显示 --}}
                @if($status)
                    <div style="
                        padding: 16px;
                        background: {{ Str::contains($status, '✅') ? 'rgb(6 78 59 / 0.35)' : (Str::contains($status, '❌') ? 'rgb(127 29 29 / 0.35)' : 'rgb(30 58 138 / 0.35)') }};
                        border-radius: 8px;
                        border: 1px solid {{ Str::contains($status, '✅') ? 'rgb(52 211 153 / 0.4)' : (Str::contains($status, '❌') ? 'rgb(252 165 165 / 0.4)' : 'rgb(129 140 248 / 0.4)') }};
                        color: {{ Str::contains($status, '✅') ? '#6ee7b7' : (Str::contains($status, '❌') ? '#fca5a5' : '#c7d2fe') }};
                        font-weight: 600;
                        margin-top: 16px;
                    ">
                        {!! $status !!}
                        @if($resultCount > 0)
                            <div style="margin-top: 8px; font-size: 14px; font-weight: normal; color: rgb(203 213 225);">
                                数据已保存到数据库，可在对应管理页面查看
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- 使用说明（深色背景 + 浅色字，保证对比度） --}}
        <div class="fi-ai-fetcher-hint">
            <h4 style="font-size: 16px; font-weight: 700;">
                💡 使用说明
            </h4>
            <ul>
                <li>使用 OpenClaw Gateway 调用 AI 生成真实内容</li>
                <li>点击快捷按钮快速采集默认主题</li>
                <li>自定义采集可以指定主题和数量</li>
                <li>采集的数据会自动保存到数据库</li>
                <li>可在文章/项目/职位管理页面查看和编辑</li>
                <li>定时任务会在每日凌晨 2 点自动执行</li>
            </ul>
        </div>

    </div>
</x-filament-panels::page>
