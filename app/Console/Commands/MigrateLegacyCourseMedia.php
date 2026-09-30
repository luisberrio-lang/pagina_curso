<?php

namespace App\Console\Commands;

use App\Services\LegacyMediaMigrationService;
use Illuminate\Console\Command;

class MigrateLegacyCourseMedia extends Command
{
    protected $signature = 'media:migrate-legacy {--dry-run : Muestra las operaciones sin copiar archivos}';

    protected $description = 'Copia medios históricos de storage/courses al disco público sin sobrescribir ni borrar el origen';

    public function handle(LegacyMediaMigrationService $migration): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $result = $migration->migrate($dryRun);
        $action = $dryRun ? 'Se copiarían' : 'Copiados';
        $this->info(
            "Encontrados: {$result['found']}. {$action}: {$result['copied']}. "
            ."Omitidos: {$result['skipped']}. Conflictos: {$result['conflicts']}. "
            ."Errores: {$result['errors']}. El origen no fue modificado.",
        );

        return $result['errors'] === 0 ? self::SUCCESS : self::FAILURE;
    }
}
