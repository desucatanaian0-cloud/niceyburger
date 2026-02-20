<?php
session_start();
require_once 'config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

try {
    if ($action === 'update_profile') {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->execute([$username, $email, $user_id]);
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    } elseif ($action === 'update_address') {
        $address = $_POST['address'] ?? '';
        
        $stmt = $pdo->prepare("UPDATE users SET address = ? WHERE id = ?");
        $stmt->execute([$address, $user_id]);
        echo json_encode(['success' => true, 'message' => 'Address updated successfully']);
    } elseif ($action === 'update_payment') {
        $payment_method = $_POST['payment_method'] ?? '';
        
        $stmt = $pdo->prepare("UPDATE users SET payment_method = ? WHERE id = ?");
        $stmt->execute([$payment_method, $user_id]);
        echo json_encode(['success' => true, 'message' => 'Payment method updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
