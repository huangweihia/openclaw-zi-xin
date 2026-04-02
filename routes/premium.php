// Premium 内容路由
Route::prefix('premium')->middleware(['auth'])->group(function () {
    // 副业案例
    Route::get('cases', [SideHustleCaseController::class, 'index'])->name('premium.cases.index');
    Route::get('cases/{slug}', [SideHustleCaseController::class, 'show'])->name('premium.cases.show');
    
    // AI 工具变现
    Route::get('tools', [AiToolMonetizationController::class, 'index'])->name('premium.tools.index');
    Route::get('tools/{slug}', [AiToolMonetizationController::class, 'show'])->name('premium.tools.show');
    
    // 运营 SOP
    Route::get('sops', [PrivateTrafficSopController::class, 'index'])->name('premium.sops.index');
    Route::get('sops/{slug}', [PrivateTrafficSopController::class, 'show'])->name('premium.sops.show');
    
    // 付费资源
    Route::get('resources', [PremiumResourceController::class, 'index'])->name('premium.resources.index');
    Route::get('resources/{slug}', [PremiumResourceController::class, 'show'])->name('premium.resources.show');
});

// 公开的 Premium 内容预览（非登录用户可访问）
Route::prefix('preview')->group(function () {
    Route::get('cases', [SideHustleCaseController::class, 'publicIndex'])->name('preview.cases.index');
    Route::get('tools', [AiToolMonetizationController::class, 'publicIndex'])->name('preview.tools.index');
    Route::get('sops', [PrivateTrafficSopController::class, 'publicIndex'])->name('preview.sops.index');
    Route::get('resources', [PremiumResourceController::class, 'publicIndex'])->name('preview.resources.index');
});
