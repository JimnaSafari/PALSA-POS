<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class FileUploadService
{
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'application/pdf'
    ];

    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

    public function uploadProductImage(UploadedFile $file, ?string $oldFileName = null): string
    {
        $this->validateFile($file);
        
        // Delete old file if exists
        if ($oldFileName && Storage::disk('public')->exists("products/{$oldFileName}")) {
            Storage::disk('public')->delete("products/{$oldFileName}");
        }

        // Generate secure filename
        $filename = $this->generateSecureFilename($file);
        
        // Resize and optimize image
        $image = Image::make($file->getRealPath());
        $image->resize(800, 600, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        
        // Save optimized image
        $path = "products/{$filename}";
        Storage::disk('public')->put($path, $image->encode('jpg', 85));
        
        return $filename;
    }

    public function uploadPaySlip(UploadedFile $file): string
    {
        $this->validateFile($file);
        
        $filename = $this->generateSecureFilename($file);
        $path = "payslips/{$filename}";
        
        Storage::disk('public')->putFileAs('payslips', $file, $filename);
        
        return $filename;
    }

    public function uploadUserProfile(UploadedFile $file, ?string $oldFileName = null): string
    {
        $this->validateFile($file);
        
        // Delete old file if exists
        if ($oldFileName && Storage::disk('public')->exists("profiles/{$oldFileName}")) {
            Storage::disk('public')->delete("profiles/{$oldFileName}");
        }

        $filename = $this->generateSecureFilename($file);
        
        // Resize profile image
        $image = Image::make($file->getRealPath());
        $image->fit(300, 300);
        
        $path = "profiles/{$filename}";
        Storage::disk('public')->put($path, $image->encode('jpg', 90));
        
        return $filename;
    }

    private function validateFile(UploadedFile $file): void
    {
        // Check file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \InvalidArgumentException('File size exceeds maximum allowed size of 5MB.');
        }

        // Check MIME type
        if (!in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES)) {
            throw new \InvalidArgumentException('File type not allowed.');
        }

        // Additional security checks
        $this->performSecurityChecks($file);
    }

    private function performSecurityChecks(UploadedFile $file): void
    {
        // Check for executable files
        $dangerousExtensions = ['php', 'exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js'];
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (in_array($extension, $dangerousExtensions)) {
            throw new \InvalidArgumentException('File type not allowed for security reasons.');
        }

        // Check file content (basic)
        $fileContent = file_get_contents($file->getRealPath());
        $suspiciousPatterns = ['<?php', '<script', 'javascript:', 'vbscript:'];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (stripos($fileContent, $pattern) !== false) {
                throw new \InvalidArgumentException('File contains suspicious content.');
            }
        }
    }

    private function generateSecureFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('Y-m-d_H-i-s');
        $random = Str::random(8);
        
        return "{$timestamp}_{$random}.{$extension}";
    }

    public function deleteFile(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        
        return false;
    }
}