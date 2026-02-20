<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cart_data'])) {
    $cart_data = json_decode($_POST['cart_data'], true);
    
    if (empty($cart_data)) {
        header("Location: index.php");
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Calculate total
        $total_price = 0;
        foreach ($cart_data as $item) {
            $total_price += $item['price'] * $item['quantity'];
        }

        // Create order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$_SESSION['user_id'], $total_price]);
        $order_id = $pdo->lastInsertId();

        // Create order items
        $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($cart_data as $item) {
            $item_stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
        }

        $pdo->commit();
        
        // Clear cart in script via session flag or direct redirect and handle via JS
        $_SESSION['order_success'] = true;
        header("Location: order_success.php?id=" . $order_id);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Order failed: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit;
}
?>
