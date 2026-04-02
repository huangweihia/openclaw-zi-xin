<?php

namespace App\Http\Controllers;

use App\Models\ContentSubmission;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index()
    {
        $submissions = ContentSubmission::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(12);

        return view('submissions.index', compact('submissions'));
    }

    public function create()
    {
        return view('submissions.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'type' => 'required|in:document,project,job,knowledge',
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:500',
            'content' => 'required|string',
            'is_paid' => 'nullable|boolean',
            'price' => 'nullable|numeric|min:0|max:999999.99',
            'currency' => 'nullable|string|size:3',
        ];

        if ($request->input('type') === 'job') {
            $rules['company_name'] = 'required|string|max:200';
            $rules['job_title'] = 'required|string|max:200';
            $rules['salary_range'] = 'nullable|string|max:100';
            $rules['location'] = 'nullable|string|max:100';
            $rules['job_requirements'] = 'nullable|string|max:10000';
        }

        $request->validate($rules);

        $isPaid = (bool) $request->boolean('is_paid');
        $price = $isPaid ? (float) ($request->input('price') ?: 0) : 0;

        $payload = [
            'client_ip' => $request->ip(),
        ];

        if ($request->input('type') === 'job') {
            $payload['company_name'] = $request->input('company_name');
            $payload['job_title'] = $request->input('job_title');
            $payload['salary_range'] = $request->input('salary_range');
            $payload['location'] = $request->input('location');
            $payload['job_requirements'] = $request->input('job_requirements');
        }

        ContentSubmission::create([
            'user_id' => auth()->id(),
            'type' => $request->input('type'),
            'title' => $request->input('title'),
            'summary' => $request->input('summary'),
            'content' => $request->input('content'),
            'is_paid' => $isPaid,
            'price' => $price,
            'currency' => strtoupper($request->input('currency', 'CNY')),
            'status' => 'pending',
            'payload' => $payload,
        ]);

        return redirect()->route('dashboard')->with('success', '投稿已提交，等待管理员审核。');
    }

    /**
     * 投稿详情 API
     */
    public function show($id)
    {
        $submission = ContentSubmission::where('user_id', auth()->id())->findOrFail($id);
        
        return response()->json([
            'id' => $submission->id,
            'title' => $submission->title,
            'type' => $submission->type,
            'status' => $submission->status,
            'summary' => $submission->summary,
            'content' => $submission->content,
            'review_note' => $submission->review_note,
            'is_paid' => $submission->is_paid,
            'created_at' => $submission->created_at->format('Y-m-d H:i'),
            'reviewed_at' => $submission->reviewed_at?->format('Y-m-d H:i'),
        ]);
    }

    /**
     * 编辑投稿页面 - 复用创建页面
     */
    public function edit($id)
    {
        $submission = ContentSubmission::where('user_id', auth()->id())->findOrFail($id);
        
        // 只有被驳回的投稿才能编辑
        if ($submission->status !== 'rejected') {
            return redirect()->route('submissions.index')
                ->with('error', '只有被驳回的投稿才能重新编辑');
        }
        
        // 复用创建页面，传入投稿数据
        return view('submissions.create', compact('submission'));
    }

    /**
     * 更新投稿
     */
    public function update(Request $request, $id)
    {
        $submission = ContentSubmission::where('user_id', auth()->id())->findOrFail($id);
        
        // 只有被驳回的投稿才能编辑
        if ($submission->status !== 'rejected') {
            return back()->with('error', '只有被驳回的投稿才能重新编辑');
        }
        
        $rules = [
            'type' => 'required|in:document,project,job,knowledge',
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:500',
            'content' => 'required|string',
            'is_paid' => 'nullable|boolean',
        ];

        if ($request->input('type') === 'job') {
            $rules['company_name'] = 'required|string|max:200';
            $rules['job_title'] = 'required|string|max:200';
            $rules['salary_range'] = 'nullable|string|max:100';
            $rules['location'] = 'nullable|string|max:100';
            $rules['job_requirements'] = 'nullable|string|max:10000';
        }

        $request->validate($rules);

        $payload = array_merge($submission->payload ?? [], [
            'client_ip' => $request->ip(),
        ]);

        if ($request->input('type') === 'job') {
            $payload['company_name'] = $request->input('company_name');
            $payload['job_title'] = $request->input('job_title');
            $payload['salary_range'] = $request->input('salary_range');
            $payload['location'] = $request->input('location');
            $payload['job_requirements'] = $request->input('job_requirements');
        }

        $submission->update([
            'type' => $request->input('type'),
            'title' => $request->input('title'),
            'summary' => $request->input('summary'),
            'content' => $request->input('content'),
            'is_paid' => (bool) $request->boolean('is_paid'),
            'status' => 'pending', // 重置状态为待审核
            'review_note' => null, // 清空审核备注
            'payload' => $payload,
        ]);
        
        return redirect()->route('submissions.index')
            ->with('success', '投稿已重新提交，等待管理员审核。');
    }

    /**
     * 上传图片（供 Trix 编辑器使用）
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // 最大 5MB
        ]);

        if ($request->file('image')->isValid()) {
            // 保存到本地 storage/app/public：对外通过 /storage/... 访问
            $path = $request->file('image')->store('submissions/images', 'public');
            if (! is_string($path) || $path === '') {
                return response()->json(['error' => '上传失败：存储写入失败'], 422);
            }

            // 兜底：确保文件在磁盘上确实存在（避免返回成功但文件不存在）
            if (! file_exists(storage_path('app/public/' . $path))) {
                return response()->json(['error' => '上传失败：存储文件不存在'], 422);
            }

            $url = asset('storage/' . $path);
            
            return response()->json([
                'url' => $url,
                'location' => $url,
            ]);
        }

        return response()->json(['error' => '上传失败'], 422);
    }
}
