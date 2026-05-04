<?php
declare(strict_types=1);

namespace App\Admin\Controllers;

final class ImageUpload
{
    private const MAX_SIZE   = 2_048_000;  // 2MB
    private const MIN_RESIZE = 204_800;    // 200KB

    private const ALLOWED_TYPES = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpe'  => 'image/jpeg',
        'gif'  => 'image/gif',
        'png'  => 'image/png',
        'ico'  => 'image/x-icon',
        'webp' => 'image/webp',
        'avif' => 'image/avif',
        'svg'  => 'image/svg+xml',
    ];

    /** Formats already web-optimised — skip re-encoding, go straight to thumbs. */
    private const PASSTHROUGH_TYPES = ['webp', 'avif', 'svg'];

    private const SOCIAL_SIZES = [
        'facebook' => [1200, 630],
        'twitter'  => [1200, 675],
    ];

    /** Whether ImageMagick's convert binary is available via exec(). */
    private bool $hasImageMagick;

    public function __construct()
    {
        $this->hasImageMagick = $this->detectImageMagick();
    }

    // -------------------------------------------------------------------------
    // Capability detection
    // -------------------------------------------------------------------------

    /**
     * Returns true only if exec() is callable AND the ImageMagick convert
     * binary responds. False on shared hosts that disable exec() or lack IM.
     */
    private function detectImageMagick(): bool
    {
        if (!function_exists('exec') || !is_callable('exec')) {
            return false;
        }

        $disabled = array_map('trim', explode(',', ini_get('disable_functions')));
        if (in_array('exec', $disabled, true)) {
            return false;
        }

        exec('convert --version 2>&1', $out, $code);

        return $code === 0 && !empty($out);
    }

    // -------------------------------------------------------------------------
    // Public entry point
    // -------------------------------------------------------------------------

    public function handle(): array
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['error' => 'Invalid request'];
        }

        if (empty($_FILES)) {
            return ['error' => 'Upload too large or blocked'];
        }

        $field = $_POST['imagepath'] ?? null;

        if (!$field || !isset($_FILES[$field])) {
            return ['error' => 'Invalid upload field'];
        }

        $results = [];

        foreach ($_FILES[$field]['tmp_name'] as $i => $tmp) {
            $results[] = $this->processFile($field, $i);
        }

        return ['results' => $results];
    }

    // -------------------------------------------------------------------------
    // Core processing
    // -------------------------------------------------------------------------

    private function processFile(string $field, int $i): array
    {
        $tmpName = $_FILES[$field]['tmp_name'][$i];
        $name    = basename($_FILES[$field]['name'][$i]);
        $size    = $_FILES[$field]['size'][$i];
        $error   = $_FILES[$field]['error'][$i];

        if ($error !== UPLOAD_ERR_OK) {
            return ['error' => $this->uploadError($error)];
        }

        $filename  = preg_replace('/\s+/', '_', $name);
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!isset(self::ALLOWED_TYPES[$extension])) {
            return ['error' => 'Invalid image type'];
        }

        if ($size > self::MAX_SIZE) {
            return ['error' => 'File exceeds 2MB'];
        }

        if (getRecord('images', 'imagepath', $filename)) {
            return ['error' => "Image already exists"];
        }

        $uploadDir = PUBLIC_UPLOADS_PATH . '/';
        $thumbDir  = PUBLIC_UPLOADS_PATH . '/thumbs/';

        $temp  = $uploadDir . 'temp_' . $filename;
        $final = $uploadDir . $filename;

        if (!move_uploaded_file($tmpName, $temp)) {
            return ['error' => 'Failed to move upload'];
        }

        if (in_array($extension, self::PASSTHROUGH_TYPES, true)) {
            // Already web-optimised — copy as-is, skip lossy re-encode
            copy($temp, $final);
        } elseif ($this->hasImageMagick) {
            $this->convertIM($temp, $final);
        } else {
            $this->convertGD($temp, $final, $extension);
        }

        if (!file_exists($final)) {
            @unlink($temp);
            return ['error' => 'Conversion failed'];
        }

        chmod($final, 0644);

        if ($this->hasImageMagick) {
            $this->resizeIfNeededIM($final, $size);
        } else {
            $this->resizeIfNeededGD($final, $size, $extension);
        }

        $this->createThumbs($final, $thumbDir, $filename, $extension);
        $this->createSocial($final, $uploadDir, $filename, $extension);
        $this->createWebp($final, $uploadDir, $filename, $extension);

        @unlink($temp);

        if (!$this->insertToDatabase($filename)) {
            return ['error' => 'File uploaded but database insert failed'];
        }

        return [
            'success' => $filename,
            'engine'  => $this->hasImageMagick ? 'imagemagick' : 'gd',
        ];
    }

    // -------------------------------------------------------------------------
    // ImageMagick implementations (exec-based)
    // -------------------------------------------------------------------------

    private function convertIM(string $input, string $output): void
    {
        $cmd = sprintf(
            'convert %s -resize 1370x1370\> -strip -interlace Plane -gaussian-blur 0.05 -quality 85%% %s',
            escapeshellarg($input),
            escapeshellarg($output)
        );
        exec($cmd);
    }

    private function resizeIfNeededIM(string $file, int $originalSize): void
    {
        if ($originalSize <= self::MIN_RESIZE) {
            return;
        }

        $quality = 85;

        while (filesize($file) > self::MAX_SIZE && $quality > 10) {
            $quality -= 5;
            $cmd = sprintf(
                'convert %s -quality %d %s',
                escapeshellarg($file),
                $quality,
                escapeshellarg($file)
            );
            exec($cmd);
        }
    }

    // -------------------------------------------------------------------------
    // GD fallback implementations
    // -------------------------------------------------------------------------

    private function convertGD(string $input, string $output, string $ext): void
    {
        $img = $this->gdCreateFrom($input, $ext);
        if (!$img) {
            return;
        }

        [$w, $h] = getimagesize($input);
        $max = 1370;

        if ($w > $max || $h > $max) {
            $ratio   = min($max / $w, $max / $h);
            $newW    = (int) round($w * $ratio);
            $newH    = (int) round($h * $ratio);
            $resized = imagecreatetruecolor($newW, $newH);
            $this->gdPreserveTransparency($resized, $ext);
            imagecopyresampled($resized, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);
            imagedestroy($img);
            $img = $resized;
        }

        $this->gdSave($img, $output, $ext, 85);
        imagedestroy($img);
    }

    private function resizeIfNeededGD(string $file, int $originalSize, string $ext): void
    {
        if ($originalSize <= self::MIN_RESIZE) {
            return;
        }

        $quality = 85;

        while (filesize($file) > self::MAX_SIZE && $quality > 10) {
            $quality -= 5;
            $img = $this->gdCreateFrom($file, $ext);
            if (!$img) {
                break;
            }
            $this->gdSave($img, $file, $ext, $quality);
            imagedestroy($img);
        }
    }

    // -------------------------------------------------------------------------
    // Shared thumbnail / social / WebP creators (engine-aware)
    // -------------------------------------------------------------------------

    private function createThumbs(string $source, string $base, string $filename, string $ext): void
    {
        foreach ([200, 60] as $size) {
            $dir = $base . $size . '/';
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $dest = $dir . $filename;

            if ($this->hasImageMagick) {
                $cmd = sprintf(
                    'convert %s -resize %dx%d %s',
                    escapeshellarg($source), $size, $size,
                    escapeshellarg($dest)
                );
                exec($cmd);
            } else {
                $this->gdResize($source, $dest, $ext, $size, $size);
            }
        }
    }

    private function createSocial(string $source, string $base, string $filename, string $ext): void
    {
        foreach (self::SOCIAL_SIZES as $platform => [$w, $h]) {
            $dir = $base . $platform . '/';
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $dest = $dir . $filename;

            if ($this->hasImageMagick) {
                $cmd = sprintf(
                    'convert %s -background white -gravity center -extent %dx%d %s',
                    escapeshellarg($source), $w, $h,
                    escapeshellarg($dest)
                );
                exec($cmd);
            } else {
                $this->gdCanvas($source, $dest, $ext, $w, $h);
            }
        }
    }

    /**
     * Creates a WebP copy in a dedicated webp/ subdirectory.
     * Uses ImageMagick when available; falls back to GD's imagewebp().
     * Silently skips if WebP is not supported by the GD build.
     */
    private function createWebp(string $source, string $base, string $filename, string $ext): void
    {
        $dir = $base . 'webp/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $webpFilename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
        $dest         = $dir . $webpFilename;

        if ($this->hasImageMagick) {
            $cmd = sprintf(
                'convert %s -quality 85 -define webp:method=6 %s',
                escapeshellarg($source),
                escapeshellarg($dest)
            );
            exec($cmd);
        } else {
            if (!function_exists('imagewebp')) {
                return; // WebP not compiled into this GD build — skip silently
            }

            $img = $this->gdCreateFrom($source, $ext);
            if (!$img) {
                return;
            }

            imagewebp($img, $dest, 85);
            imagedestroy($img);
            chmod($dest, 0644);
        }
    }

    // -------------------------------------------------------------------------
    // GD helpers
    // -------------------------------------------------------------------------

    private function gdCreateFrom(string $file, string $ext): \GdImage|false
    {
        return match ($ext) {
            'jpg', 'jpeg', 'jpe' => imagecreatefromjpeg($file),
            'png'                => imagecreatefrompng($file),
            'gif'                => imagecreatefromgif($file),
            default              => false,
        };
    }

    private function gdSave(\GdImage $img, string $dest, string $ext, int $quality): void
    {
        match ($ext) {
            'jpg', 'jpeg', 'jpe' => imagejpeg($img, $dest, $quality),
            'png'                => imagepng($img, $dest, (int) round((100 - $quality) / 10)),
            'gif'                => imagegif($img, $dest),
            default              => null,
        };
    }

    private function gdPreserveTransparency(\GdImage $img, string $ext): void
    {
        if (in_array($ext, ['png', 'gif'], true)) {
            imagealphablending($img, false);
            imagesavealpha($img, true);
        }
    }

    private function gdResize(string $src, string $dest, string $ext, int $maxW, int $maxH): void
    {
        $img = $this->gdCreateFrom($src, $ext);
        if (!$img) {
            return;
        }

        [$w, $h] = getimagesize($src);
        $ratio = min($maxW / $w, $maxH / $h);
        $newW  = (int) round($w * $ratio);
        $newH  = (int) round($h * $ratio);

        $out = imagecreatetruecolor($newW, $newH);
        $this->gdPreserveTransparency($out, $ext);
        imagecopyresampled($out, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);

        $this->gdSave($out, $dest, $ext, 85);
        imagedestroy($img);
        imagedestroy($out);
    }

    /**
     * Composite the source image centred on a white canvas of $canvasW x $canvasH
     * (equivalent to ImageMagick's -background white -gravity center -extent).
     */
    private function gdCanvas(string $src, string $dest, string $ext, int $canvasW, int $canvasH): void
    {
        $img = $this->gdCreateFrom($src, $ext);
        if (!$img) {
            return;
        }

        [$w, $h] = getimagesize($src);
        $ratio = min($canvasW / $w, $canvasH / $h);
        $newW  = (int) round($w * $ratio);
        $newH  = (int) round($h * $ratio);

        $canvas = imagecreatetruecolor($canvasW, $canvasH);
        $white  = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);

        $x = (int) round(($canvasW - $newW) / 2);
        $y = (int) round(($canvasH - $newH) / 2);

        imagecopyresampled($canvas, $img, $x, $y, 0, 0, $newW, $newH, $w, $h);

        $this->gdSave($canvas, $dest, $ext, 85);
        imagedestroy($img);
        imagedestroy($canvas);
    }

    // -------------------------------------------------------------------------
    // Misc helpers
    // -------------------------------------------------------------------------

    private function uploadError(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE,
            UPLOAD_ERR_FORM_SIZE => 'File too large',
            UPLOAD_ERR_NO_FILE   => 'No file uploaded',
            default              => "Upload error ($code)",
        };
    }

    private function insertToDatabase(string $filename): bool
    {
        global $db;

        $title = pathinfo($filename, PATHINFO_FILENAME);

        $stmt = $db->prepare("INSERT INTO images (title, alt, imagepath) VALUES (?, ?, ?)");

        if (!$stmt) {
            error_log("Database prepare failed: " . $db->error);
            return false;
        }

        if (!$stmt->bind_param('sss', $title, $title, $filename)) {
            error_log("Bind failed: " . $stmt->error);
            $stmt->close();
            return false;
        }

        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            $stmt->close();
            return false;
        }

        $stmt->close();
        return true;
    }
}