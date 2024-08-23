<?php
// Update this to match the expected upload URL
define('UPLOAD_DIRECTORY', 'uploads/');
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

function getExtension($filename)
{
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

function saveBase64Image($base64Image, $filename)
{
    // Decode Base64 image
    $imageData = base64_decode($base64Image);

    // Check if decoding was successful
    if ($imageData === false) {
        return "Base64 decoding failed.";
    }

    // Check if the filename has a valid extension
    $extension = getExtension($filename);
    if (!in_array($extension, ALLOWED_IMAGE_EXTENSIONS)) {
        return "Invalid file extension.";
    }

    // Define the path to save the image
    $filePath = UPLOAD_DIRECTORY . $filename;

    // Save the decoded image to the specified path
    if (file_put_contents($filePath, $imageData) === false) {
        return "Failed to save the image.";
    }

    return "Success";
}

// Read the JSON payload from the request
$data = json_decode(file_get_contents("php://input"), true);

// Check if JSON decoding was successful
if ($data === null) {
    echo json_encode(["status" => "0", "error" => "Invalid JSON data."]);
    exit();
}

// Extract image data and filename from the request
$base64Image = $data['image'] ?? null;
$filename = $data['filename'] ?? null;

if (!$base64Image || !$filename) {
    echo json_encode(["status" => "0", "error" => "Missing image or filename."]);
    exit();
}

// Save the Base64 image
$result = saveBase64Image($base64Image, $filename);

// Return the result as JSON
if ($result === "Success") {
    echo json_encode(["status" => "1", "error" => "0", "message" => "File uploaded successfully."]);
} else {
    echo json_encode(["status" => "0", "error" => "1", "message" => $result]);
}
?>
