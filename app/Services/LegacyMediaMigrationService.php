<?php

namespace App\Services;

use App\Support\PublicMedia;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Throwable;

class LegacyMediaMigrationService
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    public function __construct(
        private readonly Filesystem $files,
        private readonly ?string $sourceRoot = null,
    ) {}

    /**
     * @return array{found:int,copied:int,skipped:int,conflicts:int,errors:int}
     */
    public function migrate(bool $dryRun = false): array
    {
        $result = ['found' => 0, 'copied' => 0, 'skipped' => 0, 'conflicts' => 0, 'errors' => 0];
        $sourceRoot = $this->sourceRoot ?? storage_path('courses');

        if (! $this->files->isDirectory($sourceRoot)) {
            return $result;
        }

        $disk = Storage::disk('public');

        foreach ($this->files->allFiles($sourceRoot) as $file) {
            $result['found']++;
            $relative = str_replace('\\', '/', $file->getRelativePathname());

            if (! $this->isAllowedRelativePath($relative)) {
                $result['errors']++;
                continue;
            }

            if (! $this->hasExpectedImageMime($file->getPathname(), $relative)) {
                $result['errors']++;
                continue;
            }

            $destination = 'courses/'.$relative;

            try {
                if ($disk->exists($destination)) {
                    if (hash_equals($this->hashFile($file->getPathname()), $this->hashFile($disk->path($destination)))) {
                        $result['skipped']++;
                    } else {
                        $result['conflicts']++;
                    }

                    continue;
                }

                if ($dryRun) {
                    $result['copied']++;
                    continue;
                }

                $stream = fopen($file->getPathname(), 'rb');
                if ($stream === false) {
                    $result['errors']++;
                    continue;
                }

                try {
                    $stored = $disk->put($destination, $stream);
                } finally {
                    fclose($stream);
                }

                if (! $stored || ! $disk->exists($destination)
                    || ! hash_equals($this->hashFile($file->getPathname()), $this->hashFile($disk->path($destination)))) {
                    $result['errors']++;
                    continue;
                }

                $result['copied']++;
            } catch (Throwable) {
                $result['errors']++;
            }
        }

        return $result;
    }

    public function isAllowedRelativePath(string $relative): bool
    {
        $relative = str_replace('\\', '/', $relative);
        $validated = PublicMedia::relativePath('courses/'.$relative);

        if ($validated === null || ! preg_match('#^courses/(covers|samples)/[^/]+$#', $validated)) {
            return false;
        }

        return in_array(strtolower(pathinfo($validated, PATHINFO_EXTENSION)), self::ALLOWED_EXTENSIONS, true);
    }

    private function hashFile(string $path): string
    {
        $hash = hash_file('sha256', $path);

        if (! is_string($hash)) {
            throw new \RuntimeException('No se pudo verificar la integridad del archivo.');
        }

        return $hash;
    }

    private function hasExpectedImageMime(string $path, string $relative): bool
    {
        $extension = strtolower(pathinfo($relative, PATHINFO_EXTENSION));
        $expected = match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            default => null,
        };
        $details = @getimagesize($path);

        return $expected !== null
            && is_array($details)
            && ($details['mime'] ?? null) === $expected;
    }
}
