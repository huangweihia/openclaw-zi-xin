<?php

namespace App\Http\Controllers;

use App\Models\RefundRequest;
use App\Models\InvoiceRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RefundInvoiceController extends Controller
{
    /**
     * 申请退款
     */
    public function requestRefund(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'reason' => 'required|max:200',
            'description' => 'nullable|max:500',
        ]);

        $order = Order::where('id', $request->order_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // 检查是否符合退款条件（7 天内）
        if ($order->created_at->diffInDays(now()) > 7) {
            return back()->with('error', '超过 7 天退款期限');
        }

        $refund = RefundRequest::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'amount' => $order->paid_amount ?? $order->amount,
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return redirect()->route('refunds.index')
            ->with('success', '退款申请已提交');
    }

    /**
     * 我的退款申请
     */
    public function index()
    {
        $refunds = RefundRequest::where('user_id', Auth::id())
            ->with('order')
            ->latest()
            ->paginate(10);
        
        return view('max.refunds.index', compact('refunds'));
    }

    /**
     * 申请发票
     */
    public function requestInvoice(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'invoice_type' => 'required|in:personal/company',
            'invoice_title' => 'required|max:200',
            'tax_id' => 'nullable|max:100',
            'invoice_email' => 'required|email|max:200',
        ]);

        $order = Order::where('id', $request->order_id)
            ->where('user_id', Auth::id())
            ->where('status', 'paid')
            ->firstOrFail();

        $invoice = InvoiceRequest::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'invoice_type' => $request->invoice_type,
            'invoice_title' => $request->invoice_title,
            'tax_id' => $request->tax_id,
            'invoice_email' => $request->invoice_email,
            'status' => 'pending',
        ]);

        return redirect()->route('invoices.index')
            ->with('success', '发票申请已提交');
    }

    /**
     * 我的发票申请
     */
    public function invoices()
    {
        $invoices = InvoiceRequest::where('user_id', Auth::id())
            ->with('order')
            ->latest()
            ->paginate(10);
        
        return view('max.invoices.index', compact('invoices'));
    }
}
