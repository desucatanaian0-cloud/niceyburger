<?php
session_start();
require_once 'config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

// Check if already favorited
$stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
$stmt->execute([$user_id, $product_id]);
$favorite = $stmt->fetch();

if ($favorite) {
    // Remove from favorites
    $delete_stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND product_id = ?");
    $delete_stmt->execute([$user_id, $product_id]);
    echo json_encode(['success' => true, 'action' => 'removed']);
} else {
    // Add to favorites
    $insert_stmt = $pdo->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
    try {
        $insert_stmt->execute([$user_id, $product_id]);
        echo json_encode(['success' => true, 'action' => 'added']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}
?>
