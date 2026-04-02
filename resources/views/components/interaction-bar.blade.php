@props(['model', 'user' => null])

<div class="interaction-bar" style="display: flex; gap: 16px; align-items: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.1);">
    {{-- 点赞按钮 --}}
    <button onclick="toggleLike('{{ get_class($model) }}', {{ $model->id }})"
            style="
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 8px 16px;
                background: {{ $user && $user->hasLiked($model) ? 'rgba(239, 68, 68, 0.2)' : 'rgba(255, 255, 255, 0.05)' }};
                border: 1px solid {{ $user && $user->hasLiked($model) ? '#ef4444' : 'rgba(255, 255, 255, 0.1)' }};
                border-radius: 20px;
                color: {{ $user && $user->hasLiked($model) ? '#ef4444' : '#94a3b8' }};
                font-size: 14px;
                cursor: pointer;
                transition: all 0.2s;
            "
            id="like-btn-{{ $model->id }}">
        <span>{{ $user && $user->hasLiked($model) ? '❤️' : '🤍' }}</span>
        <span id="like-count-{{ $model->id }}">{{ $model->like_count ?? 0 }}</span>
    </button>

    {{-- 收藏按钮 --}}
    <button onclick="toggleFavorite('{{ get_class($model) }}', {{ $model->id }})"
            style="
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 8px 16px;
                background: {{ $user && $user->hasFavorited($model) ? 'rgba(234, 179, 8, 0.2)' : 'rgba(255, 255, 255, 0.05)' }};
                border: 1px solid {{ $user && $user->hasFavorited($model) ? '#eab308' : 'rgba(255, 255, 255, 0.1)' }};
                border-radius: 20px;
                color: {{ $user && $user->hasFavorited($model) ? '#eab308' : '#94a3b8' }};
                font-size: 14px;
                cursor: pointer;
                transition: all 0.2s;
            "
            id="favorite-btn-{{ $model->id }}">
        <span>{{ $user && $user->hasFavorited($model) ? '⭐' : '☆' }}</span>
        <span id="favorite-count-{{ $model->id }}">{{ $model->favorite_count ?? 0 }}</span>
    </button>

    {{-- 分享按钮 --}}
    <button onclick="shareContent('{{ get_class($model) }}', {{ $model->id }})"
            style="
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 8px 16px;
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 20px;
                color: #94a3b8;
                font-size: 14px;
                cursor: pointer;
                transition: all 0.2s;
            ">
        <span>🔗</span>
        <span>分享</span>
    </button>
</div>

<script>
function toggleLike(modelType, modelId) {
    fetch(`/interactions/${modelType.toLowerCase()}s/${modelId}/like`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const btn = document.getElementById(`like-btn-${modelId}`);
            const count = document.getElementById(`like-count-${modelId}`);
            
            if (data.liked) {
                btn.style.background = 'rgba(239, 68, 68, 0.2)';
                btn.style.borderColor = '#ef4444';
                btn.style.color = '#ef4444';
                btn.querySelector('span:first-child').textContent = '❤️';
            } else {
                btn.style.background = 'rgba(255, 255, 255, 0.05)';
                btn.style.borderColor = 'rgba(255, 255, 255, 0.1)';
                btn.style.color = '#94a3b8';
                btn.querySelector('span:first-child').textContent = '🤍';
            }
            count.textContent = data.count;
        } else if (data.message === '请先登录') {
            window.location.href = '/login';
        } else {
            alert(data.message);
        }
    })
    .catch(err => console.error(err));
}

function toggleFavorite(modelType, modelId) {
    fetch(`/interactions/${modelType.toLowerCase()}s/${modelId}/favorite`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const btn = document.getElementById(`favorite-btn-${modelId}`);
            const count = document.getElementById(`favorite-count-${modelId}`);
            
            if (data.favorited) {
                btn.style.background = 'rgba(234, 179, 8, 0.2)';
                btn.style.borderColor = '#eab308';
                btn.style.color = '#eab308';
                btn.querySelector('span:first-child').textContent = '⭐';
            } else {
                btn.style.background = 'rgba(255, 255, 255, 0.05)';
                btn.style.borderColor = 'rgba(255, 255, 255, 0.1)';
                btn.style.color = '#94a3b8';
                btn.querySelector('span:first-child').textContent = '☆';
            }
            count.textContent = data.count;
        } else if (data.message === '请先登录') {
            window.location.href = '/login';
        } else {
            alert(data.message);
        }
    })
    .catch(err => console.error(err));
}

function shareContent(modelType, modelId) {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        alert('链接已复制到剪贴板！');
    }).catch(() => {
        prompt('复制链接：', url);
    });
}
</script>
