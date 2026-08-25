<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate(['search' => ['nullable', 'string', 'max:100']]);
        $search = trim((string) $request->query('search'));
        $payments = Payment::query()
            ->with('order:id,order_number,email')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('payment_number', 'like', "%{$search}%")
                    ->orWhere('provider_transaction_id', 'like', "%{$search}%")
                    ->orWhereHas('order', fn ($query) => $query->where('order_number', 'like', "%{$search}%"));
            }))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('Admin.payments.index', compact('payments', 'search'));
    }

    public function show(Payment $payment): View
    {
        return view('Admin.payments.show', ['payment' => $payment->load(['order', 'events'])]);
    }
}
