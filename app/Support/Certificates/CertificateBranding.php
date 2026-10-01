<?php

namespace App\Support\Certificates;

use App\Models\Institution;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * An institution's logo, ready for a certificate (DataSensei Updates 13): a
 * small white-backed JPEG as base64, stored in the issued certificate's
 * snapshot so a later logo change never alters it. Needs PHP's GD extension
 * (on by default in XAMPP); without it, or without a logo, the layout shows
 * the institution's initials instead.
 */
final class CertificateBranding
{
    public const MAX_SIDE = 240;

    public static function logo(?Institution $institution): ?string
    {
        $path = $institution?->logo_path;
        if (! is_string($path) || $path === '' || ! function_exists('imagecreatefromstring')) {
            return null;
        }

        try {
            $disk = Storage::disk('public');
            if (! $disk->exists($path)) {
                return null;
            }

            return self::jpeg((string) $disk->get($path));
        } catch (Throwable) {
            return null;
        }
    }

    /** Any image GD can read, as a JPEG no larger than MAX_SIDE, base64. */
    public static function jpeg(string $bytes): ?string
    {
        if ($bytes === '' || ! function_exists('imagecreatefromstring')) {
            return null;
        }

        $source = @imagecreatefromstring($bytes);
        if ($source === false) {
            return null;
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min(1, self::MAX_SIDE / max($width, $height, 1));
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        ob_start();
        imagejpeg($canvas, null, 85);
        $jpeg = (string) ob_get_clean();
        imagedestroy($source);
        imagedestroy($canvas);

        return $jpeg === '' ? null : base64_encode($jpeg);
    }
}
