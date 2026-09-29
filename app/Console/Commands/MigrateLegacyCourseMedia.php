<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class MigrateLegacyCourseMedia extends Command
{
    protected $signature = 'media:migrate-legacy {--dry-run : Muestra las operaciones sin copiar archivos}';

    protected $description = 'Copia medios históricos de storage/courses al disco público sin sobrescribir ni borrar el origen';

    public function handle(): int
    {
        $sourceRoot = storage_path('courses');
        if (! File::isDirectory($sourceRoot)) {
            $this->info('No existe la carpeta histórica storage/courses. No hay nada que copiar.');

            return self::SUCCESS;
        }

        $copied = 0;
        $skipped = 0;
        $dryRun = (bool) $this->option('dry-run');
        $disk = Storage::disk('public');

        foreach (File::allFiles($sourceRoot) as $file) {
            $relative = str_replace('\\', '/', $file->getRelativePathname());
            $destination = 'courses/'.$relative;

            if ($disk->exists($destination)) {
                $skipped++;
                continue;
            }

            if ($dryRun) {
                $this->line("COPIAR: {$destination}");
                $copied++;
                continue;
            }

            $stream = fopen($file->getPathname(), 'rb');
            if ($stream === false) {
                throw new RuntimeException("No se pudo leer el archivo histórico {$relative}.");
            }

            try {
                if (! $disk->put($destination, $stream)) {
                    throw new RuntimeException("No se pudo copiar {$relative} al disco público.");
                }
            } finally {
                fclose($stream);
            }

            $copied++;
        }

        $action = $dryRun ? 'Se copiarían' : 'Copiados';
        $this->info("{$action}: {$copied}. Omitidos por existir: {$skipped}. El origen no fue modificado.");

        return self::SUCCESS;
    }
}
