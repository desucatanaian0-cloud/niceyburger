<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    $description = trim($_POST['description']);
    // $image = $_POST['image']; // Removed as we use $_FILES now

    if (empty($name) || empty($price)) {
        $error = "Name and Price are required.";
    } else {
        $image_name = $product['image']; // Keep old image by default
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../assets/images/";
            $new_image_name = time() . '_' . basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $new_image_name;
            
            // Check if image file is a actual image
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if($check !== false) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $image_name = $new_image_name;
                } else {
                    $error = "Sorry, there was an error uploading your file.";
                }
            } else {
                $error = "File is not an image.";
            }
        }

        if (empty($error)) {
            $stmt = $pdo->prepare("UPDATE products SET name = ?, category_id = ?, price = ?, description = ?, image = ? WHERE id = ?");
            if ($stmt->execute([$name, $category_id, $price, $description, $image_name, $id])) {
                $success = "Product updated successfully!";
                // Refresh product data
                $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->execute([$id]);
                $product = $stmt->fetch();
            } else {
                $error = "Failed to update product.";
            }
        }
    }
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Burger App</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <h1>Edit Product</h1>
        <?php if ($error): ?><p style="color: #ff4444;"><?php echo $error; ?></p><?php endif; ?>
        <?php if ($success): ?><p style="color: #44ff44;"><?php echo $success; ?></p><?php endif; ?>
        
        <form method="POST" class="auth-form" enctype="multipart/form-data">
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category_id" class="form-control">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $product['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo $cat['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Price</label>
                <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $product['price']; ?>" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label>Product Image (Leave blank to keep current)</label>
                <?php if ($product['image']): ?>
                    <div style="margin-bottom: 10px;">
                        <img src="../assets/images/<?php echo $product['image']; ?>" alt="Current image" style="width: 100px; height: 100px; object-fit: contain; border: 1px solid #444;">
                    </div>
                <?php endif; ?>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <button type="submit" class="auth-btn">Update Product</button>
            <a href="dashboard.php" class="auth-link">Back to Dashboard</a>
        </form>
    </div>
</body>
</html>
