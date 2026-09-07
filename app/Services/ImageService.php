<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Process, resize, and convert an uploaded image to WebP.
     *
     * @param UploadedFile|string $file
     * @param string $folder Subfolder inside storage/app/public
     * @param int $maxWidth Max width constraint (proportional scaling)
     * @param int $quality WebP compression quality (1-100)
     * @return string Publicly accessible relative path (e.g. storage/products/abc.webp)
     */
    public static function uploadAndOptimize($file, string $folder = 'products', int $maxWidth = 1200, int $quality = 82): string
    {
        // Generate unique filename with .webp extension
        $filename = Str::random(24) . '.webp';
        $relativeStoragePath = trim($folder, '/') . '/' . $filename;

        // Process image with Intervention Image v4
        $image = Image::decode($file);

        // Scale down if larger than max width, keeping original aspect ratio
        if ($image->width() > $maxWidth) {
            $image->scaleDown(width: $maxWidth);
        }

        // Encode to modern WebP format
        $encoded = $image->encode(new \Intervention\Image\Encoders\WebpEncoder(quality: $quality));

        // Store to public disk
        Storage::disk('public')->put($relativeStoragePath, (string) $encoded);

        // Return path suitable for asset() helper or direct storage/ URL
        return 'storage/' . $relativeStoragePath;
    }

    /**
     * Create an exact square or cropped thumbnail.
     *
     * @param UploadedFile|string $file
     * @param string $folder
     * @param int $width
     * @param int $height
     * @return string
     */
    public static function uploadThumbnail($file, string $folder = 'products/thumbs', int $width = 300, int $height = 300): string
    {
        $filename = 'thumb_' . Str::random(24) . '.webp';
        $relativeStoragePath = trim($folder, '/') . '/' . $filename;

        $image = Image::decode($file);
        $image->cover($width, $height);
        $encoded = $image->encode(new \Intervention\Image\Encoders\WebpEncoder(quality: 80));

        Storage::disk('public')->put($relativeStoragePath, (string) $encoded);

        return 'storage/' . $relativeStoragePath;
    }

    /**
     * Delete an uploaded image from public storage.
     *
     * @param string|null $path
     * @return bool
     */
    public static function delete(?string $path): bool
    {
        if (!$path) return false;

        // Only delete files stored in storage/
        if (str_starts_with($path, 'storage/')) {
            $relativePath = substr($path, strlen('storage/'));
            if (Storage::disk('public')->exists($relativePath)) {
                return Storage::disk('public')->delete($relativePath);
            }
        }

        return false;
    }
}

