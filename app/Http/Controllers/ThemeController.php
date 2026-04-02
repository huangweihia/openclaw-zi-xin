<?php

namespace App\Http\Controllers;

use App\Models\UserThemePreference;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    /**
     * 切换主题
     */
    public function setTheme(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:default,blue,green,orange,dark',
        ]);

        $preference = UserThemePreference::getOrCreate(auth()->id());
        $preference->update(['theme' => $request->theme]);

        return back()->with('success', '主题已切换');
    }

    /**
     * 切换深色模式
     */
    public function toggleDarkMode(Request $request)
    {
        $preference = UserThemePreference::getOrCreate(auth()->id());
        $preference->update(['dark_mode' => !$preference->dark_mode]);

        return back()->with('success', '深色模式已切换');
    }

    /**
     * 设置字体大小
     */
    public function setFontSize(Request $request)
    {
        $request->validate([
            'font_size' => 'required|in:small,medium,large',
        ]);

        $preference = UserThemePreference::getOrCreate(auth()->id());
        $preference->update(['font_size' => $request->font_size]);

        return back()->with('success', '字体大小已设置');
    }

    /**
     * 切换跟随系统
     */
    public function toggleFollowSystem(Request $request)
    {
        $preference = UserThemePreference::getOrCreate(auth()->id());
        $preference->update(['follow_system' => !$preference->follow_system]);

        return back()->with('success', '设置已更新');
    }
}
