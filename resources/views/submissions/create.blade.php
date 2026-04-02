@extends('layouts.app')

@section('title', isset($submission) ? '重新编辑投稿 - AI 副业情报局' : 'VIP 投稿 - AI 副业情报局')

@section('content')
<div class="container" style="max-width: 900px; margin: 0 auto; padding: 40px 20px;">
    
    @php
        $isEdit = isset($submission);
        $typeIcons = ['document' => '📄', 'project' => '🚀', 'job' => '💼', 'knowledge' => '📚'];
        $typeNames = ['document' => '文档', 'project' => '项目', 'job' => '职位', 'knowledge' => '知识库'];
        $typeName = $isEdit ? ($typeNames[$submission->type] ?? '投稿') : null;
        $typeIcon = $isEdit ? ($typeIcons[$submission->type] ?? '📝') : null;
    @endphp
    
    {{-- 页面标题 --}}
    <div style="text-align: center; margin-bottom: 40px;">
        @if($isEdit)
            <div style="display: inline-flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <span style="font-size: 40px;">{{ $typeIcon }}</span>
                <h1 style="font-size: 32px; font-weight: 800; color: var(--white); margin: 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    重新编辑{{ $typeName }}
                </h1>
            </div>
            <p style="color: var(--gray-light); font-size: 15px;">
                根据审核意见修改后重新提交
            </p>
        @else
            <h1 style="font-size: 32px; font-weight: 800; color: var(--white); margin-bottom: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                📝 VIP 投稿
            </h1>
            <p style="color: var(--gray-light); font-size: 15px;">
                分享优质内容，与社区共同成长
            </p>
        @endif
    </div>

    {{-- 审核备注提示（仅编辑模式） --}}
    @if($isEdit && $submission->review_note)
        <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 16px; padding: 20px 24px; margin-bottom: 32px;">
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <div style="font-size: 24px;">🚫</div>
                <div style="flex: 1;">
                    <div style="font-weight: 700; color: #f87171; margin-bottom: 8px; font-size: 15px;">审核意见</div>
                    <div style="color: var(--gray-light); line-height: 1.7; white-space: pre-wrap;">{{ $submission->review_note }}</div>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ $isEdit ? route('submissions.update', $submission->id) : route('submissions.store') }}" id="submission-form">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        {{-- 第一步：选择内容类型（仅新建模式）或显示类型（仅编辑模式） --}}
        <div class="form-section" style="background: var(--dark-light); border-radius: 16px; padding: 24px; margin-bottom: 24px; border: 1px solid rgba(255,255,255,0.08);">
            @if($isEdit)
                {{-- 编辑模式：显示只读类型 --}}
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        {{ $typeIcon }}
                    </div>
                    <div>
                        <h3 style="font-size: 18px; font-weight: 700; color: var(--white); margin: 0;">{{ $typeName }}信息</h3>
                        <p style="font-size: 13px; color: var(--gray); margin: 4px 0 0 0;">当前投稿类型（不可修改）</p>
                    </div>
                </div>
                <div style="padding: 14px 18px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; color: var(--white); font-size: 15px;">
                    {{ $typeIcon }} {{ $typeName }}
                </div>
                <input type="hidden" name="type" value="{{ $submission->type }}">
            @else
                {{-- 新建模式：选择类型 --}}
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        📂
                    </div>
                    <div>
                        <h3 style="font-size: 18px; font-weight: 700; color: var(--white); margin: 0;">选择内容类型</h3>
                        <p style="font-size: 13px; color: var(--gray); margin: 4px 0 0 0;">这将决定你需要填写的信息</p>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px;">
                    <label class="type-card" data-type="document" style="padding: 20px; background: rgba(255,255,255,0.03); border: 2px solid rgba(255,255,255,0.08); border-radius: 12px; cursor: pointer; transition: all 0.3s; text-align: center;">
                        <input type="radio" name="type" value="document" style="display: none;" checked>
                        <div style="font-size: 32px; margin-bottom: 8px;">📄</div>
                        <div style="font-weight: 600; color: var(--white); margin-bottom: 4px;">文档</div>
                        <div style="font-size: 12px; color: var(--gray);">教程/指南/文档</div>
                    </label>

                    <label class="type-card" data-type="knowledge" style="padding: 20px; background: rgba(255,255,255,0.03); border: 2px solid rgba(255,255,255,0.08); border-radius: 12px; cursor: pointer; transition: all 0.3s; text-align: center;">
                        <input type="radio" name="type" value="knowledge" style="display: none;">
                        <div style="font-size: 32px; margin-bottom: 8px;">📚</div>
                        <div style="font-weight: 600; color: var(--white); margin-bottom: 4px;">知识库</div>
                        <div style="font-size: 12px; color: var(--gray);">知识体系/资料</div>
                    </label>

                    <label class="type-card" data-type="project" style="padding: 20px; background: rgba(255,255,255,0.03); border: 2px solid rgba(255,255,255,0.08); border-radius: 12px; cursor: pointer; transition: all 0.3s; text-align: center;">
                        <input type="radio" name="type" value="project" style="display: none;">
                        <div style="font-size: 32px; margin-bottom: 8px;">🚀</div>
                        <div style="font-weight: 600; color: var(--white); margin-bottom: 4px;">项目</div>
                        <div style="font-size: 12px; color: var(--gray);">开源项目/工具</div>
                    </label>

                    <label class="type-card" data-type="job" style="padding: 20px; background: rgba(255,255,255,0.03); border: 2px solid rgba(255,255,255,0.08); border-radius: 12px; cursor: pointer; transition: all 0.3s; text-align: center;">
                        <input type="radio" name="type" value="job" style="display: none;">
                        <div style="font-size: 32px; margin-bottom: 8px;">💼</div>
                        <div style="font-weight: 600; color: var(--white); margin-bottom: 4px;">职位</div>
                        <div style="font-size: 12px; color: var(--gray);">招聘信息</div>
                    </label>
                </div>
            @endif
        </div>

        {{-- 第二步：基本信息 --}}
        <div class="form-section" style="background: var(--dark-light); border-radius: 16px; padding: 24px; margin-bottom: 24px; border: 1px solid rgba(255,255,255,0.08);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    ✏️
                </div>
                <div>
                    <h3 style="font-size: 18px; font-weight: 700; color: var(--white); margin: 0;">基本信息</h3>
                    <p style="font-size: 13px; color: var(--gray); margin: 4px 0 0 0;">填写内容的核心信息</p>
                </div>
            </div>

            <div style="display: grid; gap: 20px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 600; color: var(--white); margin-bottom: 8px;">
                        标题 <span style="color: #ef4444;">*</span>
                    </label>
                    <input name="title" required maxlength="255" 
                           value="{{ $isEdit ? old('title', $submission->title) : old('title') }}"
                           placeholder="请输入吸引人的标题..." 
                           style="width: 100%; padding: 14px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; color: var(--white); font-size: 15px; transition: all 0.2s;"
                           onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.15)'"
                           onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none'">
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 600; color: var(--white); margin-bottom: 8px;">
                        摘要 <span style="color: var(--gray); font-weight: 400;">（可选）</span>
                    </label>
                    <textarea name="summary" rows="3" maxlength="500" 
                              placeholder="简要描述内容亮点，将显示在列表页..." 
                              style="width: 100%; padding: 14px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; color: var(--white); font-size: 15px; resize: vertical; transition: all 0.2s;"
                              onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.15)'"
                              onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none'">{{ $isEdit ? old('summary', $submission->summary) : old('summary') }}</textarea>
                    <div style="font-size: 12px; color: var(--gray); margin-top: 6px; text-align: right;">
                        <span id="summary-count">{{ $isEdit ? strlen($submission->summary ?? '') : 0 }}</span>/500
                    </div>
                </div>
            </div>
        </div>

        {{-- 第三步：动态字段（根据类型显示） --}}
        
        {{-- 项目类型专属字段 --}}
        <div id="project-fields" class="type-fields" style="display: none; background: var(--dark-light); border-radius: 16px; padding: 24px; margin-bottom: 24px; border: 1px solid rgba(255,255,255,0.08);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    🚀
                </div>
                <div>
                    <h3 style="font-size: 18px; font-weight: 700; color: var(--white); margin: 0;">项目信息</h3>
                    <p style="font-size: 13px; color: var(--gray); margin: 4px 0 0 0;">补充项目的详细信息</p>
                </div>
            </div>

            <div style="display: grid; gap: 20px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 600; color: var(--white); margin-bottom: 8px;">
                        GitHub / 项目链接 <span style="color: #ef4444;">*</span>
                    </label>
                    <input name="project_url" maxlength="500" 
                           placeholder="https://github.com/..." 
                           style="width: 100%; padding: 14px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; color: var(--white); font-size: 15px; transition: all 0.2s;"
                           onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.15)'"
                           onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none'">
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 600; color: var(--white); margin-bottom: 8px;">
                        技术栈 <span style="color: var(--gray); font-weight: 400;">（用逗号分隔）</span>
                    </label>
                    <input name="tech_stack" maxlength="500" 
                           placeholder="Laravel, Vue.js, MySQL..." 
                           style="width: 100%; padding: 14px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; color: var(--white); font-size: 15px; transition: all 0.2s;"
                           onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.15)'"
                           onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none'">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: var(--white); margin-bottom: 8px;">
                            难度等级
                        </label>
                        <select name="difficulty" style="width: 100%; padding: 14px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; color: var(--white); font-size: 15px; cursor: pointer; transition: all 0.2s;"
                                onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='rgba(255,255,255,0.15)'">
                            <option value="" style="background: var(--dark); color: var(--white);">请选择</option>
                            <option value="beginner" style="background: var(--dark); color: var(--white);">🌱 入门</option>
                            <option value="intermediate" style="background: var(--dark); color: var(--white);">🌿 进阶</option>
                            <option value="advanced" style="background: var(--dark); color: var(--white);">🌳 高级</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: var(--white); margin-bottom: 8px;">
                            月收入预估
                        </label>
                        <select name="income_range" style="width: 100%; padding: 14px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; color: var(--white); font-size: 15px; cursor: pointer; transition: all 0.2s;"
                                onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='rgba(255,255,255,0.15)'">
                            <option value="" style="background: var(--dark); color: var(--white);">请选择</option>
                            <option value="0-1k" style="background: var(--dark); color: var(--white);">💰 0-1k</option>
                            <option value="1k-5k" style="background: var(--dark); color: var(--white);">💰💰 1k-5k</option>
                            <option value="5k-10k" style="background: var(--dark); color: var(--white);">💰💰 5k-10k</option>
                            <option value="10k+" style="background: var(--dark); color: var(--white);">💰💰💰💰 10k+</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: var(--white); margin-bottom: 8px;">
                            时间投入
                        </label>
                        <select name="time_commitment" style="width: 100%; padding: 14px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; color: var(--white); font-size: 15px; cursor: pointer; transition: all 0.2s;"
                                onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='rgba(255,255,255,0.15)'">
                            <option value="" style="background: var(--dark); color: var(--white);">请选择</option>
                            <option value="part-time" style="background: var(--dark); color: var(--white);">⏰ 兼职</option>
                            <option value="full-time" style="background: var(--dark); color: var(--white);">⏰⏰ 全职</option>
                            <option value="flexible" style="background: var(--dark); color: var(--white);">⏰ 灵活</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- 职位类型专属字段 --}}
        @php
            $jobPayload = ($isEdit && isset($submission)) ? ($submission->payload ?? []) : [];
        @endphp
        <div id="job-fields" class="type-fields" style="display: none; background: var(--dark-light); border-radius: 16px; padding: 24px; margin-bottom: 24px; border: 1px solid rgba(255,255,255,0.08);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    💼
                </div>
                <div>
                    <h3 style="font-size: 18px; font-weight: 700; color: var(--white); margin: 0;">职位信息</h3>
                    <p style="font-size: 13px; color: var(--gray); margin: 4px 0 0 0;">补充招聘的详细信息</p>
                </div>
            </div>

            <div style="display: grid; gap: 20px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: var(--white); margin-bottom: 8px;">
                            公司名称 <span style="color: #ef4444;">*</span>
                        </label>
                        <input name="company_name" required maxlength="200" 
                               value="{{ $isEdit ? old('company_name', $jobPayload['company_name'] ?? '') : old('company_name') }}"
                               placeholder="请输入公司名称" 
                               style="width: 100%; padding: 14px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; color: var(--white); font-size: 15px; transition: all 0.2s;"
                               onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.15)'"
                               onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none'">
                    </div>

                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: var(--white); margin-bottom: 8px;">
                            职位名称 <span style="color: #ef4444;">*</span>
                        </label>
                        <input name="job_title" required maxlength="200" 
                               value="{{ $isEdit ? old('job_title', $jobPayload['job_title'] ?? '') : old('job_title') }}"
                               placeholder="例如：高级 PHP 工程师" 
                               style="width: 100%; padding: 14px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; color: var(--white); font-size: 15px; transition: all 0.2s;"
                               onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.15)'"
                               onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none'">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: var(--white); margin-bottom: 8px;">
                            薪资范围
                        </label>
                        <input name="salary_range" maxlength="100" 
                               value="{{ $isEdit ? old('salary_range', $jobPayload['salary_range'] ?? '') : old('salary_range') }}"
                               placeholder="例如：15-25k·14 薪" 
                               style="width: 100%; padding: 14px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; color: var(--white); font-size: 15px; transition: all 0.2s;"
                               onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.15)'"
                               onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none'">
                    </div>

                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 600; color: var(--white); margin-bottom: 8px;">
                            工作地点
                        </label>
                        <input name="location" maxlength="100" 
                               value="{{ $isEdit ? old('location', $jobPayload['location'] ?? '') : old('location') }}"
                               placeholder="例如：北京·朝阳区" 
                               style="width: 100%; padding: 14px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; color: var(--white); font-size: 15px; transition: all 0.2s;"
                               onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.15)'"
                               onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none'">
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 600; color: var(--white); margin-bottom: 8px;">
                        任职要求
                    </label>
                    <textarea name="job_requirements" rows="4" 
                              placeholder="列出职位要求和技能要求..." 
                              style="width: 100%; padding: 14px 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; color: var(--white); font-size: 15px; resize: vertical; transition: all 0.2s;"
                              onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.15)'"
                              onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.boxShadow='none'">{{ $isEdit ? old('job_requirements', $jobPayload['job_requirements'] ?? '') : old('job_requirements') }}</textarea>
                </div>
            </div>
        </div>

        {{-- 第四步：正文内容 --}}
        <div class="form-section" style="background: var(--dark-light); border-radius: 16px; padding: 24px; margin-bottom: 24px; border: 1px solid rgba(255,255,255,0.08);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    📝
                </div>
                <div>
                    <h3 style="font-size: 18px; font-weight: 700; color: var(--white); margin: 0;">正文内容 <span style="color: #ef4444;">*</span></h3>
                    <p style="font-size: 13px; color: var(--gray); margin: 4px 0 0 0;">详细描述你的内容</p>
                </div>
            </div>

            <input id="content" type="hidden" name="content" value="{{ $isEdit ? old('content', $submission->content) : old('content') }}" required>
            <div style="background: rgba(0,0,0,0.2); border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.15);">
                <trix-editor input="content" 
                             placeholder="开始撰写精彩内容... 支持富文本编辑，可以添加标题、列表、引用、代码块等格式" 
                             style="min-height: 350px; background: transparent; color: var(--white); font-size: 15px; line-height: 1.7; padding: 16px;"></trix-editor>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 12px;">
                <div style="font-size: 12px; color: var(--gray);">
                    💡 提示：使用工具栏添加格式，让内容更易读
                </div>
                <div style="font-size: 12px; color: var(--gray);">
                    <span id="content-count">0</span> 字
                </div>
            </div>
        </div>

        {{-- 第五步：VIP 设置 --}}
        <div class="form-section" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.15) 0%, rgba(139, 92, 246, 0.15) 100%); border-radius: 16px; padding: 24px; margin-bottom: 24px; border: 1px solid rgba(99, 102, 241, 0.3);">
            <div style="display: flex; align-items: flex-start; gap: 16px;">
                <label style="display: flex; align-items: flex-start; gap: 14px; cursor: pointer; flex: 1;">
                    <input type="checkbox" name="is_paid" value="1" {{ $isEdit && $submission->is_paid ? 'checked' : '' }}
                           style="width: 22px; height: 22px; margin-top: 2px; accent-color: #6366f1; cursor: pointer;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                            <span style="font-size: 16px; font-weight: 700; color: var(--white);">⭐ 设为 VIP 专属内容</span>
                        </div>
                        <p style="font-size: 13px; color: var(--gray-light); line-height: 1.6; margin: 0;">
                            开启后仅 VIP 用户可观看完整内容，普通用户只能看到摘要。{{ $isEdit ? '修改后可重新提交审核。' : '这是变现的好方式！' }}
                        </p>
                    </div>
                </label>
            </div>
        </div>

        {{-- 提交按钮 --}}
        <div style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 8px;">
            <a href="{{ route('submissions.index') }}" 
               style="padding: 14px 32px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.2); color: var(--gray-light); text-decoration: none; font-weight: 600; font-size: 15px; transition: all 0.2s;"
               onmouseover="this.style.background='rgba(255,255,255,0.05)'; this.style.borderColor='rgba(255,255,255,0.3)'; this.style.color='var(--white)'"
               onmouseout="this.style.background='transparent'; this.style.borderColor='rgba(255,255,255,0.2)'; this.style.color='var(--gray-light)'">
                取消
            </a>
            <button type="submit" 
                    style="padding: 14px 40px; border: none; border-radius: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 700; font-size: 15px; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(102, 126, 234, 0.5)'"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(102, 126, 234, 0.4)'">
                {{ $isEdit ? '✨ 重新提交审核' : '✨ 提交审核' }}
            </button>
        </div>
    </form>
