<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CkeditorController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = Str::slug($filename) . '_' . time() . '.' . $file->getClientOriginalExtension();

            // Create directory if it doesn't exist
            $uploadPath = public_path('uploads/ckeditor');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $file->move($uploadPath, $filename);

            // Return URL without 'assets'
            $url = url('public/uploads/ckeditor/' . $filename);
            
            return response()->json([
                'uploaded' => 1,
                'fileName' => $filename,
                'url' => $url
            ]);
        }

        return response()->json([
            'uploaded' => 0,
            'error' => [
                'message' => 'No file uploaded'
            ]
        ]);
    }

    public function browse()
    {
        $uploadPath = public_path('uploads/ckeditor');
        $files = [];

        if (file_exists($uploadPath)) {
            $fileItems = array_diff(scandir($uploadPath), ['.', '..']);
            
            foreach ($fileItems as $file) {
                $filePath = $uploadPath . '/' . $file;
                if (is_file($filePath)) {
                    $url = url('public/uploads/ckeditor/' . $file);
                    $files[] = [
                        'name' => $file,
                        'url' => $url,
                        'size' => $this->formatSize(filesize($filePath)),
                        'modified' => date('Y-m-d H:i:s', filemtime($filePath))
                    ];
                }
            }
        }

        return view('admin.pages.ckeditor.browse', compact('files'));
    }

    private function formatSize($size)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }
} 