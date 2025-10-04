<?php
/**
 * ImageUploader.php
 *
 * This file demonstrates a basic reusable ImageUploader class
 * It's part of the PHP_341 class project. All contents are for educational and informational purposes only
 *
 * @package  PHP_341
 * @author   Hunter Lovan
 * @version  1.0.0
 * @link     http://kickshunter.com/WDV341/wdv341.php
 */
/**
 * Assignment & direction: 5-1: Image File uploader
 * Create a form page that will locate an image file on your client and use PHP to upload the image to a folder on your server. 
 * This could be a very useful feature for the final project. 
 * --------------------------------------
 * Reusable ImageUploader class
 * --------------------------------------
 * Features:
 *  - Configurable upload directory
 *  - Validate real image (MIME + getimagesize)
 *  - Allowed extensions filtering (default: jpg, jpeg, png, gif)
 *  - Max file size (default: 500KB)
 *  - Duplicate filename prevention (optional override)
 *  - Generates safe unique filename when requested
 *  - Returns structured result array
 *
 *  Typical usage:
 *   $uploader = new ImageUploader([
 *       'uploadDir' => __DIR__ . '/uploads',
 *       'maxBytes' => 500 * 1024,
 *       'allowedExtensions' => ['jpg','jpeg','png','gif'],
 *       'autoRename' => true, // to avoid collisions
 *   ]);
 *   $result = $uploader->upload($_FILES['fileToUpload'] ?? null);
 *   if ($result['success']) { echo 'Uploaded to: ' . $result['path']; } else { echo $result['error']; }
 */
class ImageUploader
{
    private string $uploadDir;
    private int $maxBytes;
    private array $allowedExtensions;
    private bool $autoRename;

    public function __construct(array $options = [])
    {
        $this->uploadDir = rtrim($options['uploadDir'] ?? (__DIR__ . '/uploads'), DIRECTORY_SEPARATOR);
        $this->maxBytes = (int)($options['maxBytes'] ?? (500 * 1024)); // 500KB default
        $this->allowedExtensions = array_map('strtolower', $options['allowedExtensions'] ?? ['jpg','jpeg','png','gif']);
        $this->autoRename = (bool)($options['autoRename'] ?? false);

        if (!is_dir($this->uploadDir)) {
            @mkdir($this->uploadDir, 0755, true);
        }
    }

    public function upload(?array $file): array
    {
        if (!$file || !isset($file['error'])) {
            return $this->errorResult('No file uploaded.');
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return $this->errorResult($this->codeToMessage($file['error']));
        }

        // Security: ensure uploaded via HTTP POST
        if (!is_uploaded_file($file['tmp_name'])) {
            return $this->errorResult('Potential file upload attack detected.');
        }

        $originalName = $file['name'];
        $size = (int)$file['size'];
        $tmp = $file['tmp_name'];

        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        // Validate extension
        if (!in_array($ext, $this->allowedExtensions, true)) {
            return $this->errorResult('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');
        }

        // Validate image authenticity
        $imageInfo = @getimagesize($tmp);
        if ($imageInfo === false) {
            return $this->errorResult('File is not a valid image.');
        }

        // Size check
        if ($size > $this->maxBytes) {
            return $this->errorResult('Sorry, your file is too large. Max ' . $this->formatBytes($this->maxBytes) . '.');
        }

        // Build target path
        $safeBase = $this->sanitizeFilename(pathinfo($originalName, PATHINFO_FILENAME));
        $targetName = $safeBase . '.' . $ext;
        $targetPath = $this->uploadDir . DIRECTORY_SEPARATOR . $targetName;

        // Duplicate handling
        if (file_exists($targetPath)) {
            if ($this->autoRename) {
                $counter = 1;
                do {
                    $targetName = $safeBase . '_' . $counter++ . '.' . $ext;
                    $targetPath = $this->uploadDir . DIRECTORY_SEPARATOR . $targetName;
                } while (file_exists($targetPath));
            } else {
                return $this->errorResult('Sorry, file already exists.');
            }
        }

        if (!move_uploaded_file($tmp, $targetPath)) {
            return $this->errorResult('Sorry, there was an error uploading your file.');
        }

        return [
            'success' => true,
            'path' => $targetPath,
            'filename' => $targetName,
            'original' => $originalName,
            'mime' => $imageInfo['mime'] ?? null,
            'size' => $size,
            'width' => $imageInfo[0] ?? null,
            'height' => $imageInfo[1] ?? null,
        ];
    }

    private function sanitizeFilename(string $name): string
    {
        $name = preg_replace('/[^A-Za-z0-9_-]+/', '-', $name);
        $name = trim($name, '-');
        return $name ?: 'image';
    }

    private function codeToMessage(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Uploaded file exceeds size limit.',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder on the server.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
            default => 'Unknown upload error.'
        };
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }

    private function errorResult(string $message): array
    {
        return ['success' => false, 'error' => $message];
    }
}

