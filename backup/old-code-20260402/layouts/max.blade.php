{{-- MAX 版本主布局 - 支持换肤功能 --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $metaDescription ?? 'AI 副业情报局 MAX - 用 AI 搞副业，30 天多赚 5000+' }}">
    <meta name="keywords" content="{{ $metaKeywords ?? 'AI 副业，自动化，OpenClaw，VIP 会员' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? 'AI 副业情报局 MAX' }}</title>
    
    {{-- Tailwind CSS (CDN) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- 自定义配置 --}}
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: 'var(--color-primary-50, #f5f3ff)',
                            100: 'var(--color-primary-100, #ede9fe)',
                            500: 'var(--color-primary-500, #8b5cf6)',
                            600: 'var(--color-primary-600, #7c3aed)',
                            700: 'var(--color-primary-700, #6d28d9)',
                        },
                        background: {
                            main: 'var(--color-bg-main, #f9fafb)',
                            card: 'var(--color-bg-card, #ffffff)',
                        },
                        text: {
                            main: 'var(--color-text-main, #1f2937)',
                            secondary: 'var(--color-text-secondary, #6b7280)',
                            muted: 'var(--color-text-muted, #9ca3af)',
                        }
                    }
                }
            }
        }
    </script>
    
    {{-- 主题样式 - 支持换肤 --}}
    <style>
        /* 默认主题（紫色） */
        :root {
            --color-primary-50: #f5f3ff;
            --color-primary-100: #ede9fe;
            --color-primary-500: #8b5cf6;
            --color-primary-600: #7c3aed;
            --color-primary-700: #6d28d9;
            --color-bg-main: #f9fafb;
            --color-bg-card: #ffffff;
            --color-text-main: #1f2937;
            --color-text-secondary: #6b7280;
            --color-text-muted: #9ca3af;
        }
        
        /* 蓝色主题 */
        [data-theme="blue"] {
            --color-primary-50: #eff6ff;
            --color-primary-100: #dbeafe;
            --color-primary-500: #3b82f6;
            --color-primary-600: #2563eb;
            --color-primary-700: #1d4ed8;
        }
        
        /* 绿色主题 */
        [data-theme="green"] {
            --color-primary-50: #f0fdf4;
            --color-primary-100: #dcfce7;
            --color-primary-500: #10b981;
            --color-primary-600: #059669;
            --color-primary-700: #047857;
        }
        
        /* 橙色主题 */
        [data-theme="orange"] {
            --color-primary-50: #fff7ed;
            --color-primary-100: #ffedd5;
            --color-primary-500: #f97316;
            --color-primary-600: #ea580c;
            --color-primary-700: #c2410c;
        }
        
        /* 深色模式 */
        .dark {
            --color-bg-main: #111827;
            --color-bg-card: #1f2937;
            --color-text-main: #f9fafb;
            --color-text-secondary: #d1d5db;
            --color-text-muted: #9ca3af;
        }
        
        /* 通用样式类 */
        .gradient-bg {
            background: linear-gradient(135deg, var(--color-primary-500) 0%, var(--color-primary-700) 100%);
        }
        
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        @keyframes pulse-glow {
            0%, 100% { 
                box-shadow: 0 0 20px color-mix(in srgb, var(--color-primary-500) 50%, transparent);
            }
            50% { 
                box-shadow: 0 0 40px color-mix(in srgb, var(--color-primary-500) 80%, transparent);
            }
        }
        
        .pulse-glow {
            animation: pulse-glow 2s infinite;
        }
        
        /* 确保文字在所有主题下都可见 */
        body {
            color: var(--color-text-main);
            background-color: var(--color-bg-main);
        }
        
        /* 滚动条美化 */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--color-bg-main);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--color-primary-500);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--color-primary-600);
        }
    </style>
    
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col transition-colors duration-300">
    {{-- 导航栏 --}}
    @include('partials.max.navbar')
    
    {{-- 主内容区 --}}
    <main class="flex-grow">
        @yield('content')
    </main>
    
    {{-- 页脚 --}}
    @include('partials.max.footer')
    
    {{-- 主题切换脚本 --}}
    <script>
        // 主题管理
        const ThemeManager = {
            init() {
                this.loadTheme();
                this.setupThemeSwitcher();
            },
            
            loadTheme() {
                const savedTheme = localStorage.getItem('theme') || 'purple';
                const savedDarkMode = localStorage.getItem('darkMode') === 'true';
                
                this.setTheme(savedTheme);
                if (savedDarkMode) {
                    document.documentElement.classList.add('dark');
                }
            },
            
            setTheme(theme) {
                document.documentElement.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
            },
            
            toggleDarkMode() {
                document.documentElement.classList.toggle('dark');
                const isDark = document.documentElement.classList.contains('dark');
                localStorage.setItem('darkMode', isDark);
            },
            
            setupThemeSwitcher() {
                document.querySelectorAll('[data-theme-switch]').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const theme = e.currentTarget.dataset.themeSwitch;
                        this.setTheme(theme);
                    });
                });
                
                document.querySelectorAll('[data-dark-toggle]').forEach(btn => {
                    btn.addEventListener('click', () => {
                        this.toggleDarkMode();
                    });
                });
            }
        };
        
        // 初始化主题
        ThemeManager.init();
    </script>
    
    @stack('scripts')
</body>
</html>
