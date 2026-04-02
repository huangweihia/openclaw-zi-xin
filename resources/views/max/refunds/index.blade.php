<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>退款申请 - OpenClaw 智信</title>
    @include('max.partials.head')
</head>
<body class="bg-gray-50">
    @php $currentPage = 'refunds'; @endphp
    @include('max.partials.nav')

    <section class="pt-32 pb-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-4xl font-bold mb-8">退款申请</h1>

            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                <form action="{{ route('refunds.request') }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">选择订单 *</label>
                        <select name="order_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                            <option value="">请选择订单</option>
                            @foreach(auth()->user()->orders()->where('status', 'paid')->latest()->get() as $order)
                                <option value="{{ $order->id }}">
                                    {{ $order->order_no }} - ¥{{ $order->amount }} ({{ $order->created_at->format('Y-m-d') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">退款原因 *</label>
                        <select name="reason" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                            <option value="">请选择原因</option>
                            <option value="未使用">未使用服务</option>
                            <option value="不满意">服务不满意</option>
                            <option value="重复购买">重复购买</option>
                            <option value="其他">其他原因</option>
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">详细描述</label>
                        <textarea name="description" rows="4" placeholder="请详细描述退款原因..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500"></textarea>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">ℹ️</span>
                            <div class="text-sm text-blue-800">
                                <div class="font-semibold mb-2">退款政策</div>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>7 天内可申请退款</li>
                                    <li>未使用服务优先审核</li>
                                    <li>审核时间：1-3 个工作日</li>
                                    <li>退款原路返回</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="gradient-bg text-white px-8 py-4 rounded-lg font-semibold hover:opacity-90 transition">
                        提交退款申请
                    </button>
                </form>
            </div>

            <!-- 退款记录 -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h3 class="text-2xl font-bold mb-6">退款记录</h3>
                <div class="space-y-4">
                    @forelse($refunds as $refund)
                        <div class="border rounded-xl p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <div class="font-bold">订单 {{ $refund->order->order_no }}</div>
                                    <div class="text-sm text-gray-500">申请时间：{{ $refund->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-green-600">¥{{ $refund->amount }}</div>
                                    <span class="px-3 py-1 {{ $refund->status === 'approved' ? 'bg-green-100 text-green-700' : ($refund->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }} rounded-full text-sm">
                                        {{ $refund->status === 'approved' ? '✅ 已通过' : ($refund->status === 'rejected' ? '❌ 已拒绝' : '⏳ 审核中') }}
                                    </span>
                                </div>
                            </div>
                            @if($refund->audit_note)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="text-sm text-gray-600">审核备注：{{ $refund->audit_note }}</div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">暂无退款记录</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    @include('max.partials.footer')
</body>
</html>
