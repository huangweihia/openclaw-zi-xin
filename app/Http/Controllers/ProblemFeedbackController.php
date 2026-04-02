<?php

namespace App\Http\Controllers;

use App\Models\ProblemFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProblemFeedbackController extends Controller
{
    public function create()
    {
        $feedbacks = ProblemFeedback::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->limit(10)
            ->get();

        return view('feedback.create', compact('feedbacks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            // 反馈截图使用本地 storage/app/public：对外通过 /storage/... 访问
            $path = $request->file('image')->store('feedback', 'public');
            if (! is_string($path) || $path === '' || ! Storage::disk('public')->exists($path)) {
                $message = '上传失败：存储写入失败，请检查 storage/app/public 写入权限。';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 500);
                }
                return back()->with('error', $message);
            }
        }

        $feedback = ProblemFeedback::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'image_path' => $path,
            'status' => 'pending',
        ]);

        $message = '反馈已提交，管理员审核通过后将奖励 1 天 VIP。';
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'feedback_id' => $feedback->id,
            ]);
        }

        return redirect()->route('feedback.create')->with('success', $message);
    }
}

