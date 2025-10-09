<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageUploadService
{
    private $disk;
    private $basePath;

    public function __construct()
    {
        $this->disk = Storage::disk('public');
        $this->basePath = 'products';
    }

    /**
     * Upload multiple images for a product
     *
     * @param array $files Array of UploadedFile instances
     * @param string|null $productId Optional product ID for folder organization
     * @return array Array of uploaded image paths
     */
    public function uploadProductImages(array $files, $productId = null): array
    {
        $uploadedImages = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $imagePath = $this->uploadSingleImage($file, $productId);
                if ($imagePath) {
                    $uploadedImages[] = $imagePath;
                }
            }
        }

        return $uploadedImages;
    }

    /**
     * Upload a single image
     *
     * @param UploadedFile $file
     * @param string|null $productId
     * @return string|null
     */
    private function uploadSingleImage(UploadedFile $file, $productId = null): ?string
    {
        try {
            // Generate unique filename
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Create path with optional product ID subfolder
            $path = $this->basePath . ($productId ? "/{$productId}" : '');
            $fullPath = $path . '/' . $filename;

            // Resize and optimize image before storing
            $processedImage = $this->processImage($file);

            // Store the processed image
            $this->disk->put($fullPath, $processedImage);

            return $fullPath;
        } catch (\Exception $e) {
            \Log::error('Image upload failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Process image (resize, optimize)
     *
     * @param UploadedFile $file
     * @return string
     */
    private function processImage(UploadedFile $file): string
    {
        try {
            // Use basic GD processing if Intervention Image is not available
            $image = imagecreatefromstring(file_get_contents($file->getRealPath()));

            // Get original dimensions
            $originalWidth = imagesx($image);
            $originalHeight = imagesy($image);

            // Set max dimensions
            $maxWidth = 800;
            $maxHeight = 800;

            // Calculate new dimensions
            $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
            $newWidth = round($originalWidth * $ratio);
            $newHeight = round($originalHeight * $ratio);

            // Create new image
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG
            if ($file->getClientOriginalExtension() === 'png') {
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);
                $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
                imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, $transparent);
            }

            // Resize image
            imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

            // Start output buffering
            ob_start();

            // Output image based on type
            switch ($file->getClientOriginalExtension()) {
                case 'jpg':
                case 'jpeg':
                    imagejpeg($resizedImage, null, 85);
                    break;
                case 'png':
                    imagepng($resizedImage, null, 8);
                    break;
                case 'gif':
                    imagegif($resizedImage);
                    break;
                default:
                    imagejpeg($resizedImage, null, 85);
            }

            $imageData = ob_get_contents();
            ob_end_clean();

            // Clean up memory
            imagedestroy($image);
            imagedestroy($resizedImage);

            return $imageData;
        } catch (\Exception $e) {
            // Fallback: return original file content
            return file_get_contents($file->getRealPath());
        }
    }

    /**
     * Delete images
     *
     * @param array $imagePaths
     * @return bool
     */
    public function deleteImages(array $imagePaths): bool
    {
        try {
            foreach ($imagePaths as $path) {
                if ($this->disk->exists($path)) {
                    $this->disk->delete($path);
                }
            }
            return true;
        } catch (\Exception $e) {
            \Log::error('Image deletion failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get full URL for an image path
     *
     * @param string $path
     * @return string
     */
    public function getImageUrl(string $path): string
    {
        return $this->disk->url($path);
    }
}