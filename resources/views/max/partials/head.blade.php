{{-- MAX 风格统一样式头文件 --}}
<script src="https://cdn.tailwindcss.com"></script>
<style>
@include('max.partials.mobile-styles')
</style>
<style>
    /* 主色渐变 */
    .gradient-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    /* 渐变文字 */
    .gradient-text {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* 卡片悬停效果 */
    .card-hover {
        transition: all 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(102, 126, 234, 0.15);
    }
    
    /* 锁图标 */
    .lock-icon {
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
    }
    
    /* 脉冲发光效果 */
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(102, 126, 234, 0.5); }
        50% { box-shadow: 0 0 40px rgba(102, 126, 234, 0.8); }
    }
    .pulse-glow {
        animation: pulse-glow 2s infinite;
    }
    
    /* 输入框焦点效果 */
    .input-focus:focus {
        border-color: #9333ea;
        box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1);
    }
    
    /* 多行文本截断 */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
