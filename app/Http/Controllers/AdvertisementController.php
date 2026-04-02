<?php

namespace App\Http\Controllers;

use App\Models\AdSlot;
use App\Models\Advertisement;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{
    /**
     * 广告列表（后台）
     */
    public function index()
    {
        $slots = AdSlot::with('advertisements')->orderBy('sort')->get();
        return view('filament.pages.advertisement-manager', compact('slots'));
    }

    /**
     * 创建广告
     */
    public function create()
    {
        $slots = AdSlot::active()->get();
        return view('filament.advertisements.create', compact('slots'));
    }

    /**
     * 存储广告
     */
    public function store(Request $request)
    {
        $request->validate([
            'ad_slot_id' => 'required|exists:ad_slots,id',
            'title' => 'required|max:100',
            'content' => 'nullable',
            'image_url' => 'nullable|url',
            'link_url' => 'nullable|url',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after:start_at',
        ]);

        Advertisement::create($request->all());

        return redirect()->route('admin.advertisements.index')
            ->with('success', '广告创建成功');
    }

    /**
     * 记录广告曝光
     */
    public function recordImpression($id)
    {
        $ad = Advertisement::findOrFail($id);
        $ad->recordImpression();
        return response()->json(['success' => true]);
    }

    /**
     * 记录广告点击
     */
    public function recordClick($id)
    {
        $ad = Advertisement::findOrFail($id);
        $ad->recordClick();
        return response()->json(['success' => true]);
    }
}
