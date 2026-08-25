<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AdminEnvironmentSynchronizer
{
    public function sync(): User
    {
        $configured = $this->validatedConfiguration();

        return DB::transaction(function () use ($configured): User {
            $admin = User::query()
                ->where('is_admin', true)
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            $admin ??= User::query()
                ->where('email', $configured['email'])
                ->lockForUpdate()
                ->first();

            if (! $admin && blank($configured['password'])) {
                throw ValidationException::withMessages([
                    'configuration' => 'ADMIN_PASSWORD es obligatorio para crear el primer administrador.',
                ]);
            }

            $emailOwner = User::query()
                ->where('email', $configured['email'])
                ->when($admin, fn ($query) => $query->where('id', '!=', $admin->getKey()))
                ->exists();

            if ($emailOwner) {
                throw ValidationException::withMessages([
                    'configuration' => 'El correo configurado ya pertenece a otro usuario.',
                ]);
            }

            $admin ??= new User;
            $admin->forceFill([
                'name' => $configured['name'],
                'email' => $configured['email'],
                'phone' => $configured['phone'],
                'is_admin' => true,
            ]);

            if (! $admin->exists) {
                $admin->email_verified_at = now();
            }

            if (filled($configured['password']) && (! $admin->exists || ! Hash::check($configured['password'], (string) $admin->password))) {
                $admin->password = Hash::make($configured['password']);
            }

            $admin->save();

            return $admin;
        });
    }

    private function validatedConfiguration(): array
    {
        return Validator::make([
            'name' => config('admin.name'),
            'email' => config('admin.email'),
            'password' => config('admin.password'),
            'phone' => config('admin.phone'),
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:30'],
        ], [], [
            'name' => 'ADMIN_NAME',
            'email' => 'ADMIN_EMAIL',
            'password' => 'ADMIN_PASSWORD',
            'phone' => 'ADMIN_PHONE',
        ])->validate();
    }
}
