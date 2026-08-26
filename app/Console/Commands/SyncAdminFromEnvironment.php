<?php

namespace App\Console\Commands;

use App\Services\AdminEnvironmentSynchronizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Throwable;

class SyncAdminFromEnvironment extends Command
{
    protected $signature = 'admin:sync-env';

    protected $description = 'Sincroniza el administrador principal desde la configuración del entorno';

    public function handle(AdminEnvironmentSynchronizer $synchronizer): int
    {
        try {
            $admin = $synchronizer->sync();
        } catch (ValidationException $exception) {
            $this->error(collect($exception->errors())->flatten()->first() ?? 'La configuración del administrador no es válida.');

            return self::FAILURE;
        } catch (Throwable) {
            $this->error('No se pudo sincronizar el administrador. Revisa la configuración y el estado de la base de datos.');

            return self::FAILURE;
        }

        $this->info('Administrador sincronizado correctamente.');
        $passwordConfigured = filled(config('admin.password'));
        $passwordStatus = $passwordConfigured && Hash::check((string) config('admin.password'), (string) $admin->password)
            ? 'SÍ'
            : ($passwordConfigured ? 'NO' : 'NO SOLICITADA');

        $this->table(['Campo', 'Resultado'], [
            ['Usuario', '#'.$admin->id],
            ['Nombre', $admin->name],
            ['Correo', $admin->email],
            ['Teléfono', $admin->phone ?: 'No configurado'],
            ['Administrador', $admin->is_admin ? 'SÍ' : 'NO'],
            ['Contraseña configurada', $passwordConfigured ? 'SÍ' : 'NO'],
            ['Contraseña sincronizada', $passwordStatus],
        ]);

        return self::SUCCESS;
    }
}
