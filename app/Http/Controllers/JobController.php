<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class JobController extends Controller
{
    /**
     * 职位列表
     */
    public function index(Request $request)
    {
        $query = Job::query()->where('is_published', true);
        
        // 搜索
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('company_name', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
            });
        }
        
        // 地点筛选
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }
        
        $jobs = $query->latest('published_at')->paginate(20)->withQueryString();
        
        return view('jobs.index', compact('jobs'));
    }

    /**
     * 职位详情
     */
    public function show($id)
    {
        $job = Job::with(['user', 'comments.user'])->findOrFail($id);
        
        // 增加浏览次数
        $job->incrementViewCount();
        
        // 记录浏览历史
        if (auth()->check()) {
            \App\Models\ViewHistory::record(auth()->user(), $job);
        }
        
        // 检查是否可以查看联系方式与完整正文（VIP 专属职位）
        $canViewContact = $job->canViewContact(auth()->user());
        $canViewFullContent = $job->userCanViewFullContent(auth()->user());
        
        // 获取评论（最新 10 条）
        $comments = $job->comments()
            ->whereNull('parent_id')
            ->where('is_hidden', false)
            ->with(['user', 'replies.user'])
            ->latest()
            ->limit(10)
            ->get();
        
        $commentsTotal = $job->comments()
            ->whereNull('parent_id')
            ->where('is_hidden', false)
            ->count();
        
        // 获取相关职位
        $relatedJobs = Job::query()
            ->where('id', '!=', $job->id)
            ->where('is_published', true)
            ->where(function ($q) use ($job) {
                $q->where('location', $job->location)
                  ->orWhere('salary_range', 'like', '%' . substr($job->salary_range, 0, 3) . '%');
            })
            ->limit(5)
            ->get();
        
        return view('jobs.show', compact('job', 'canViewContact', 'canViewFullContent', 'comments', 'commentsTotal', 'relatedJobs'));
    }

    /**
     * 申请职位
     */
    public function apply(Request $request, $id)
    {
        $job = Job::findOrFail($id);
        $user = auth()->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => '请先登录后申请职位',
            ], 401);
        }

        if ((int) $user->id === (int) $job->user_id) {
            return response()->json([
                'success' => false,
                'message' => '不能申请自己发布的职位',
            ], 422);
        }

        $request->validate([
            'message' => 'nullable|string|max:1000',
        ]);

        if (! Schema::hasTable('job_applications')) {
            return response()->json([
                'success' => false,
                'message' => '系统尚未完成数据库升级，请联系管理员执行：php artisan migrate',
            ], 503);
        }

        $application = JobApplication::firstOrNew([
            'job_id' => $job->id,
            'user_id' => $user->id,
        ]);
        $isNew = ! $application->exists;
        $application->message = $request->input('message');
        $application->save();

        if ($isNew) {
            $job->increment('apply_count');
        }

        return response()->json([
            'success' => true,
            'message' => $isNew ? '申请已提交，祝您求职顺利！' : '已更新你的申请附言。',
            'apply_count' => $job->fresh()->apply_count,
        ]);
    }

    /**
     * 我发布的职位（发布者查看）
     */
    public function myJobs(Request $request)
    {
        $user = auth()->user();
        if (! $user->isVip() && ! $user->isAdmin()) {
            abort(403, '仅 VIP 会员可使用「我发布的职位」');
        }

        $query = Job::query()
            ->where('user_id', auth()->id())
            ->latest('published_at');

        if (Schema::hasTable('job_applications')) {
            $query->withCount('applications');
        }

        $jobs = $query->paginate(15)->withQueryString();
        $applicationsTableReady = Schema::hasTable('job_applications');

        return view('jobs.my-index', compact('jobs', 'applicationsTableReady'));
    }

    /**
     * 某职位的申请列表（仅发布者）
     */
    public function myJobApplications(Request $request, Job $job)
    {
        $user = auth()->user();
        if (! $user->isVip() && ! $user->isAdmin()) {
            abort(403, '仅 VIP 会员可使用「我发布的职位」');
        }

        if ((int) $job->user_id !== (int) auth()->id()) {
            abort(403, '无权查看该职位的申请');
        }

        if (! Schema::hasTable('job_applications')) {
            return redirect()
                ->route('my.jobs.index')
                ->with('error', '请先在生产环境执行数据库迁移：php artisan migrate（需包含 job_applications 表）');
        }

        $applications = $job->applications()
            ->with('applicant')
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('jobs.my-applications', compact('job', 'applications'));
    }

    /**
     * 发表评论
     */
    public function storeComment(Request $request, $id)
    {
        $job = Job::findOrFail($id);
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '请先登录后评论',
            ], 401);
        }
        
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);
        
        $comment = $job->comments()->create([
            'user_id' => $user->id,
            'content' => $request->content,
        ]);
        
        return response()->json([
            'success' => true,
            'comment' => $comment->load('user'),
            'total' => $job->comments()->whereNull('parent_id')->where('is_hidden', false)->count(),
        ]);
    }
}
