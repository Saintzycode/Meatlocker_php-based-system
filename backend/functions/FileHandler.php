<?php
/**
 * FileHandler Class
 * Handles all file operations with proper validation and security
 */
class FileHandler {
    const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    const UPLOAD_DIR = 'assets/images/';

    /**
     * Validate and upload image file
     * @param array $file - $_FILES array
     * @param string $prefix - Prefix for the filename
     * @return string|false - Filename on success, false on failure
     */
    public static function uploadImage($file, $prefix = 'product') {
        // Check if file was uploaded without errors
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // Validate file size
        if ($file['size'] > self::MAX_FILE_SIZE) {
            error_log("File too large: {$file['size']} bytes");
            return false;
        }

        // Validate MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, self::ALLOWED_IMAGE_TYPES)) {
            error_log("Invalid MIME type: $mimeType");
            return false;
        }

        // Generate safe filename
        $extension = self::getExtensionFromMimeType($mimeType);
        $filename = $prefix . '_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;

        // Ensure upload directory exists
        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0755, true);
        }

        // Move uploaded file
        $uploadPath = self::UPLOAD_DIR . $filename;
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return $filename;
        }

        error_log("Failed to move uploaded file: {$file['tmp_name']}");
        return false;
    }

    /**
     * Get file extension from MIME type
     */
    private static function getExtensionFromMimeType($mimeType) {
        $mimeMap = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp'
        ];
        return $mimeMap[$mimeType] ?? 'jpg';
    }

    /**
     * Delete file if it exists
     */
    public static function deleteFile($filename) {
        $filepath = self::UPLOAD_DIR . $filename;
        if (file_exists($filepath) && unlink($filepath)) {
            return true;
        }
        return false;
    }

    /**
     * Read JSON file safely
     */
    public static function readJSON($filepath) {
        if (!file_exists($filepath)) {
            return [];
        }

        if (!is_readable($filepath)) {
            error_log("File not readable: $filepath");
            return [];
        }

        $content = file_get_contents($filepath);
        if ($content === false) {
            error_log("Failed to read file: $filepath");
            return [];
        }

        $decoded = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON decode error: " . json_last_error_msg());
            return [];
        }

        return $decoded ?: [];
    }

    /**
     * Write JSON file safely
     */
    public static function writeJSON($filepath, $data) {
        // Ensure directory exists
        $directory = dirname($filepath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Write to temporary file first
        $tempFile = $filepath . '.tmp';
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON encode error: " . json_last_error_msg());
            return false;
        }

        if (file_put_contents($tempFile, $json) === false) {
            error_log("Failed to write temp file: $tempFile");
            return false;
        }

        // Atomic rename
        if (!rename($tempFile, $filepath)) {
            error_log("Failed to rename temp file to: $filepath");
            @unlink($tempFile);
            return false;
        }

        return true;
    }

    /**
     * Validate directory path to prevent directory traversal
     */
    public static function validatePath($path) {
        $realPath = realpath($path);
        $baseDir = realpath(dirname(__DIR__));
        
        return $realPath && strpos($realPath, $baseDir) === 0;
    }
}
?>
