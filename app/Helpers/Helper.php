/**
 * Clone image with special characters in filename to a safe name for sharing
 * 
 * @param string $originalPath Original path to image file
 * @param string $type Type of content (threads, track, page)
 * @return string URL to safe image
 */
function rrt_get_safe_image_url($originalPath, $type = 'general') {
    if (empty($originalPath)) {
        return asset('style2/img/1N2Logo 2.png');
    }
    
    // Get filename from path
    $filename = basename($originalPath);
    $fileInfo = pathinfo($filename);
    $filenameWithoutExt = $fileInfo['filename'];
    $extension = isset($fileInfo['extension']) ? $fileInfo['extension'] : '';
    
    // Check if filename contains non-ASCII characters
    if (preg_match('/[^\x20-\x7e]/', $filenameWithoutExt)) {
        // Create safe filename - sanitize and add hash to ensure uniqueness
        $safeFilename = Str::slug($filenameWithoutExt);
        if (empty($safeFilename)) {
            $safeFilename = 'image';
        }
        $safeFilename .= '_' . substr(md5($filenameWithoutExt), 0, 8);
        if (!empty($extension)) {
            $safeFilename .= '.' . $extension;
        }
        
        // Determine paths
        $safeDir = public_path('uploads/safe_images/' . $type);
        $safePath = $safeDir . '/' . $safeFilename;
        $safeUrl = url('public/uploads/safe_images/' . $type . '/' . $safeFilename);
        
        // Determine original full path based on type
        $baseUploadPath = public_path('uploads');
        switch ($type) {
            case 'threads':
                $originalFullPath = $baseUploadPath . '/threads/' . $filename;
                break;
            case 'tracks':
                $originalFullPath = $baseUploadPath . '/tracks/' . $filename;
                break;
            case 'page':
                $originalFullPath = $baseUploadPath . '/page/' . $filename;
                break;
            default:
                $originalFullPath = $baseUploadPath . '/' . $filename;
        }
        
        // Create directory if it doesn't exist
        if (!file_exists($safeDir)) {
            mkdir($safeDir, 0777, true);
        }
        
        // If safe file doesn't exist yet, create a copy
        if (!file_exists($safePath) && file_exists($originalFullPath)) {
            copy($originalFullPath, $safePath);
        }
        
        return $safeUrl;
    }
    
    // If filename doesn't contain special characters, return original URL
    switch ($type) {
        case 'threads':
            return url('public/uploads/threads/' . $filename);
        case 'tracks':
            return url('public/uploads/tracks/' . $filename);
        case 'page':
            return url('public/uploads/page/' . $filename);
        default:
            return url('public/uploads/' . $filename);
    }
} 