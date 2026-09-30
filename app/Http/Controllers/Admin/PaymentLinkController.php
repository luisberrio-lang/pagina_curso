<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePaymentLinkRequest;
use App\Models\PaymentLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PaymentLinkController extends Controller
{
    public function index(): View
    {
        PaymentLink::query()
            ->where('status', PaymentLink::STATUS_PENDING)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->update(['status' => PaymentLink::STATUS_EXPIRED, 'updated_at' => now()]);

        $paymentLinks = PaymentLink::query()->latest()->paginate(25);

        return view('Admin.payment-links.index', compact('paymentLinks'));
    }

    public function create(): View
    {
        return view('Admin.payment-links.create');
    }

    public function store(StorePaymentLinkRequest $request): RedirectResponse
    {
        $data = $request->validated();

        do {
            $token = Str::random(64);
            $tokenHash = hash('sha256', $token);
        } while (PaymentLink::query()->where('token_hash', $tokenHash)->exists());

        do {
            $reference = 'PL-'.now()->format('Y').'-'.strtoupper(Str::random(8));
        } while (PaymentLink::query()->where('reference', $reference)->exists());

        $paymentLink = new PaymentLink;
        $paymentLink->forceFill([
            ...$data,
            'customer_phone' => $data['customer_phone'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
            'internal_note' => $data['internal_note'] ?? null,
            'reference' => $reference,
            'token_hash' => $tokenHash,
            'token_ciphertext' => Crypt::encryptString($token),
            'currency' => 'PEN',
            'status' => PaymentLink::STATUS_PENDING,
            'created_by' => $request->user()->getKey(),
        ])->save();

        return redirect()
            ->route('admin.payment-links.index')
            ->with('ok', 'Enlace de pago creado correctamente.')
            ->with('created_payment_link_id', $paymentLink->getKey());
    }

    public function cancel(PaymentLink $paymentLink): RedirectResponse
    {
        $paymentLink->markExpiredIfNeeded();

        if (! $paymentLink->canBeCancelled()) {
            return redirect()
                ->route('admin.payment-links.index')
                ->withErrors(['payment_link' => 'Solo se pueden cancelar enlaces pendientes.']);
        }

        $paymentLink->forceFill([
            'status' => PaymentLink::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ])->save();

        return redirect()->route('admin.payment-links.index')->with('ok', 'Enlace cancelado correctamente.');
    }
}
