# Reusable Image Uploader

This mini component provides a reusable, self‑contained image upload workflow for PHP projects.

## Files
- `ImageUploader.php` – Core reusable class (validation + storage)
- `upload.php` – Example handler page that processes uploads and displays result
- `index.html` – Simple demo form that posts to `upload.php`
- `uploads/` – Destination directory for stored images (auto-created if missing)

## Features
- Real image validation using `getimagesize()` + `is_uploaded_file()`
- Allowed extension filter (default: jpg, jpeg, png, gif)
- Maximum file size (default: 500KB)
- Duplicate filename prevention (either reject or auto-rename with suffix)
- Automatic creation of target directory
- Sanitized, safe filenames
- Structured result array with meta (mime, dimensions, size)

## Basic Usage
```php
require_once __DIR__ . '/ImageUploader.php';
$uploader = new ImageUploader([
    'uploadDir' => __DIR__ . '/uploads',
    'maxBytes' => 500 * 1024,
    'allowedExtensions' => ['jpg','jpeg','png','gif'],
    'autoRename' => true, // set false to reject duplicates
]);
$result = $uploader->upload($_FILES['fileFieldName'] ?? null);
if ($result['success']) {
    echo 'Stored at: ' . $result['path'];
} else {
    echo 'Error: ' . $result['error'];
}
```

## Result Structure
On success:
```php
[
  'success' => true,
  'path' => '/abs/path/uploads/example.png',
  'filename' => 'example.png',
  'original' => 'Example.PNG',
  'mime' => 'image/png',
  'size' => 12345,       // bytes
  'width' => 640,
  'height' => 480,
]
```
On failure:
```php
['success' => false, 'error' => 'Readable message'];
```

## Security Notes
- Always keep the `uploads` directory outside the web root or restrict execution (e.g., add an `.htaccess` that disallows script execution) if users can upload arbitrary files.
- Consider further MIME validation (e.g., `finfo_file`) if security is critical.
- Limit dimensions if needed (you can extend the class to enforce max width/height).
- Log unexpected upload errors for auditing.

## Reuse in Another Project
1. Copy `ImageUploader.php` into your project (e.g., `lib/` or `src/` directory).
2. Create an `uploads` directory with proper permissions (0755 is typical).
3. Include and instantiate as shown above.
4. Adjust options: target directory, size, extensions, and whether to auto-rename.
5. Integrate into an existing form that has: `<form method="post" enctype="multipart/form-data">` and `<input type="file" name="fileFieldName">`.

## Extending
You can subclass `ImageUploader` to add:
- Dimension constraints
- Image optimization / compression
- Cloud storage (override the move logic to push to S3/Azure/etc.)
- Logging hooks

## License
Feel free to adapt for personal or educational use.
