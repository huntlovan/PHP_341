<?php
require_once __DIR__ . '/ImageUploader.php';

// Configure uploader (adjust settings as needed per project)
$uploader = new ImageUploader([
    'uploadDir' => __DIR__ . '/uploads',
    'maxBytes' => 500 * 1024, // 500KB
    'allowedExtensions' => ['jpg','jpeg','png','gif'],
    'autoRename' => true,
]);

$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $uploader->upload($_FILES['fileToUpload'] ?? null);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Image Upload</title>
<style>
 body { font-family: system-ui, Arial, sans-serif; margin: 2rem; }
 .status { margin-top: 1rem; padding: .75rem 1rem; border-radius: 6px; }
 .status.success { background: #e6ffed; border: 1px solid #2ecc71; }
 .status.error { background: #ffecec; border: 1px solid #e74c3c; }
 form { border: 1px solid #ddd; padding: 1rem 1.25rem; border-radius: 6px; max-width: 420px; }
 label { display:block; margin-bottom:.5rem; font-weight:600; }
 input[type=file] { margin-bottom:1rem; }
 .details { font-size: .85rem; margin-top:.5rem; color:#555; }
</style>
</head>
<body>
<h1>Upload an Image</h1>
<form action="" method="post" enctype="multipart/form-data">
  <label for="fileToUpload">Select image to upload:</label>
  <input type="file" name="fileToUpload" id="fileToUpload" accept="image/*" required>
  <button type="submit" name="submit">Upload Image</button>
  <div class="details">Allowed types: JPG, JPEG, PNG, GIF. Max size: 500KB.</div>
</form>
<?php if ($result): ?>
  <?php if ($result['success']): ?>
    <div class="status success">
      <strong>Success!</strong><br>
      Stored as: <?php echo htmlspecialchars(basename($result['path'])); ?><br>
      Original Name: <?php echo htmlspecialchars($result['original']); ?><br>
      MIME: <?php echo htmlspecialchars($result['mime']); ?><br>
      Dimensions: <?php echo (int)$result['width']; ?>x<?php echo (int)$result['height']; ?> px<br>
      Size: <?php echo number_format($result['size']/1024,2); ?> KB
    </div>
    <p><img src="uploads/<?php echo rawurlencode($result['filename']); ?>" alt="Uploaded Image" style="max-width:300px; margin-top:1rem; border:1px solid #ccc; padding:4px;" /></p>
  <?php else: ?>
    <div class="status error">
      <strong>Error:</strong> <?php echo htmlspecialchars($result['error']); ?>
    </div>
  <?php endif; ?>
<?php endif; ?>
</body>
</html>