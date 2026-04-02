<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="AI 副业情报局 - 用 AI 赋能副业，让赚钱变得更简单">
    <title>@yield('title', config('app.name', 'AI 副业情报局'))</title>
    
    <script src="{{ asset('js/ui-components.js') }}" defer></script>
    <style>
        /* ========== 默认皮肤：深空蓝 ========== */
        :root {
            --site-marquee-offset: 0px;
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #818cf8;
            --secondary: #ec4899;
            --accent: #06b6d4;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #0f172a;
            --dark-light: #1e293b;
            --gray: #64748b;
            --gray-light: #94a3b8;
            --light: #f1f5f9;
            --white: #ffffff;
            --gradient-primary: linear-gradient(135deg, #6366f1 0%, #ec4899 100%);
            --gradient-dark: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --radius: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
        }

        /* ========== 皮肤 2：极简白 ========== */
        body.skin-light {
            --dark: #f8fafc;
            --dark-light: #ffffff;
            --gray: #94a3b8;
            --gray-light: #64748b;
            --white: #1e293b;
            --gradient-dark: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        }
        body.skin-light .navbar {
            background: rgba(255, 255, 255, 0.9);
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }
        body.skin-light .card {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.08);
        }

        /* ========== 皮肤 3：暗夜黑 ========== */
        body.skin-dark {
            --dark: #000000;
            --dark-light: #0a0a0a;
            --gray: #404040;
            --gray-light: #737373;
            --white: #ffffff;
            --gradient-dark: linear-gradient(135deg, #000000 0%, #0a0a0a 100%);
        }

        /* ========== 皮肤 4：护眼绿 ========== */
        body.skin-green {
            --dark: #0c1a12;
            --dark-light: #14291f;
            --gray: #5c7c70;
            --gray-light: #8ba89a;
            --white: #e8f5e9;
            --gradient-dark: linear-gradient(135deg, #0c1a12 0%, #14291f 100%);
        }

        /* ========== 皮肤 5：暖棕咖啡 ========== */
        body.skin-brown {
            --dark: #1a1512;
            --dark-light: #2a2218;
            --gray: #8b7355;
            --gray-light: #b8a082;
            --white: #f5ebe0;
            --gradient-dark: linear-gradient(135deg, #1a1512 0%, #2a2218 100%);
        }

        body.has-site-marquee {
            --site-marquee-offset: 38px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'PingFang SC', 'Hiragino Sans GB', 'Microsoft YaHei', sans-serif;
            background: var(--dark);
            color: var(--white);
            line-height: 1.6;
            overflow-x: hidden;
            transition: all 0.3s ease;
        }

        a { text-decoration: none; color: inherit; transition: all 0.2s ease; }
        ul { list-style: none; }
        img { max-width: 100%; display: block; }

        /* Container */
        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }

        /* 顶部滚动公告：贴在主导航下方（关闭后仅本页隐藏，刷新后仍会出现） */
        .site-marquee-bar {
            position: fixed;
            top: 80px;
            left: 0;
            right: 0;
            height: 38px;
            z-index: 999;
            display: flex;
            align-items: stretch;
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.35), rgba(236, 72, 153, 0.25));
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            font-size: 13px;
        }
        .site-marquee-bar a.site-marquee-link {
            flex: 1 1 0%;
            min-width: 0;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            overflow: hidden;
            color: var(--white);
            padding: 0 8px 0 12px;
            text-align: left;
        }
        /* min-width:0 关键：否则内部超长一行会撑满 min-content，裁剪区像只在中间一条在滚 */
        .site-marquee-bar .site-marquee-viewport {
            flex: 1 1 0%;
            min-width: 0;
            width: 100%;
            max-width: 100%;
            overflow: hidden;
            text-align: left;
        }
        .site-marquee-bar .site-marquee-track {
            display: inline-flex;
            flex-wrap: nowrap;
            align-items: center;
            flex-shrink: 0;
            white-space: nowrap;
            will-change: transform;
            /* 无 JS 时退化为一次性左移；有 JS 后切换为从视口右侧入场 */
            animation: site-marquee-fallback 20s linear 1 forwards;
        }
        .site-marquee-bar .site-marquee-track.is-marquee-ready {
            animation-name: site-marquee-x;
            animation-timing-function: linear;
            animation-iteration-count: 1;
            animation-fill-mode: forwards;
        }
        .site-marquee-bar .site-marquee-chunk,
        .site-marquee-bar .site-marquee-sep {
            flex-shrink: 0;
        }
        @keyframes site-marquee-fallback {
            0% { transform: translate3d(0, 0, 0); }
            100% { transform: translate3d(-100%, 0, 0); }
        }
        /* --mx-start/--mx-end 为 px，由脚本写入：完整滚过头部后结束 */
        @keyframes site-marquee-x {
            0% { transform: translate3d(var(--mx-start), 0, 0); }
            100% { transform: translate3d(var(--mx-end), 0, 0); }
        }
        .site-marquee-bar .site-marquee-close {
            flex-shrink: 0;
            width: 40px;
            border: none;
            background: rgba(0, 0, 0, 0.2);
            color: var(--gray-light);
            cursor: pointer;
            font-size: 20px;
            line-height: 1;
            z-index: 2;
        }
        .site-marquee-bar .site-marquee-close:hover {
            color: var(--white);
            background: rgba(0, 0, 0, 0.35);
        }
        /* 无广告：主区仍居中 1200px */
        .layout-page-grid:not(.layout-with-sidebar) {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px 48px;
        }
        /* 有广告：主内容仍居中，广告改为浮动窗口，不占主内容栅格 */
        .layout-page-grid.layout-with-sidebar {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px 48px;
        }
        .layout-page-grid.layout-with-sidebar .layout-page-primary {
            min-width: 0;
        }
        .layout-page-grid.layout-with-sidebar .site-ad-sidebar {
            position: fixed;
            right: 16px;
            top: calc(80px + var(--site-marquee-offset, 0px) + 12px);
            z-index: 1200;
            width: min(320px, calc(100vw - 24px));
            max-height: calc(100vh - 110px);
            overflow: auto;
        }
        .layout-page-grid.layout-with-sidebar .site-ad-sidebar-card {
            backdrop-filter: blur(8px);
        }
        @media (max-width: 1199px) {
            .layout-page-grid.layout-with-sidebar {
                padding-left: 24px;
                padding-right: 24px;
            }
            .layout-page-grid.layout-with-sidebar .layout-page-primary {
                min-width: 0;
            }
            .layout-page-grid.layout-with-sidebar .site-ad-sidebar {
                right: 8px;
                width: min(300px, calc(100vw - 16px));
                max-height: none;
                overflow: visible;
            }
        }
        .layout-page-primary {
            min-width: 0;
            width: 100%;
        }
        .site-ad-sidebar-card {
            border-radius: var(--radius);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: var(--dark-light);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            max-width: 100%;
            min-width: 280px;
        }
        .site-ad-sidebar-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 10px;
            background: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }
        .site-ad-sidebar-head-label {
            font-size: 10px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--gray-light);
            opacity: 0.85;
        }
        .site-ad-sidebar-close {
            border: none;
            background: transparent;
            color: var(--gray-light);
            cursor: pointer;
            font-size: 20px;
            line-height: 1;
            padding: 2px 6px;
            border-radius: 6px;
            transition: color 0.15s, background 0.15s;
        }
        .site-ad-sidebar-close:hover {
            color: var(--white);
            background: rgba(255, 255, 255, 0.08);
        }
        .site-ad-sidebar-body {
            padding: 10px;
            position: relative;
            background: rgba(15, 23, 42, 0.35);
        }
        .skin-light .site-ad-sidebar-body {
            background: #fff;
        }
        .site-ad-sidebar-meta {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 6px;
        }
        .site-ad-sidebar-badge {
            font-size: 10px;
            font-weight: 600;
            color: #fff;
            background: #16a34a;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .site-ad-sidebar-img {
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 10px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .site-ad-sidebar-img img {
            width: 100%;
            min-height: 260px;
            max-height: 440px;
            height: auto;
            object-fit: cover;
            display: block;
        }
        .site-ad-sidebar-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--white);
            margin: 0 0 6px;
            line-height: 1.35;
        }
        .skin-light .site-ad-sidebar-title {
            color: #0f172a;
        }
        .site-ad-sidebar-text {
            font-size: 12px;
            line-height: 1.5;
            color: var(--gray-light);
            margin-bottom: 10px;
        }
        .skin-light .site-ad-sidebar-text {
            color: #475569;
        }
        .site-ad-sidebar-cta {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            background: var(--primary);
            text-decoration: none;
            transition: filter 0.15s, transform 0.15s;
        }
        .site-ad-sidebar-cta:hover {
            filter: brightness(1.08);
            transform: translateY(-1px);
            color: #fff;
        }

        .skin-panel-floating {
            position: fixed;
            top: calc(80px + var(--site-marquee-offset, 0px));
            right: 20px;
            z-index: 100000;
        }

        /* Navigation */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 24px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 20px;
            font-weight: 700;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .navbar-brand-icon {
            font-size: 28px;
        }

        .navbar-menu {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .navbar-link {
            padding: 10px 18px;
            border-radius: var(--radius);
            color: var(--gray-light);
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .navbar-link:hover {
            color: var(--white);
            background: rgba(255, 255, 255, 0.1);
        }

        .navbar-link.active {
            color: var(--white);
            background: var(--primary);
        }

        /* 个人中心头像入口 */
        .nav-avatar-link {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            overflow: hidden;
            text-decoration: none;
        }

        .nav-avatar-link img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .nav-avatar-fallback {
            font-size: 18px;
            font-weight: 900;
            color: #fff;
            letter-spacing: 0.02em;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: var(--radius);
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            outline: none;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: var(--white);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.5);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--white);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .btn-sm { padding: 8px 16px; font-size: 13px; }
        .btn-lg { padding: 16px 32px; font-size: 16px; }

        /* Main Content */
        .main { padding-top: calc(80px + var(--site-marquee-offset, 0px)); min-height: 100vh; }

        /* Hero Section */
        .hero {
            position: relative;
            padding: 120px 0 100px;
            text-align: center;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: 50%;
            transform: translateX(-50%);
            width: 1000px;
            height: 1000px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 50px;
            font-size: 13px;
            color: var(--primary-light);
            margin-bottom: 24px;
        }

        .hero-title {
            font-size: clamp(36px, 5vw, 64px);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 20px;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: clamp(16px, 2vw, 20px);
            color: var(--gray-light);
            max-width: 600px;
            margin: 0 auto 40px;
            line-height: 1.8;
        }

        .hero-cta {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 60px;
            flex-wrap: wrap;
        }

        .hero-stat {
            text-align: center;
        }

        .hero-stat-value {
            font-size: 36px;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-stat-label {
            font-size: 14px;
            color: var(--gray-light);
            margin-top: 4px;
        }

        /* Features Section */
        .features {
            padding: 100px 0;
            background: var(--dark-light);
        }

        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-label {
            display: inline-block;
            padding: 6px 14px;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            color: var(--primary-light);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 16px;
        }

        .section-title {
            font-size: clamp(28px, 4vw, 40px);
            font-weight: 700;
            margin-bottom: 16px;
        }

        .section-desc {
            font-size: 16px;
            color: var(--gray-light);
            max-width: 500px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
        }

        .feature-card {
            background: var(--dark);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-lg);
            padding: 32px;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            border-color: rgba(99, 102, 241, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--gradient-primary);
            border-radius: var(--radius);
            font-size: 26px;
            margin-bottom: 20px;
        }

        .feature-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .feature-desc {
            color: var(--gray-light);
            line-height: 1.8;
            font-size: 15px;
        }

        /* Cards */
        .card {
            background: var(--dark);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-lg);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            border-color: rgba(99, 102, 241, 0.3);
            box-shadow: var(--shadow-xl);
        }

        .card-image {
            height: 200px;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 64px;
        }

        .card-body { padding: 24px; }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .card-desc {
            color: var(--gray-light);
            font-size: 14px;
            line-height: 1.8;
            margin-bottom: 16px;
        }

        .card-meta {
            display: flex;
            gap: 16px;
            font-size: 13px;
            color: var(--gray);
        }

        .card-tag {
            display: inline-block;
            padding: 4px 12px;
            background: rgba(99, 102, 241, 0.1);
            border-radius: 50px;
            font-size: 12px;
            color: var(--primary-light);
            font-weight: 500;
        }

        /* Footer */
        .footer {
            background: var(--dark-light);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 60px 0 30px;
            margin-top: 100px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-brand {
            font-size: 20px;
            font-weight: 700;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 16px;
        }

        .footer-desc {
            color: var(--gray-light);
            font-size: 14px;
            line-height: 1.8;
            max-width: 300px;
        }

        .footer-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 16px;
            color: var(--white);
        }

        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .footer-link {
            color: var(--gray-light);
            font-size: 14px;
            transition: color 0.2s ease;
        }

        .footer-link:hover { color: var(--primary-light); }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--gray);
            font-size: 13px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar-menu { display: none; }
            .hero { padding: 80px 0 60px; }
            .hero-stats { gap: 20px; }
            .features-grid { grid-template-columns: 1fr; }
            .footer-content { grid-template-columns: 1fr; gap: 30px; }
        }

        /* Pagination: 美化 + 防止在未加载 Tailwind/Bootstrap 时，pagination 里的 svg 退化成超大尺寸 */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin: 0;
            padding: 0;
            flex-wrap: wrap;
        }
        .page-item .page-link {
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.12);
            background: rgba(255,255,255,0.06);
            color: var(--gray-light);
            padding: 8px 12px;
            font-weight: 600;
            line-height: 1.2;
        }
        .page-item .page-link:hover {
            background: rgba(99,102,241,0.18);
            border-color: rgba(99,102,241,0.35);
            color: var(--white);
        }
        .page-item.active .page-link {
            background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%);
            border-color: rgba(99,102,241,0.55);
            color: #fff;
        }
        .page-item.disabled .page-link {
            opacity: 0.45;
            cursor: not-allowed;
        }
        .page-item .page-link:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.18);
        }
        
        nav[aria-label="pagination"] a,
        nav[aria-label="pagination"] span,
        nav[aria-label*="Pagination"] a,
        nav[aria-label*="Pagination"] span {
            font-size: 14px;
            line-height: 1.2;
        }
        nav[aria-label="pagination"] svg,
        nav[aria-label*="Pagination"] svg {
            width: 14px;
            height: 14px;
            display: inline-block;
        }
        /* tailwind 的 sr-only 兼容：把隐藏文本真正隐藏，避免占位/放大 */
        .sr-only {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border-width: 0 !important;
        }

        /* Forms */
        .form-group { margin-bottom: 20px; }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--gray-light);
        }

        .form-input {
            width: 100%;
            padding: 14px 18px;
            background: var(--dark-light);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius);
            color: var(--white);
            font-size: 15px;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .form-input::placeholder { color: var(--gray); }

        /* Alert */
        .alert {
            padding: 16px 20px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }

        /*
         * 富文本（知识库/文章/公告/广告 HTML）：正文用灰字，标题用主题字；
         * 对常见「浅色背景」内联样式强制深色字，避免继承 body 浅色导致白底白字。
         */
        .rich-html-content,
        .site-ad-sidebar-html {
            color: var(--gray-light);
            font-size: 15px;
            line-height: 1.85;
            word-break: break-word;
        }
        .rich-html-content h1,
        .rich-html-content h2,
        .rich-html-content h3,
        .rich-html-content h4,
        .rich-html-content h5,
        .rich-html-content h6,
        .site-ad-sidebar-html h1,
        .site-ad-sidebar-html h2,
        .site-ad-sidebar-html h3,
        .site-ad-sidebar-html h4,
        .site-ad-sidebar-html h5,
        .site-ad-sidebar-html h6 {
            color: var(--white);
            margin-top: 1.15em;
            margin-bottom: 0.45em;
            line-height: 1.35;
        }
        .rich-html-content p,
        .site-ad-sidebar-html p {
            margin: 0.65em 0;
        }
        .rich-html-content a,
        .site-ad-sidebar-html a {
            color: var(--primary-light);
        }
        .rich-html-content img,
        .site-ad-sidebar-html img {
            max-width: 100%;
            height: auto;
        }
        .rich-html-content pre,
        .rich-html-content code,
        .site-ad-sidebar-html pre,
        .site-ad-sidebar-html code {
            background: rgba(15, 23, 42, 0.55);
            color: #e2e8f0;
            border-radius: 6px;
        }
        .rich-html-content pre,
        .site-ad-sidebar-html pre {
            padding: 12px 14px;
            overflow-x: auto;
        }
        .rich-html-content code,
        .site-ad-sidebar-html code {
            padding: 2px 6px;
            font-size: 0.9em;
        }
        body.skin-light .rich-html-content pre,
        body.skin-light .rich-html-content code,
        body.skin-light .site-ad-sidebar-html pre,
        body.skin-light .site-ad-sidebar-html code {
            background: #f1f5f9;
            color: #0f172a;
        }

        /* 深色皮肤下，知识库文档正文内若含内联深色字体，统一提亮（避免几乎看不见） */
        body.skin-dark .knowledge-doc-body,
        body.skin-dark .knowledge-doc-body * {
            color: #e5e7eb !important;
        }

        /* 内联 style 含浅色背景时，强制深色正文（覆盖继承的浅色字） */
        .rich-html-content [style*="background:#fff"],
        .rich-html-content [style*="background: #fff"],
        .rich-html-content [style*="background:#ffffff"],
        .rich-html-content [style*="background: #ffffff"],
        .rich-html-content [style*="background:#f8fafc"],
        .rich-html-content [style*="background: #f8fafc"],
        .rich-html-content [style*="background:#f1f5f9"],
        .rich-html-content [style*="background: #f1f5f9"],
        .rich-html-content [style*="background:#f0f9ff"],
        .rich-html-content [style*="background: #f0f9ff"],
        .rich-html-content [style*="background:#f0f7ff"],
        .rich-html-content [style*="background: #f0f7ff"],
        .rich-html-content [style*="background:#e0f7fa"],
        .rich-html-content [style*="background: #e0f7fa"],
        .rich-html-content [style*="background:#e0f2fe"],
        .rich-html-content [style*="background: #e0f2fe"],
        .rich-html-content [style*="background:#ecfdf5"],
        .rich-html-content [style*="background: #ecfdf5"],
        .rich-html-content [style*="background:#fef3c7"],
        .rich-html-content [style*="background: #fef3c7"],
        .rich-html-content [style*="background:#fefce8"],
        .rich-html-content [style*="background: #fefce8"],
        .rich-html-content [style*="background:rgb(255"],
        .rich-html-content [style*="background: rgb(255"],
        .rich-html-content [style*="background:rgba(255, 255, 255"],
        .rich-html-content [style*="background: rgba(255, 255, 255"],
        .site-ad-sidebar-html [style*="background:#fff"],
        .site-ad-sidebar-html [style*="background: #fff"],
        .site-ad-sidebar-html [style*="background:#f0f7ff"],
        .site-ad-sidebar-html [style*="background: #f0f7ff"],
        .site-ad-sidebar-html [style*="background:#e0f7fa"],
        .site-ad-sidebar-html [style*="background: #e0f7fa"],
        .site-ad-sidebar-html [style*="background:rgb(255"],
        .site-ad-sidebar-html [style*="background: rgb(255"] {
            color: #0f172a !important;
        }
        .rich-html-content [style*="background:#f0f7ff"] a,
        .rich-html-content [style*="background: #f0f7ff"] a,
        .rich-html-content [style*="background:#e0f7fa"] a,
        .rich-html-content [style*="background: #e0f7fa"] a,
        .rich-html-content [style*="background:#fff"] a,
        .rich-html-content [style*="background: #fff"] a {
            color: #4f46e5 !important;
        }

        /* 项目页等：白底卡片内富文本，标题不能用 var(--white) */
        .project-description-body.rich-html-content {
            color: #475569 !important;
        }
        .project-description-body.rich-html-content h1,
        .project-description-body.rich-html-content h2,
        .project-description-body.rich-html-content h3,
        .project-description-body.rich-html-content h4,
        .project-description-body.rich-html-content h5,
        .project-description-body.rich-html-content h6 {
            color: #0f172a !important;
        }
        .project-description-body.rich-html-content a {
            color: #4f46e5 !important;
        }
    </style>
