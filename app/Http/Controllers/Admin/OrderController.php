<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $orders = Order::query()
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('order_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('Admin.orders.index', compact('orders', 'search'));
    }

    public function show(Order $order): View
    {
        return view('Admin.orders.show', ['order' => $order->load('items')]);
    }
}
