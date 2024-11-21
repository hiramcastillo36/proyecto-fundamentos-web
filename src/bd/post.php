<?php
// process_upload.php
include_once 'CAD.php';

header('Content-Type: application/json');
session_start();

try {
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        throw new Exception("Invalid request method");
    }

    if (!isset($_FILES['image']) || !isset($_POST['title']) || !isset($_POST['readTime']) ||
        !isset($_POST['description']) || !isset($_POST['content'])) {
        throw new Exception("Missing required fields");
    }

    $uploadDir = "../uploads/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $imageFile = $_FILES['image'];
    $fileName = uniqid() . '_' . basename($imageFile['name']);
    $targetPath = $uploadDir . $fileName;

    // Validate image
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($imageFile['type'], $allowedTypes)) {
        throw new Exception("Invalid file type");
    }

    if (!move_uploaded_file($imageFile['tmp_name'], $targetPath)) {
        throw new Exception("Failed to upload image");
    }

    $cad = new CAD();

    $result = $cad->createBlogPost($_POST['title'], $_POST['content'], $_SESSION['user_id'], $_POST['readTime'], $_POST['description'], $fileName);

    if (!$result) {
        throw new Exception("Failed to save post to database");
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
