<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SyncAdminEnvironmentRequest;
use App\Payments\PaymentGateway;
use App\Services\AdminEnvironmentSynchronizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminConfigurationController extends Controller
{
    public function show(PaymentGateway $paymentGateway): View
    {
        $izipay = (array) config('services.izipay', []);

        return view('Admin.configuration', [
            'configuredAdmin' => [
                'name' => config('admin.name'),
                'email' => config('admin.email'),
                'phone' => config('admin.phone'),
                'password_configured' => filled(config('admin.password')),
            ],
            'izipayStatus' => [
                'enabled' => ($izipay['payments_enabled'] ?? false) === true,
                'environment' => in_array($izipay['environment'] ?? null, ['sandbox', 'production'], true)
                    ? $izipay['environment']
                    : 'inválido',
                'merchant_code_configured' => filled($izipay['merchant_code'] ?? null),
                'api_key_configured' => filled($izipay['api_key'] ?? null),
                'hash_key_configured' => filled($izipay['hash_key'] ?? null),
                'public_key_configured' => filled($izipay['public_key'] ?? null),
                'ready' => $paymentGateway->isReady(),
            ],
        ]);
    }

    public function sync(SyncAdminEnvironmentRequest $request, AdminEnvironmentSynchronizer $synchronizer): RedirectResponse
    {
        $admin = $synchronizer->sync();

        return redirect()
            ->route('admin.configuration.show')
            ->with('ok', "Administrador sincronizado correctamente (usuario #{$admin->id}).");
    }
}
