<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TestIdVerificationController extends Controller
{
    public function testFileStorage(Request $request)
    {
        try {
            $file = $request->file('test_file');
            
            if (!$file) {
                return response()->json([
                    'success' => false,
                    'message' => 'No file uploaded'
                ], 400);
            }
            
            // Store file
            $path = $file->store('temp/test', 'public');
            
            // Verify storage
            $exists = Storage::disk('public')->exists($path);
            $fullPath = Storage::disk('public')->path($path);
            $fileExists = file_exists($fullPath);
            $readable = is_readable($fullPath);
            $size = filesize($fullPath);
            
            // Try to read content
            $content = file_get_contents($fullPath);
            $contentLength = strlen($content);
            
            // Try getimagesize
            $imageInfo = false;
            try {
                $imageInfo = @getimagesize($fullPath);
            } catch (\Exception $e) {
                $imageInfo = 'Error: ' . $e->getMessage();
            }
            
            // Try getimagesizefromstring
            $imageInfoFromString = false;
            try {
                if ($content && strlen($content) > 0) {
                    $imageInfoFromString = @getimagesizefromstring($content);
                }
            } catch (\Exception $e) {
                $imageInfoFromString = 'Error: ' . $e->getMessage();
            }
            
            return response()->json([
                'success' => true,
                'storage_path' => $path,
                'full_path' => $fullPath,
                'storage_exists' => $exists,
                'file_exists' => $fileExists,
                'readable' => $readable,
                'file_size' => $size,
                'content_length' => $contentLength,
                'content_preview' => substr($content, 0, 100),
                'getimagesize' => $imageInfo,
                'getimagesizefromstring' => is_array($imageInfoFromString) ? [
                    'width' => $imageInfoFromString[0] ?? null,
                    'height' => $imageInfoFromString[1] ?? null,
                    'type' => $imageInfoFromString[2] ?? null,
                ] : $imageInfoFromString,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}