</head>
<body @class(['has-site-marquee' => isset($marqueeAnnouncements) && $marqueeAnnouncements->isNotEmpty()])>
    <nav class="navbar">
        <div class="navbar-content">
            <a href="{{ route('home') }}" class="navbar-brand">
                <span class="navbar-brand-icon">🤖</span>
                <span>AI 副业情报局</span>
            </a>
            
            <div class="navbar-menu">
                {{-- 公开导航 --}}
                <a href="{{ route('home') }}" class="navbar-link {{ request()->routeIs('home') ? 'active' : '' }}">首页</a>
                
                {{-- 内容分类导航 --}}
                <a href="{{ route('projects.index') }}" class="navbar-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">🚀 项目</a>
                <a href="{{ route('articles.index') }}" class="navbar-link {{ request()->routeIs('articles.*') ? 'active' : '' }}">📝 文章</a>
                <a href="{{ route('knowledge.index') }}" class="navbar-link {{ request()->routeIs('knowledge.*') ? 'active' : '' }}">📚 知识库</a>
                <a href="{{ route('jobs.index') }}" class="navbar-link {{ request()->routeIs('jobs.*') ? 'active' : '' }}">💼 职位</a>
                
                {{-- 用户功能 --}}
                @auth
                    {{-- 个人中心：头像入口 --}}
                    <a href="{{ route('dashboard') }}" class="nav-avatar-link" title="个人中心">
                        @if(auth()->user()?->avatar)
                            <img src="{{ auth()->user()->avatar }}" alt="用户头像">
                        @else
                            <span class="nav-avatar-fallback">{{ substr(auth()->user()->name ?? '', 0, 1) }}</span>
                        @endif
                    </a>
                    {{-- VIP 仍保留在顶部栏（外部菜单），其余功能在个人中心中集中展示 --}}
                    <a href="{{ route('vip') }}" class="btn btn-sm btn-primary" style="margin-left: 10px;">👑 VIP 会员</a>
                    <button type="button" data-skin-toggle class="btn btn-sm btn-secondary" style="padding: 8px 12px; font-size: 16px; position: relative; z-index: 10000;" title="切换皮肤">🎨</button>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary">登出</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="navbar-link">登录</a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-primary">免费注册</a>
                    <a href="{{ route('vip') }}" class="btn btn-sm btn-primary" style="margin-left: 10px;">👑 VIP 会员</a>
                    <button type="button" data-skin-toggle class="btn btn-sm btn-secondary" style="padding: 8px 12px; font-size: 16px; position: relative; z-index: 10000;" title="切换皮肤">🎨</button>
                @endauth
            </div>
        </div>
    </nav>

    @if(isset($marqueeAnnouncements) && $marqueeAnnouncements->isNotEmpty())
        @php
            $marqueeFirst = $marqueeAnnouncements->first();
            $marqueeLine = $marqueeAnnouncements->map(fn ($a) => $a->marquee_line)->implode('　｜　');
        @endphp
        <div id="site-marquee-bar" class="site-marquee-bar" role="region" aria-label="站点公告">
            <a href="{{ route('announcements.show', $marqueeFirst->slug) }}" class="site-marquee-link">
                <div class="site-marquee-viewport">
                    <div class="site-marquee-track">
                        <span class="site-marquee-chunk">{{ $marqueeLine }}</span>
                    </div>
                </div>
            </a>
            <button type="button" class="site-marquee-close" id="site-marquee-close" aria-label="关闭公告条">×</button>
        </div>
        <script>
        (function () {
            var btn = document.getElementById('site-marquee-close');
            var bar = document.getElementById('site-marquee-bar');
            if (!btn || !bar) return;

            // 公告关闭缓存：10 分钟内不再弹出（按用户区分；未登录则按浏览器本地匿名 ID 区分）
            var TEN_MIN_MS = 10 * 60 * 1000;
            function getLeWanCacheUserId() {
                try {
                    var uid = @json(auth()->check() ? auth()->id() : null);
                    if (uid !== null && uid !== undefined && uid !== '') {
                        return 'u:' + uid;
                    }
                    var anonKey = 'lewan:anon_user_id';
                    var anonId = localStorage.getItem(anonKey);
                    if (!anonId) {
                        anonId = 'a_' + Math.random().toString(36).slice(2) + '_' + Date.now();
                        localStorage.setItem(anonKey, anonId);
                    }
                    return 'g:' + anonId;
                } catch (e) {
                    return 'g:local';
                }
            }

            var cacheKey = 'lewan:closed:site_marquee:' + getLeWanCacheUserId();
            try {
                var raw = localStorage.getItem(cacheKey);
                if (raw) {
                    var obj = JSON.parse(raw);
                    var ts = Number(obj && obj.ts ? obj.ts : 0);
                    if (ts && (Date.now() - ts) < TEN_MIN_MS) {
                        bar.remove();
                        return;
                    }
                }
            } catch (e) {}

            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                try {
                    localStorage.setItem(cacheKey, JSON.stringify({ ts: Date.now() }));
                } catch (err) {}

                bar.remove();
                document.body.classList.remove('has-site-marquee');
            });
            var vp = bar.querySelector('.site-marquee-viewport');
            var track = bar.querySelector('.site-marquee-track');
            if (!vp || !track) return;
            var resizeTimer;
            function updateMarqueeMetrics() {
                var w = vp.offsetWidth;
                var tw = track.scrollWidth;
                if (w < 2 || tw < 2) {
                    track.classList.remove('is-marquee-ready');
                    return;
                }
                var startPx = w;
                var endPx = -tw;
                track.style.setProperty('--mx-start', startPx + 'px');
                track.style.setProperty('--mx-end', endPx + 'px');
                track.style.animationDuration = Math.max(10, (w + tw) / 80) + 's';
                track.classList.add('is-marquee-ready');
            }
            function scheduleUpdate() {
                updateMarqueeMetrics();
                requestAnimationFrame(function () {
                    updateMarqueeMetrics();
                });
            }
            scheduleUpdate();
            window.addEventListener('resize', function () {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(scheduleUpdate, 120);
            });
            track.addEventListener('animationend', function () {
                if (!bar || !bar.parentNode) {
                    return;
                }
                bar.remove();
                document.body.classList.remove('has-site-marquee');
            });
        })();
        </script>
    @endif

    {{-- 皮肤切换面板 --}}
    <div id="skin-panel" class="skin-panel-floating" style="display: none; background: var(--dark-light); border: 1px solid rgba(255,255,255,0.15); border-radius: 16px; padding: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.4); min-width: 200px;" onclick="event.stopPropagation()">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.08);">
            <span style="font-weight: 700; color: var(--white); font-size: 14px;">🎨 选择皮肤</span>
            <button onclick="toggleSkinPanel()" style="background: transparent; border: none; color: var(--gray-light); font-size: 20px; cursor: pointer; padding: 0; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'; this.style.color='var(--white)'" onmouseout="this.style.background='transparent'; this.style.color='var(--gray-light)'">×</button>
        </div>
        <div style="display: grid; gap: 8px;">
            <button onclick="setSkin('')" style="padding: 10px 14px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border: 2px solid rgba(99, 102, 241, 0.3); border-radius: 10px; color: #fff; cursor: pointer; font-weight: 600; font-size: 13px; text-align: left; transition: all 0.2s; display: flex; align-items: center; gap: 8px;" onmouseover="this.style.borderColor='#667eea'; this.style.transform='translateX(4px)'" onmouseout="this.style.borderColor='rgba(99, 102, 241, 0.3)'; this.style.transform='translateX(0)'">
                <span style="width: 16px; height: 16px; background: #0f172a; border-radius: 50%; border: 2px solid #667eea;"></span>
                深空蓝 (默认)
            </button>
            <button onclick="setSkin('skin-light')" style="padding: 10px 14px; background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%); border: 2px solid rgba(0, 0, 0, 0.1); border-radius: 10px; color: #1e293b; cursor: pointer; font-weight: 600; font-size: 13px; text-align: left; transition: all 0.2s; display: flex; align-items: center; gap: 8px;" onmouseover="this.style.borderColor='#667eea'; this.style.transform='translateX(4px)'" onmouseout="this.style.borderColor='rgba(0, 0, 0, 0.1)'; this.style.transform='translateX(0)'">
                <span style="width: 16px; height: 16px; background: #f8fafc; border-radius: 50%; border: 2px solid #cbd5e1;"></span>
                极简白
            </button>
            <button onclick="setSkin('skin-dark')" style="padding: 10px 14px; background: linear-gradient(135deg, #000000 0%, #0a0a0a 100%); border: 2px solid rgba(255, 255, 255, 0.2); border-radius: 10px; color: #fff; cursor: pointer; font-weight: 600; font-size: 13px; text-align: left; transition: all 0.2s; display: flex; align-items: center; gap: 8px;" onmouseover="this.style.borderColor='#667eea'; this.style.transform='translateX(4px)'" onmouseout="this.style.borderColor='rgba(255, 255, 255, 0.2)'; this.style.transform='translateX(0)'">
                <span style="width: 16px; height: 16px; background: #000000; border-radius: 50%; border: 2px solid #404040;"></span>
                暗夜黑
            </button>
            <button onclick="setSkin('skin-green')" style="padding: 10px 14px; background: linear-gradient(135deg, #0c1a12 0%, #14291f 100%); border: 2px solid rgba(16, 185, 129, 0.3); border-radius: 10px; color: #e8f5e9; cursor: pointer; font-weight: 600; font-size: 13px; text-align: left; transition: all 0.2s; display: flex; align-items: center; gap: 8px;" onmouseover="this.style.borderColor='#10b981'; this.style.transform='translateX(4px)'" onmouseout="this.style.borderColor='rgba(16, 185, 129, 0.3)'; this.style.transform='translateX(0)'">
                <span style="width: 16px; height: 16px; background: #0c1a12; border-radius: 50%; border: 2px solid #10b981;"></span>
                护眼绿
            </button>
            <button onclick="setSkin('skin-brown')" style="padding: 10px 14px; background: linear-gradient(135deg, #1a1512 0%, #2a2218 100%); border: 2px solid rgba(184, 160, 130, 0.3); border-radius: 10px; color: #f5ebe0; cursor: pointer; font-weight: 600; font-size: 13px; text-align: left; transition: all 0.2s; display: flex; align-items: center; gap: 8px;" onmouseover="this.style.borderColor='#b8a082'; this.style.transform='translateX(4px)'" onmouseout="this.style.borderColor='rgba(184, 160, 130, 0.3)'; this.style.transform='translateX(0)'">
                <span style="width: 16px; height: 16px; background: #1a1512; border-radius: 50%; border: 2px solid #b8a082;"></span>
                暖棕咖啡
            </button>
        </div>
    </div>

    @php
        $showAdSidebar = isset($adSlot) && $adSlot->shouldDisplaySidebar() && ! ($hideGlobalAdSlot ?? false);
    @endphp
    <main class="main">
        <div class="layout-page-grid{{ $showAdSidebar ? ' layout-with-sidebar' : '' }}">
            <div class="layout-page-primary">
                @yield('content')
            </div>
            @if($showAdSidebar)
                <aside id="site-ad-sidebar" class="site-ad-sidebar" aria-label="推广">
                    <div class="site-ad-sidebar-card">
                        <div class="site-ad-sidebar-head">
                            <span class="site-ad-sidebar-head-label">推广</span>
                            <button type="button" class="site-ad-sidebar-close" onclick="closeSiteAdSidebar()" aria-label="关闭">×</button>
                        </div>
                        <div class="site-ad-sidebar-body">
                            @if($adSlot->display_mode === 'html' && filled($adSlot->html_content))
                                <div class="site-ad-sidebar-html rich-html-content">{!! $adSlot->html_content !!}</div>
                            @else
                                <div class="site-ad-sidebar-meta">
                                    <span class="site-ad-sidebar-badge">广告</span>
                                </div>
                                @php $adResolvedImg = $adSlot->resolvedImageUrl(); @endphp
                                @if($adResolvedImg)
                                    <div class="site-ad-sidebar-img">
                                        @if($adSlot->link_url)
                                            <a href="{{ $adSlot->link_url }}" target="_blank" rel="noopener noreferrer">
                                                <img src="{{ $adResolvedImg }}" alt="">
                                            </a>
                                        @else
                                            <img src="{{ $adResolvedImg }}" alt="">
                                        @endif
                                    </div>
                                @endif
                                @if(filled($adSlot->title))
                                    <h3 class="site-ad-sidebar-title">{{ $adSlot->title }}</h3>
                                @endif
                                @if(filled($adSlot->body))
                                    <div class="site-ad-sidebar-text">{!! nl2br(e($adSlot->body)) !!}</div>
                                @endif
                                @if(filled($adSlot->cta_label) && filled($adSlot->link_url))
                                    <a href="{{ $adSlot->link_url }}" class="site-ad-sidebar-cta" target="_blank" rel="noopener noreferrer">{{ $adSlot->cta_label }} <span aria-hidden="true">›</span></a>
                                @endif
                            @endif
                        </div>
                    </div>
                </aside>
            @endif
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div>
                    <div class="footer-brand">🤖 AI 副业情报局</div>
                    <p class="footer-desc">用 AI 赋能副业，让赚钱变得更简单。每天推送最新 AI 项目、变现灵感和学习资源。</p>
                </div>
                <div>
                    <div class="footer-title">产品</div>
                    <div class="footer-links">
                        <a href="{{ route('projects.index') }}" class="footer-link">项目库</a>
                        <a href="{{ route('articles.index') }}" class="footer-link">文章</a>
                        <a href="{{ route('vip') }}" class="footer-link">VIP 会员</a>
                    </div>
                </div>
                <div>
                    <div class="footer-title">关于</div>
                    <div class="footer-links">
                        <a href="{{ route('about') }}" class="footer-link">关于我们</a>
                        <a href="{{ route('contact') }}" class="footer-link">联系方式</a>
                        <a href="{{ route('privacy') }}" class="footer-link">隐私政策</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} AI 副业情报局。All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
    /** 关闭右侧推广位：10 分钟内不再弹出（按用户区分） */
    function closeSiteAdSidebar() {
        // 写入关闭缓存时间戳
        try {
            // 只是用于写入时间戳，不需要读取阈值
            var uid = @json(auth()->check() ? auth()->id() : null);
            var cacheUserId = '';
            if (uid !== null && uid !== undefined && uid !== '') {
                cacheUserId = 'u:' + uid;
            } else {
                var anonKey = 'lewan:anon_user_id';
                var anonId = localStorage.getItem(anonKey);
                if (!anonId) {
                    anonId = 'a_' + Math.random().toString(36).slice(2) + '_' + Date.now();
                    localStorage.setItem(anonKey, anonId);
                }
                cacheUserId = 'g:' + anonId;
            }
            var cacheKey = 'lewan:closed:site_ad_sidebar:' + cacheUserId;
            localStorage.setItem(cacheKey, JSON.stringify({ ts: Date.now() }));
        } catch (e) {}

        var el = document.getElementById('site-ad-sidebar');
        var grid = document.querySelector('.layout-page-grid');
        if (el) el.remove();
        if (grid) grid.classList.remove('layout-with-sidebar');
    }

    (function () {
        // 关闭缓存：同公告逻辑，10 分钟内不弹出
        var TEN_MIN_MS = 10 * 60 * 1000;
        function getLeWanCacheUserId() {
            try {
                var uid = @json(auth()->check() ? auth()->id() : null);
                if (uid !== null && uid !== undefined && uid !== '') {
                    return 'u:' + uid;
                }
                var anonKey = 'lewan:anon_user_id';
                var anonId = localStorage.getItem(anonKey);
                if (!anonId) {
                    anonId = 'a_' + Math.random().toString(36).slice(2) + '_' + Date.now();
                    localStorage.setItem(anonKey, anonId);
                }
                return 'g:' + anonId;
            } catch (e) {
                return 'g:local';
            }
        }

        var cacheKey = 'lewan:closed:site_ad_sidebar:' + getLeWanCacheUserId();
        try {
            var raw = localStorage.getItem(cacheKey);
            if (raw) {
                var obj = JSON.parse(raw);
                var ts = Number(obj && obj.ts ? obj.ts : 0);
                if (ts && (Date.now() - ts) < TEN_MIN_MS) {
                    var el = document.getElementById('site-ad-sidebar');
                    var grid = document.querySelector('.layout-page-grid');
                    if (el) el.remove();
                    if (grid) grid.classList.remove('layout-with-sidebar');
                }
            }
        } catch (e) {}
    })();

    // 皮肤切换功能（注意：打开面板时必须阻止冒泡，否则 document 上的关闭逻辑会在同一次点击里立刻把面板关掉）
    function toggleSkinPanel() {
        const panel = document.getElementById('skin-panel');
        if (!panel) return;
        const isShowing = panel.style.display === 'block';
        panel.style.display = isShowing ? 'none' : 'block';

        if (!isShowing) {
            setTimeout(function () {
                panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 100);
        }
    }

    function setSkin(skinName) {
        document.body.className = skinName;
        localStorage.setItem('preferred_skin', skinName);
        toggleSkinPanel();
        
        // 通知其他页面组件皮肤已更改
        if (window.dispatchEvent) {
            window.dispatchEvent(new CustomEvent('skin-changed', { detail: { skin: skinName } }));
        }
    }

    // 点击页面其他地方关闭皮肤面板（排除调色按钮本身）
    document.addEventListener('click', function(e) {
        if (e.target && e.target.closest && e.target.closest('[data-skin-toggle]')) {
            return;
        }
        const panel = document.getElementById('skin-panel');
        if (panel && panel.style.display === 'block' && !panel.contains(e.target)) {
            panel.style.display = 'none';
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        try {
            const savedSkin = localStorage.getItem('preferred_skin') || '';
            if (savedSkin) {
                document.body.className = savedSkin;
            }
        } catch(e) {
            console.log('Skin restore failed:', e);
        }

        document.querySelectorAll('[data-skin-toggle]').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                toggleSkinPanel();
            });
        });

        const panel = document.getElementById('skin-panel');
        if (panel) {
            panel.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    });

    // ESC 键关闭面板
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const panel = document.getElementById('skin-panel');
            if (panel) panel.style.display = 'none';
        }
    });
    </script>
</body>
</html>
