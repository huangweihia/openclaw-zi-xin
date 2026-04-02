<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>发票申请 - OpenClaw 智信</title>
    @include('max.partials.head')
</head>
<body class="bg-gray-50">
    @php $currentPage = 'invoices'; @endphp
    @include('max.partials.nav')

    <section class="pt-32 pb-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-4xl font-bold mb-8">发票申请</h1>

            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                <form action="{{ route('invoices.request') }}" method="POST">
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
                        <label class="block text-sm font-semibold text-gray-700 mb-2">发票类型 *</label>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="invoice_type" value="personal" checked class="w-4 h-4 text-purple-600">
                                <span>个人发票</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="invoice_type" value="company" class="w-4 h-4 text-purple-600">
                                <span>企业发票</span>
                            </label>
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">发票抬头 *</label>
                        <input type="text" name="invoice_title" required placeholder="个人姓名或公司名称" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">税号</label>
                        <input type="text" name="tax_id" placeholder="企业税号（个人发票可不填）" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">接收邮箱 *</label>
                        <input type="email" name="invoice_email" required value="{{ auth()->user()->email }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                    </div>
                    <button type="submit" class="gradient-bg text-white px-8 py-4 rounded-lg font-semibold hover:opacity-90 transition">
                        提交发票申请
                    </button>
                </form>
            </div>

            <!-- 发票记录 -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h3 class="text-2xl font-bold mb-6">发票记录</h3>
                <div class="space-y-4">
                    @forelse($invoices as $invoice)
                        <div class="border rounded-xl p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <div class="font-bold">{{ $invoice->invoice_title }}</div>
                                    <div class="text-sm text-gray-500">申请时间：{{ $invoice->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="text-right">
                                    <span class="px-3 py-1 {{ $invoice->status === 'sent' ? 'bg-green-100 text-green-700' : ($invoice->status === 'processing' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700') }} rounded-full text-sm">
                                        {{ $invoice->status === 'sent' ? '✅ 已开具' : ($invoice->status === 'processing' ? '📝 处理中' : '⏳ 待处理') }}
                                    </span>
                                </div>
                            </div>
                            @if($invoice->invoice_number)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="text-sm text-gray-600">发票号码：{{ $invoice->invoice_number }}</div>
                                    <div class="text-sm text-gray-600">接收邮箱：{{ $invoice->invoice_email }}</div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">暂无发票记录</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    @include('max.partials.footer')
</body>
</html>
