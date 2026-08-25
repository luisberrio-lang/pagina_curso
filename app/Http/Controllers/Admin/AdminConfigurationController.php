<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SyncAdminEnvironmentRequest;
use App\Services\AdminEnvironmentSynchronizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminConfigurationController extends Controller
{
    public function show(): View
    {
        return view('Admin.configuration', [
            'configuredAdmin' => [
                'name' => config('admin.name'),
                'email' => config('admin.email'),
                'phone' => config('admin.phone'),
                'password_configured' => filled(config('admin.password')),
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