</div>

{{-- 引入 Trix 富文本编辑器 --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.js"></script>

{{-- Trix 图片上传配置 --}}
<script>
// 监听 Trix 文件上传事件
document.addEventListener("trix-file-accept", function(event) {
    // 只接受图片
    if (!event.file.type.match('image.*')) {
        event.preventDefault();
        alert('只支持上传图片文件');
        return;
    }
    
    // 限制图片大小（5MB）
    if (event.file.size > 5 * 1024 * 1024) {
        event.preventDefault();
        alert('图片大小不能超过 5MB');
        return;
    }
});

// 处理图片上传
document.addEventListener("trix-attachment-add", function(event) {
    const attachment = event.attachment;
    
    if (attachment.file && !attachment.getHref()) {
        // 上传图片到服务器
        uploadImage(attachment.file).then(url => {
            attachment.setAttributes({
                url: url,
                href: url
            });
        }).catch(error => {
            alert('图片上传失败：' + error.message);
            attachment.remove();
        });
    }
});

// 上传图片函数
async function uploadImage(file) {
    const formData = new FormData();
    formData.append('image', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    const response = await fetch('/admin/upload-image', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    
    if (!response.ok) {
        throw new Error('上传失败');
    }
    
    const data = await response.json();
    return data.url || data.location;
}
</script>

<style>
/* Trix 暗色主题深度适配 */
trix-toolbar {
    position: sticky;
    top: 0;
    z-index: 10;
    background: rgba(30, 41, 59, 0.95);
    backdrop-filter: blur(10px);
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    border-bottom: 1px solid rgba(255,255,255,0.08) !important;
    padding: 12px 16px;
}

trix-toolbar .trix-button-row {
    border-bottom: none !important;
    gap: 8px;
}

trix-toolbar .trix-button-group {
    border: none !important;
    margin: 0 !important;
}

trix-toolbar .trix-button {
    color: #94a3b8 !important;
    border-color: transparent !important;
    background: transparent !important;
    border-radius: 6px !important;
    padding: 6px 10px !important;
    transition: all 0.2s !important;
}

trix-toolbar .trix-button:hover {
    background: rgba(255,255,255,0.1) !important;
    color: #f1f5f9 !important;
}

trix-toolbar .trix-button.trix-active {
    background: rgba(99, 102, 241, 0.3) !important;
    color: #f1f5f9 !important;
}

trix-toolbar .trix-button--icon::before {
    opacity: 1 !important;
    filter: none !important;
}

trix-toolbar .trix-dialog {
    background: #1e293b !important;
    border: 1px solid rgba(255,255,255,0.15) !important;
    border-radius: 12px !important;
    box-shadow: 0 20px 50px rgba(0,0,0,0.5) !important;
}

trix-toolbar .trix-dialog input {
    background: rgba(0,0,0,0.3) !important;
    border: 1px solid rgba(255,255,255,0.15) !important;
    color: #f1f5f9 !important;
    border-radius: 8px !important;
    padding: 10px 14px !important;
}

trix-editor {
    min-height: 350px !important;
    color: #f1f5f9 !important;
    line-height: 1.7 !important;
}

trix-editor:focus {
    outline: none !important;
}

trix-editor a {
    color: #818cf8 !important;
    text-decoration: underline !important;
}

trix-editor blockquote {
    border-left: 3px solid #6366f1 !important;
    padding-left: 16px !important;
    margin-left: 0 !important;
    color: #94a3b8 !important;
    font-style: italic !important;
}

trix-editor pre {
    background: rgba(0,0,0,0.4) !important;
    border-radius: 8px !important;
    padding: 16px !important;
    color: #e2e8f0 !important;
    font-family: 'Fira Code', 'Consolas', monospace !important;
    font-size: 13px !important;
    overflow-x: auto !important;
}

trix-editor ul, trix-editor ol {
    padding-left: 24px !important;
    color: #f1f5f9 !important;
}

trix-editor li {
    margin-bottom: 8px !important;
}

trix-editor h1, trix-editor h2, trix-editor h3 {
    color: #f1f5f9 !important;
    font-weight: 700 !important;
    margin-top: 24px !important;
    margin-bottom: 12px !important;
}

trix-editor h1 { font-size: 24px !important; }
trix-editor h2 { font-size: 20px !important; }
trix-editor h3 { font-size: 18px !important; }

.trix-content {
    line-height: 1.7 !important;
}

/* 类型卡片选中状态 */
.type-card:has(input:checked) {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.2) 0%, rgba(139, 92, 246, 0.2) 100%) !important;
    border-color: #667eea !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.type-card:hover {
    background: rgba(255,255,255,0.06) !important;
    border-color: rgba(255,255,255,0.15) !important;
    transform: translateY(-2px);
}

/* 响应式 */
@media (max-width: 768px) {
    .container { padding: 20px 16px; }
    
    .type-card { padding: 16px 12px; }
    .type-card div:first-child { font-size: 28px; }
    
    [style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
    
    [style*="grid-template-columns: 1fr 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
// 页面加载时初始化
@php $currentType = $isEdit ? $submission->type : 'document'; @endphp

// 显示对应类型的字段
function showTypeFields(type) {
    document.querySelectorAll('.type-fields').forEach(f => f.style.display = 'none');
    
    if (type === 'project') {
        document.getElementById('project-fields').style.display = 'block';
    } else if (type === 'job') {
        document.getElementById('job-fields').style.display = 'block';
    }
    // document 和 knowledge 没有专属字段
}

// 编辑模式下自动显示对应类型的字段
@if($isEdit)
    document.addEventListener('DOMContentLoaded', function() {
        showTypeFields('{{ $submission->type }}');
    });
@endif

// 类型切换逻辑
document.querySelectorAll('.type-card').forEach(card => {
    card.addEventListener('click', function() {
        // 更新选中状态
        document.querySelectorAll('.type-card').forEach(c => {
            c.style.background = 'rgba(255,255,255,0.03)';
            c.style.borderColor = 'rgba(255,255,255,0.08)';
            c.style.transform = 'translateY(0)';
            c.style.boxShadow = 'none';
        });
        
        this.style.background = 'linear-gradient(135deg, rgba(99, 102, 241, 0.2) 0%, rgba(139, 92, 246, 0.2) 100%)';
        this.style.borderColor = '#667eea';
        this.style.transform = 'translateY(-2px)';
        this.style.boxShadow = '0 8px 25px rgba(102, 126, 234, 0.3)';
        
        // 显示对应字段
        const type = this.querySelector('input').value;
        showTypeFields(type);
    });
});

// 摘要字数统计
const summaryInput = document.querySelector('textarea[name="summary"]');
const summaryCount = document.getElementById('summary-count');
if (summaryInput && summaryCount) {
    summaryInput.addEventListener('input', () => {
        summaryCount.textContent = summaryInput.value.length;
    });
}

// 内容字数统计（Trix）
document.addEventListener('trix-change', function(e) {
    const editor = e.target;
    const content = editor.editor.getDocument().toString();
    const countEl = document.getElementById('content-count');
    if (countEl) {
        countEl.textContent = content.length;
    }
});

// 表单验证
document.getElementById('submission-form').addEventListener('submit', function(e) {
    const content = document.getElementById('content').value;
    if (!content || content.trim().length === 0) {
        e.preventDefault();
        alert('请输入正文内容');
        document.querySelector('trix-editor').focus();
        return false;
    }
});
</script>
@endsection
