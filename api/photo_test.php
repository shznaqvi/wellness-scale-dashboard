<?php
// Define the upload directory
define('UPLOAD_DIR', 'uploads/');

// Initialize response message
$response = '';

// Ensure the upload directory exists
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];

    // Check for upload errors
    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $file['tmp_name'];
        $fileName = basename($file['name']); // Use basename to avoid directory traversal issues
        $fileSize = $file['size'];
        $fileType = $file['type'];

        // Define allowed file types and max size (optional)
        $allowedTypes = array('image/jpeg', 'image/png', 'image/gif');
        $maxSize = 5 * 1024 * 1024; // 5MB

        // Validate the file type
        if (in_array($fileType, $allowedTypes) && $fileSize <= $maxSize) {
            $destination = UPLOAD_DIR . $fileName;

            // Move the uploaded file to the desired directory
            if (move_uploaded_file($fileTmpPath, $destination)) {
                $response = 'File uploaded successfully.';
            } else {
                $response = 'Failed to move uploaded file.';
            }
        } else {
            $response = 'Invalid file type or size exceeded.';
        }
    } else {
        $response = 'File upload error: ' . $file['error'];
    }
} else {
    $response = 'No file uploaded.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Upload Form</title>
</head>
<body>
    <h1>Upload an Image</h1>
    <?php if ($response): ?>
        <p><?php echo htmlspecialchars($response); ?></p>
    <?php endif; ?>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
        <label for="image">Choose an image to upload:</label>
        <input type="file" name="image" id="image" accept="image/*" required>
        <br><br>
        <input type="submit" value="Upload Image">
    </form>
</body>
</html>
