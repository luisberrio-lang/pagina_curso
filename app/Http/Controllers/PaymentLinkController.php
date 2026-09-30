<?php

namespace App\Http\Controllers;

use App\Models\PaymentLink;
use Illuminate\View\View;

class PaymentLinkController extends Controller
{
    public function show(string $token): View
    {
        $paymentLink = PaymentLink::query()
            ->where('token_hash', hash('sha256', $token))
            ->firstOrFail();

        $paymentLink->markExpiredIfNeeded();

        return view('payment-links.show', compact('paymentLink'));
    }
}
