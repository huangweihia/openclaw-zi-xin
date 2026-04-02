@php
    // 检查用户权限，非管理员重定向
    if (auth()->check() && !auth()->user()->isAdmin()) {
        // 普通用户只能访问特定页面
        $allowedPaths = ['/admin', '/admin/logout'];
        if (!in_array(request()->path(), $allowedPaths)) {
            abort(403, '此页面仅限管理员访问');
        }
    }
@endphp
