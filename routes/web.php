<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\KnowledgeController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProfileMessageController;
use App\Http\Controllers\SystemNotificationController;
use App\Http\Controllers\MyArticleEngagementController;
use App\Http\Controllers\Payments\WechatNativePaymentController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ProblemFeedbackController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 首页
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('users/{id}', [HomeController::class, 'userProfile'])->name('users.show');

// 自检页（登录后可用）
Route::get('self-check', function () {
    $hasSubmissionsTable = \Illuminate\Support\Facades\Schema::hasTable('content_submissions');
    $hasCommentLikesTable = \Illuminate\Support\Facades\Schema::hasTable('comment_likes');

    return response()->json([
        'ok' => true,
        'env' => app()->environment(),
        'db_default' => config('database.default'),
        'current_user' => auth()->user()?->only(['id', 'name', 'email', 'role']),
        'routes' => [
            'submissions.index' => \Illuminate\Support\Facades\Route::has('submissions.index'),
            'submissions.create' => \Illuminate\Support\Facades\Route::has('submissions.create'),
            'submissions.store' => \Illuminate\Support\Facades\Route::has('submissions.store'),
        ],
        'tables' => [
            'content_submissions' => $hasSubmissionsTable,
            'comment_likes' => $hasCommentLikesTable,
        ],
        'time' => now()->toDateTimeString(),
    ]);
})->middleware('auth')->name('self-check');

// 公开页面
Route::get('vip', function () {
    return view('vip');
})->name('vip');

// 微信支付异步通知（无登录、无 CSRF）
Route::post('payments/wechat/notify', [WechatNativePaymentController::class, 'notify'])
    ->name('payments.wechat.notify');

// 职位
Route::get('jobs', [\App\Http\Controllers\JobController::class, 'index'])->name('jobs.index');
Route::get('jobs/{id}', [\App\Http\Controllers\JobController::class, 'show'])->name('jobs.show');
Route::post('jobs/{id}/apply', [\App\Http\Controllers\JobController::class, 'apply'])->name('jobs.apply');
Route::post('jobs/{id}/comments', [\App\Http\Controllers\JobController::class, 'storeComment'])->name('jobs.comments.store');

Route::get('about', function() {
    return view('about');
})->name('about');

Route::get('contact', function() {
    return view('contact');
})->name('contact');

Route::get('privacy', function() {
    return view('privacy');
})->name('privacy');

Route::get('announcements/{announcement:slug}', [AnnouncementController::class, 'show'])->name('announcements.show');

Route::get('learning', function() {
    return view('articles.index');
})->name('learning');

Route::get('tools', function() {
    return view('projects.index');
})->name('tools');

// 认证路由
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('register/send-code', [RegisterController::class, 'sendSmsCode'])->name('register.send-code');
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    
    // 企业微信绑定
    Route::get('auth/bind-enterprise-wechat', [RegisterController::class, 'showEnterpriseWechatBind'])
        ->name('auth.bind-enterprise-wechat');
    Route::post('auth/bind-enterprise-wechat', [RegisterController::class, 'enterpriseWechatCallback'])
        ->name('auth.bind-enterprise-wechat.callback');
});

// 登出
Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// 用户主页留言（需登录）
Route::middleware('auth')->group(function () {
    Route::post('users/{user}/messages', [ProfileMessageController::class, 'store'])->name('users.messages.store');
    Route::post('users/{user}/messages/{message}/urgent', [ProfileMessageController::class, 'sendUrgent'])->name('users.messages.urgent');
});

