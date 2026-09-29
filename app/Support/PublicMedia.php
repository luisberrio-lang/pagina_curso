<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Throwable;

final class PublicMedia
{
    public static function url(?string $path): ?string
    {
        $path = self::relativePath($path);
        if ($path === null) {
            return null;
        }

        try {
            $disk = Storage::disk('public');

            return $disk->exists($path) ? $disk->url($path) : null;
        } catch (Throwable) {
            return null;
        }
    }

    public static function relativePath(?string $path): ?string
    {
        if ($path === null || $path === '' || str_contains($path, "\0") || str_contains($path, '\\')) {
            return null;
        }

        $path = trim($path);
        if ($path === '' || str_starts_with($path, '/') || ! preg_match('/^[A-Za-z0-9][A-Za-z0-9._\/-]*$/', $path)) {
            return null;
        }

        $segments = explode('/', $path);
        if (in_array('', $segments, true) || in_array('.', $segments, true) || in_array('..', $segments, true)) {
            return null;
        }

        return $path;
    }
}
