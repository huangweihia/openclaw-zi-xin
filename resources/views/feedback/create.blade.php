@extends('layouts.app')

@section('title', '问题反馈 - AI 副业情报局')

@section('content')
<div class="container" style="max-width: 900px; margin: 40px auto 60px;">
    <div class="card" style="padding: 28px;">
        <h1 style="font-size: 24px; margin-bottom: 8px;">问题反馈</h1>
        <p style="color: var(--gray-light); margin-bottom: 18px;">欢迎反馈 bug / 建议，审核采纳后自动奖励 1 天 VIP。</p>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div id="feedbackSuccessBox" class="alert alert-success" style="display:none;"></div>
        <div id="feedbackErrorBox" class="alert alert-error" style="display:none;"></div>
        <form id="feedbackForm" method="POST" action="{{ route('feedback.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label" for="title">标题</label>
                <input id="title" class="form-input" type="text" name="title" value="{{ old('title') }}" required placeholder="例如：广告位上传失败无法清除">
            </div>
            <div class="form-group">
                <label class="form-label" for="content">问题详情</label>
                <textarea id="content" class="form-input" name="content" rows="7" required placeholder="请描述复现步骤、预期结果、实际结果">{{ old('content') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label" for="image">截图（可选）</label>
                <input id="image" class="form-input" type="file" name="image" accept="image/*" style="display:none;">

                {{-- 更好看的上传面板（支持点击选择/拖拽） --}}
                <label for="image" id="imageDropZone"
                       style="display:block; margin-top: 10px; padding: 16px; border-radius: 16px; border: 1px dashed rgba(99, 102, 241, 0.45); background: rgba(99, 102, 241, 0.08); cursor:pointer; transition: all .2s;"
                       ondragover="return false;">
                    <div style="display:flex; gap:14px; align-items:center;">
                        <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(99,102,241,0.18); display:flex; align-items:center; justify-content:center; font-size:18px;">
                            🖼️
                        </div>
                        <div style="flex:1;">
                            <div style="font-weight: 800; color: var(--white); font-size: 14px; margin-bottom: 4px;">点击选择图片或拖拽到这里</div>
                            <div style="font-size: 12px; color: var(--gray-light); line-height: 1.6;">支持 jpg/png/webp，最大 4MB</div>
                        </div>
                    </div>
                </label>

                <div id="imagePreviewWrap" style="margin-top: 12px; padding: 12px; border-radius: 12px; border: 1px solid rgba(99, 102, 241, 0.35); background: rgba(99, 102, 241, 0.06); display:none;">
                    <div style="display:flex; justify-content:space-between; gap:12px; align-items:center; margin-bottom: 10px;">
                        <div style="font-weight: 800; color: var(--primary-light); font-size: 13px;">预览图</div>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div id="imagePreviewMeta" style="font-size: 12px; color: var(--gray-light);"></div>
                            <button id="imageRemoveBtn" type="button"
                                    style="padding: 6px 10px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.15); background: rgba(0,0,0,0.15); color: var(--gray-light); cursor:pointer; font-weight: 700; font-size: 12px;">
                                移除
                            </button>
                        </div>
                    </div>
                    <img id="imagePreview" alt="反馈截图预览" style="max-width:100%; border-radius: 10px; border: 1px solid rgba(255,255,255,0.08);">
                </div>
            </div>
            <button type="submit" id="feedbackSubmitBtn" class="btn btn-primary">提交反馈</button>
        </form>
    </div>

    <div class="card" style="padding: 22px; margin-top: 18px;">
        <h2 style="font-size: 18px; margin-bottom: 12px;">我的最近反馈</h2>
        @forelse($feedbacks as $f)
            <div style="padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.08);">
                <div style="display:flex; justify-content:space-between; gap:12px;">
                    <strong>{{ $f->title }}</strong>
                    <span style="font-size:12px; color: var(--gray-light);">
                        {{ ['pending' => '待审核', 'approved' => '已采纳', 'rejected' => '已拒绝'][$f->status] ?? $f->status }}
                    </span>
                </div>
                <div style="margin-top:6px; color: var(--gray-light); font-size: 13px;">
                    {{ $f->created_at?->format('Y-m-d H:i') }}
                    @if($f->status === 'approved' && $f->rewarded_at)
                        · 已奖励 1 天 VIP
                    @endif
                </div>
                @if($f->review_note)
                    <div style="margin-top:8px; font-size:13px; color:#a5b4fc;">审核备注：{{ $f->review_note }}</div>
                @endif
            </div>
        @empty
            <p style="color: var(--gray-light);">还没有提交过反馈。</p>
        @endforelse
    </div>
</div>
    <script>
        (function () {
            var form = document.getElementById('feedbackForm');
            var submitBtn = document.getElementById('feedbackSubmitBtn');
            var successBox = document.getElementById('feedbackSuccessBox');
            var errorBox = document.getElementById('feedbackErrorBox');
            var input = document.getElementById('image');
            if (!input) return;
            var dropZone = document.getElementById('imageDropZone');
            var wrap = document.getElementById('imagePreviewWrap');
            var img = document.getElementById('imagePreview');
            var meta = document.getElementById('imagePreviewMeta');
            var removeBtn = document.getElementById('imageRemoveBtn');
            if (!wrap || !img || !meta) return;

            var lastObjectUrl = null;

            function clearPreview() {
                if (lastObjectUrl) {
                    try { URL.revokeObjectURL(lastObjectUrl); } catch (e) {}
                    lastObjectUrl = null;
                }
                wrap.style.display = 'none';
                img.removeAttribute('src');
                meta.textContent = '';
            }

            function setSubmitState(loading) {
                if (!submitBtn) return;
                submitBtn.disabled = !!loading;
                submitBtn.textContent = loading ? '提交中...' : '提交反馈';
            }

            function showSuccess(msg) {
                if (!successBox || !errorBox) return;
                errorBox.style.display = 'none';
                errorBox.textContent = '';
                successBox.style.display = 'block';
                successBox.textContent = msg || '提交成功';
            }

            function showError(msg) {
                if (!successBox || !errorBox) return;
                successBox.style.display = 'none';
                successBox.textContent = '';
                errorBox.style.display = 'block';
                errorBox.textContent = msg || '提交失败，请稍后重试';
            }

            function setPreview(file) {
                if (!file) return;

                var isImage = file.type && file.type.startsWith('image/');
                var maxBytes = 4 * 1024 * 1024;
                if (!isImage) {
                    alert('只支持图片文件');
                    input.value = '';
                    clearPreview();
                    return;
                }

                if (file.size > maxBytes) {
                    alert('图片大小不能超过 4MB');
                    input.value = '';
                    clearPreview();
                    return;
                }

                if (lastObjectUrl) {
                    try { URL.revokeObjectURL(lastObjectUrl); } catch (e) {}
                }

                lastObjectUrl = URL.createObjectURL(file);
                img.src = lastObjectUrl;
                meta.textContent = file.name + ' · ' + Math.max(1, Math.round(file.size / 1024)) + 'KB';
                wrap.style.display = 'block';
            }

            input.addEventListener('change', function () {
                var file = input.files && input.files[0];
                setPreview(file);
                if (!file) clearPreview();
            });

            if (removeBtn) {
                removeBtn.addEventListener('click', function () {
                    input.value = '';
                    clearPreview();
                });
            }

            // 拖拽上传
            if (dropZone) {
                var highlight = function (on) {
                    dropZone.style.borderColor = on ? 'rgba(165, 180, 252, 0.9)' : 'rgba(99, 102, 241, 0.45)';
                    dropZone.style.background = on ? 'rgba(99, 102, 241, 0.16)' : 'rgba(99, 102, 241, 0.08)';
                };

                ['dragenter', 'dragover'].forEach(function (evt) {
                    dropZone.addEventListener(evt, function (e) {
                        e.preventDefault();
                        highlight(true);
                    });
                });

                ['dragleave', 'drop'].forEach(function (evt) {
                    dropZone.addEventListener(evt, function (e) {
                        e.preventDefault();
                        highlight(false);
                    });
                });

                dropZone.addEventListener('drop', function (e) {
                    var file = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
                    if (!file) return;
                    // 某些浏览器不允许直接写入 input.files，因此使用 DataTransfer 复制一份
                    try {
                        var dt = new DataTransfer();
                        dt.items.add(file);
                        input.files = dt.files;
                    } catch (err) {
                        // 回退：至少更新预览（提交文件可能不带上）
                    }
                    setPreview(file);
                });
            }

            // Ajax 提交：避免整页刷新
            if (form) {
                form.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    setSubmitState(true);

                    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    var formData = new FormData(form);

                    try {
                        var resp = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: formData
                        });

                        var data = await resp.json().catch(function () { return {}; });
                        if (!resp.ok || data.success === false) {
                            if (data && data.errors) {
                                var firstError = Object.values(data.errors)[0];
                                if (Array.isArray(firstError) && firstError.length) {
                                    throw new Error(firstError[0]);
                                }
                            }
                            throw new Error(data.message || '提交失败，请检查输入内容');
                        }

                        showSuccess(data.message || '反馈提交成功！');
                        form.reset();
                        clearPreview();
                    } catch (err) {
                        showError(err && err.message ? err.message : '提交失败，请稍后重试');
                    } finally {
                        setSubmitState(false);
                    }
                });
            }
        })();
    </script>
@endsection

