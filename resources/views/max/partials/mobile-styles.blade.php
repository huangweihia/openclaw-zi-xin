/* MAX 风格 - 移动端响应式优化 */

/* ========== 基础断点 ========== */
/* 
  sm: 640px
  md: 768px
  lg: 1024px
  xl: 1280px
*/

/* ========== 移动端优化 ========== */

/* 导航栏移动端适配 */
@media (max-width: 768px) {
    /* 隐藏桌面导航 */
    nav .hidden.md\\:flex {
        display: none !important;
    }
    
    /* 显示移动端菜单按钮 */
    .mobile-menu-btn {
        display: block !important;
    }
    
    /* Hero Section 字体调整 */
    section.pt-32 h1 {
        font-size: 2rem !important; /* 32px */
    }
    
    section.pt-32 p {
        font-size: 1rem !important; /* 16px */
    }
    
    /* 卡片网格单列显示 */
    .grid.md\\:grid-cols-2,
    .grid.md\\:grid-cols-3,
    .grid.lg\\:grid-cols-3,
    .grid.lg\\:grid-cols-4 {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
    }
    
    /* 按钮全宽显示 */
    .flex.flex-col.sm\\:flex-row {
        flex-direction: column !important;
    }
    
    .flex.flex-col.sm\\:flex-row > a,
    .flex.flex-col.sm\\:flex-row > button {
        width: 100% !important;
    }
    
    /* 表格滚动 */
    .overflow-x-auto {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    /* 图片自适应 */
    img {
        max-width: 100%;
        height: auto;
    }
}

/* ========== 平板优化 ========== */
@media (min-width: 768px) and (max-width: 1024px) {
    .grid.lg\\:grid-cols-3,
    .grid.lg\\:grid-cols-4 {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}

/* ========== 触摸优化 ========== */

/* 移除移动端点击高亮 */
* {
    -webkit-tap-highlight-color: transparent;
}

/* 触摸反馈 */
.touch-feedback:active {
    transform: scale(0.98);
    opacity: 0.9;
}

/* 按钮触摸区域优化 */
button, a {
    min-height: 44px; /* iOS 推荐最小触摸区域 */
}

/* ========== 加载动画 ========== */

/* 骨架屏动画 */
@keyframes skeleton-loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

.skeleton {
    background: linear-gradient(
        90deg,
        #f0f0f0 25%,
        #e0e0e0 50%,
        #f0f0f0 75%
    );
    background-size: 200% 100%;
    animation: skeleton-loading 1.5s infinite;
    border-radius: 8px;
}

.skeleton-text {
    height: 16px;
    margin-bottom: 8px;
}

.skeleton-title {
    height: 24px;
    width: 60%;
    margin-bottom: 16px;
}

.skeleton-card {
    height: 200px;
    border-radius: 16px;
}

/* 旋转加载器 */
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #e5e7eb;
    border-top-color: #9333ea;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

/* 淡入动画 */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in {
    animation: fadeIn 0.5s ease-out;
}

/* 滑动入场动画 */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.slide-in {
    animation: slideIn 0.4s ease-out;
}

/* ========== 滚动优化 ========== */

/* 平滑滚动 */
html {
    scroll-behavior: smooth;
}

/* 自定义滚动条 */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}

/* ========== 图片懒加载 ========== */

img.lazy {
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
}

img.lazy.loaded {
    opacity: 1;
}

/* ========== 移动端特定组件 ========== */

/* 底部导航栏（移动端） */
.mobile-bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-around;
    padding: 8px 0;
    z-index: 50;
}

.mobile-bottom-nav a {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: #6b7280;
    font-size: 12px;
}

.mobile-bottom-nav a.active {
    color: #9333ea;
}

.mobile-bottom-nav .icon {
    font-size: 24px;
    margin-bottom: 4px;
}

/* 移动端搜索栏 */
.mobile-search {
    position: sticky;
    top: 0;
    background: white;
    padding: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    z-index: 40;
}

/* 移动端筛选器（抽屉式） */
.mobile-filter-drawer {
    position: fixed;
    top: 0;
    right: -100%;
    width: 100%;
    height: 100vh;
    background: white;
    z-index: 100;
    transition: right 0.3s ease;
    overflow-y: auto;
}

.mobile-filter-drawer.open {
    right: 0;
}

/* 移动端卡片优化 */
.mobile-card {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

/* ========== 手势支持 ========== */

/* 左滑返回指示器 */
.swipe-back-area {
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    width: 20px;
    z-index: 1000;
}

/* 下拉刷新指示器 */
.pull-to-refresh {
    text-align: center;
    padding: 10px;
    color: #9333ea;
    font-size: 14px;
}

/* ========== 性能优化 ========== */

/* 硬件加速 */
.gpu-accelerated {
    transform: translateZ(0);
    will-change: transform;
}

/* 减少动画对性能的影响 */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
