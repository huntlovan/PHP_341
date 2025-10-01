<?php
// Classic procedural upload example (educational). For production prefer ImageUploader.php class.

$target_dir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
if (!is_dir($target_dir)) {
    @mkdir($target_dir, 0755, true);
}

$uploadOk = 1;
$messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileToUpload'])) {
    $originalName = $_FILES['fileToUpload']['name'];
    $target_file = $target_dir . basename($originalName);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = @getimagesize($_FILES['fileToUpload']['tmp_name']);
    if ($check !== false) {
        $messages[] = 'File is an image - ' . ($check['mime'] ?? 'unknown') . '.';
    } else {
        $messages[] = 'File is not an image.';
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        $messages[] = 'Sorry, file already exists.';
        $uploadOk = 0;
    }

    // Check file size (500KB)
    if ($_FILES['fileToUpload']['size'] > 500000) {
        $messages[] = 'Sorry, your file is too large.';
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ['jpg','jpeg','png','gif'], true)) {
        $messages[] = 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.';
        $uploadOk = 0;
    }

    if ($uploadOk === 0) {
        $messages[] = 'Sorry, your file was not uploaded.';
    } else {
        if (is_uploaded_file($_FILES['fileToUpload']['tmp_name']) &&
            move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $target_file)) {
            $messages[] = 'The file ' . htmlspecialchars(basename($originalName)) . ' has been uploaded.';
        } else {
            $messages[] = 'Sorry, there was an error uploading your file.';
        }
    }
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $messages[] = 'No file received.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Basic Upload Example</title>
<style>
 body { font-family: system-ui, Arial, sans-serif; margin:2rem; }
 .messages { margin-top:1rem; padding:.75rem 1rem; background:#f8fafc; border:1px solid #cbd5e1; border-radius:6px; }
 .messages p { margin:.4rem 0; }
 form { border:1px solid #ddd; padding:1rem 1.25rem; border-radius:6px; max-width:420px; }
 button { cursor:pointer; }
</style>
</head>
<body>
<h1>Basic Image Upload (Procedural)</h1>
<form action="" method="post" enctype="multipart/form-data">
  <label>Select image to upload:</label>
  <input type="file" name="fileToUpload" required>
  <button type="submit" name="submit">Upload Image</button>
  <p style="font-size:.85rem;color:#555;">Allowed: JPG, JPEG, PNG, GIF. Max size: 500KB.</p>
</form>
<?php if (!empty($messages)): ?>
<div class="messages">
  <?php foreach ($messages as $m): ?>
    <p><?php echo htmlspecialchars($m); ?></p>
  <?php endforeach; ?>
</div>
<?php endif; ?>
</body>
</html>