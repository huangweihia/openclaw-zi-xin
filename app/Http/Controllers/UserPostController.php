<?php

namespace App\Http\Controllers;

use App\Models\UserPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPostController extends Controller
{
    /**
     * 我的发布列表
     */
    public function index(Request $request)
    {
        $query = UserPost::where('user_id', Auth::id());
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $posts = $query->latest()->paginate(15);
        
        return view('max.posts.index', compact('posts'));
    }

    /**
     * 创建发布页面
     */
    public function create()
    {
        return view('max.posts.create');
    }

    /**
     * 存储发布
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:case,tool,experience,resource,question',
            'title' => 'required|max:100',
            'content' => 'required|min:200',
            'category' => 'nullable|max:50',
            'visibility' => 'required|in:public,vip,private',
        ]);

        $post = UserPost::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
            'tags' => $request->tags ? explode(',', $request->tags) : [],
            'visibility' => $request->visibility,
            'status' => 'pending', // 需要审核
        ]);

        return redirect()->route('posts.index')
            ->with('success', '发布成功！请等待审核。');
    }

    /**
     * 发布详情
     */
    public function show($id)
    {
        $post = UserPost::with(['user', 'comments.user'])->findOrFail($id);
        
        // 权限检查
        if ($post->visibility !== 'public' && $post->user_id !== Auth::id()) {
            abort(403);
        }
        
        $post->increment('view_count');
        
        return view('max.posts.show', compact('post'));
    }

    /**
     * 编辑发布
     */
    public function edit($id)
    {
        $post = UserPost::findOrFail($id);
        
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('max.posts.edit', compact('post'));
    }

    /**
     * 更新发布
     */
    public function update(Request $request, $id)
    {
        $post = UserPost::findOrFail($id);
        
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }
        
        $request->validate([
            'title' => 'required|max:100',
            'content' => 'required|min:200',
            'visibility' => 'required|in:public,vip,private',
        ]);

        $post->update([
            'title' => $request->title,
            'content' => $request->content,
            'tags' => $request->tags ? explode(',', $request->tags) : [],
            'visibility' => $request->visibility,
            'status' => 'pending', // 重新审核
        ]);

        return redirect()->route('posts.index')
            ->with('success', '更新成功！请重新等待审核。');
    }

    /**
     * 删除发布
     */
    public function destroy($id)
    {
        $post = UserPost::findOrFail($id);
        
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }
        
        $post->delete();
        
        return redirect()->route('posts.index')
            ->with('success', '删除成功');
    }
}