// 需要登录的路由
Route::middleware('auth')->group(function () {
    // VIP 投稿
    Route::middleware('vip')->group(function () {
        Route::get('submissions', [\App\Http\Controllers\SubmissionController::class, 'index'])->name('submissions.index');
        Route::get('submissions/create', [\App\Http\Controllers\SubmissionController::class, 'create'])->name('submissions.create');
        Route::post('submissions', [\App\Http\Controllers\SubmissionController::class, 'store'])->name('submissions.store');
        Route::get('submissions/{id}', [\App\Http\Controllers\SubmissionController::class, 'show'])->name('submissions.show');
        Route::get('submissions/{id}/edit', [\App\Http\Controllers\SubmissionController::class, 'edit'])->name('submissions.edit');
        Route::put('submissions/{id}', [\App\Http\Controllers\SubmissionController::class, 'update'])->name('submissions.update');
        
        // 图片上传（Trix 编辑器）
        Route::post('admin/upload-image', [\App\Http\Controllers\SubmissionController::class, 'uploadImage'])->name('admin.upload-image');
    });
    // 个人中心
    Route::get('dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::post('profile/upload-avatar', [HomeController::class, 'uploadAvatar'])->name('profile.upload-avatar');
    Route::get('feedback', [ProblemFeedbackController::class, 'create'])->name('feedback.create');
    Route::post('feedback', [ProblemFeedbackController::class, 'store'])->name('feedback.store');

    // 我发布的职位及收到的申请（发布者）
    Route::get('my/jobs', [\App\Http\Controllers\JobController::class, 'myJobs'])->name('my.jobs.index');
    Route::get('my/jobs/{job}/applications', [\App\Http\Controllers\JobController::class, 'myJobApplications'])->name('my.jobs.applications');
    
    // 项目列表（公开）
    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/{id}', [ProjectController::class, 'show'])->name('projects.show');
    
    // 项目收藏/取消收藏（供前端 projects.show 调用）
    Route::post('projects/{id}/favorite', [ProjectController::class, 'toggleFavorite'])->name('projects.favorite');

    // 项目发表评论（供前端 projects.show 调用）
    Route::post('projects/{id}/comments', [ProjectController::class, 'storeComment'])->name('projects.comments.store');
    
    // 文章列表（公开）
    Route::get('articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('articles/{id}', [ArticleController::class, 'show'])->name('articles.show');
    
    // 文章互动
    Route::post('articles/{id}/favorite', [ArticleController::class, 'toggleFavorite'])->name('articles.favorite');
    Route::post('articles/{id}/like', [ArticleController::class, 'toggleLike'])->name('articles.like');
    Route::post('articles/{id}/comments', [ArticleController::class, 'storeComment'])->name('articles.comments.store');
    Route::post('knowledge/doc/{document}/comments', [KnowledgeController::class, 'storeComment'])->name('knowledge.documents.comments.store');

    // 系统通知、投稿文章互动数据
    Route::get('notifications', [SystemNotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{notification}/read', [SystemNotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('notifications/read-all', [SystemNotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::get('my-articles/engagement', [MyArticleEngagementController::class, 'index'])->name('articles.my-engagement');
    
    // 订阅偏好设置
    Route::get('subscriptions/preferences', [SubscriptionController::class, 'preferences'])->name('subscriptions.preferences');
    Route::post('subscriptions/preferences', [SubscriptionController::class, 'updatePreferences'])->name('subscriptions.update');

    // VIP 微信 Native 扫码支付
    Route::get('vip/pay/{plan}', [WechatNativePaymentController::class, 'selectPlan'])
        ->where('plan', 'monthly|yearly|lifetime')
        ->name('vip.pay');
    Route::post('payments/wechat/create', [WechatNativePaymentController::class, 'create'])->name('payments.wechat.create');
    Route::get('payments/wechat/order/{orderNo}', [WechatNativePaymentController::class, 'show'])->name('payments.wechat.show');
    Route::get('payments/wechat/order/{orderNo}/status', [WechatNativePaymentController::class, 'status'])->name('payments.wechat.status');
    
    // VIP 专属内容
    Route::middleware('vip')->group(function () {
        Route::get('vip/articles', [ArticleController::class, 'vipArticles'])->name('articles.vip');
        Route::get('vip/projects', [ProjectController::class, 'vipProjects'])->name('projects.vip');
    });
});

// 公开订阅路由（退订等）
Route::get('unsubscribe/{token}', [SubscriptionController::class, 'showUnsubscribe'])->name('unsubscribe.show');
Route::post('unsubscribe/{token}', [SubscriptionController::class, 'unsubscribe'])->name('unsubscribe.confirm');
Route::get('resubscribe/{token}', [SubscriptionController::class, 'resubscribe'])->name('resubscribe');

// 邮件管理导出
Route::get('admin/email-manager/export', function() {
    $recipients = \App\Models\EmailSetting::getRecipients();
    $content = implode("\n", $recipients);
    return response()->streamDownload(function () use ($content) {
        echo $content;
    }, 'email-recipients-' . now()->format('Y-m-d') . '.txt', [
        'Content-Type' => 'text/plain',
    ]);
})->middleware('auth');

// 后台：注册赠送 VIP 开关/天数
Route::post('admin/settings/register-vip', function (\Illuminate\Http\Request $request) {
    $user = auth()->user();
    abort_unless($user && $user->isAdmin(), 403);

    $data = $request->validate([
        'enabled' => ['required'],
        'days' => ['nullable', 'integer', 'min:0', 'max:3650'],
    ]);

    $enabled = (bool) ($data['enabled'] ?? false);
    $days = (int) ($data['days'] ?? 0);
    if ($enabled) {
        $days = max(1, $days);
    }

    \App\Models\Setting::setValue('register_default_vip_enabled', $enabled, 'boolean');
    \App\Models\Setting::setValue('register_default_vip_days', $days, 'number');

    return back()->with('success', '已保存注册赠送 VIP 设置');
})->name('admin.settings.register-vip')->middleware('auth');

// 后台：订阅邮件推送时间 + 日报/周报模板（与 schedule + emails:send-scheduled 一致）
Route::post('admin/settings/email-schedule', function (\Illuminate\Http\Request $request) {
    $user = auth()->user();
    abort_unless($user && $user->isAdmin(), 403);

    $data = $request->validate([
        'email_send_time' => ['required', 'regex:/^([01]?\d|2[0-3]):[0-5]\d$/'],
        'email_digest_template_key' => ['required', 'string', 'max:64'],
        'email_weekly_template_key' => ['required', 'string', 'max:64'],
    ], [
        'email_send_time.regex' => '时间格式须为 HH:mm（24 小时制），例如 10:00',
    ]);

    $digestKey = trim($data['email_digest_template_key']);
    $weeklyKey = trim($data['email_weekly_template_key']);

    $existsDigest = \App\Models\EmailTemplate::query()->where('key', $digestKey)->exists();
    $existsWeekly = \App\Models\EmailTemplate::query()->where('key', $weeklyKey)->exists();
    if (! $existsDigest || ! $existsWeekly) {
        return back()->withErrors(['template' => '所选模板 key 在「邮件模板」中不存在，请检查后再保存。']);
    }

    \App\Models\EmailSetting::set('email_send_time', $data['email_send_time'], '邮件发送时间（定时任务）');
    \App\Models\EmailSetting::set('email_digest_template_key', $digestKey, '定时日报使用的模板 key');
    \App\Models\EmailSetting::set('email_weekly_template_key', $weeklyKey, '定时周报（周一）使用的模板 key');

    return back()->with('success', '已保存推送时间与模板配置');
})->name('admin.settings.email-schedule')->middleware('auth');

// MAX 新版付费页面（公开）
Route::prefix('max')->name('max.')->group(function () {
    // 首页
    Route::get('/', function() {
        return view('max.home');
    })->name('home');
    
    // VIP 会员页
    Route::get('/vip', function() {
        return view('max.vip');
    })->name('vip');
    
    // 价格方案页
    Route::get('/pricing', function() {
        return view('max.pricing');
    })->name('pricing');
    
    // 副业案例列表
    Route::get('/cases', function() {
        return view('max.cases.index');
    })->name('cases.index');
    
    // 文章列表
    Route::get('/articles', function() {
        return view('max.articles.index');
    })->name('articles.index');
    
    // 文章详情
    Route::get('/articles/{id}', function($id) {
        return view('max.articles.show', ['id' => $id]);
    })->name('articles.show');
    
    // 项目列表
    Route::get('/projects', function() {
        return view('max.projects.index');
    })->name('projects.index');
    
    // 项目详情
    Route::get('/projects/{id}', function($id) {
        return view('max.projects.show', ['id' => $id]);
    })->name('projects.show');
});

// 需要登录的路由
Route::middleware('auth')->group(function () {
    // 个人中心
    Route::get('dashboard', function() {
        return view('max.dashboard');
    })->name('dashboard');
    
    // 收藏列表
    Route::get('favorites', function() {
        return view('max.favorites');
    })->name('favorites.index');
    
    // 浏览历史
    Route::get('history', function() {
        return view('max.history');
    })->name('history.index');
    
    // 我的发布
    Route::get('posts', function() {
        return view('max.posts');
    })->name('posts.index');
});

// 知识库路由
Route::prefix('knowledge')->group(function () {
    Route::get('/', [KnowledgeController::class, 'index'])->name('knowledge.index');
    Route::get('/search', [KnowledgeController::class, 'search'])->name('knowledge.search');
    Route::get('/doc/{document}', [KnowledgeController::class, 'showDocument'])->name('knowledge.documents.show');
    Route::get('/{knowledgeBase}', [KnowledgeController::class, 'show'])->name('knowledge.show');
});

// 互动功能路由（需要登录）
Route::middleware('auth')->group(function () {
    // 点赞/收藏（注意：评论点赞路由必须放在通配路由前面）
    Route::post('interactions/comments/{id}/like', [InteractionController::class, 'likeComment'])->name('interactions.comments.like');
    Route::post('interactions/{type}/{id}/like', [InteractionController::class, 'like'])->name('interactions.like');
    Route::post('interactions/{type}/{id}/favorite', [InteractionController::class, 'favorite'])->name('interactions.favorite');
    
    // 积分解锁
    Route::post('interactions/articles/{id}/unlock', [InteractionController::class, 'unlockArticle'])->name('interactions.unlock');
    
    // 用户积分
    Route::get('user/points', [HomeController::class, 'userPoints'])->name('user.points');
    
    // 收藏列表
    Route::get('favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::delete('favorites/{type}/{id}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    Route::post('favorites/bulk-delete', [FavoriteController::class, 'bulkDestroy'])->name('favorites.bulk-destroy');
    
    // 浏览历史
    Route::get('history', [HistoryController::class, 'index'])->name('history.index');
    Route::delete('history/{id}', [HistoryController::class, 'destroy'])->name('history.destroy');
    Route::post('history/clear', [HistoryController::class, 'clear'])->name('history.clear');
});
