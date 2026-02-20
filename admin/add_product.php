<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
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
        $image_name = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../assets/images/";
            $image_name = time() . '_' . basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $image_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            
            // Check if image file is a actual image
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if($check !== false) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    // Success
                } else {
                    $error = "Sorry, there was an error uploading your file.";
                }
            } else {
                $error = "File is not an image.";
            }
        }

        if (empty($error)) {
            $stmt = $pdo->prepare("INSERT INTO products (name, category_id, price, description, image) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $category_id, $price, $description, $image_name])) {
                $success = "Product added successfully!";
            } else {
                $error = "Failed to add product.";
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
    <title>Add Product - Burger App</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <h1>Add New Product</h1>
        <?php if ($error): ?><p style="color: #ff4444;"><?php echo $error; ?></p><?php endif; ?>
        <?php if ($success): ?><p style="color: #44ff44;"><?php echo $success; ?></p><?php endif; ?>
        
        <form method="POST" class="auth-form" enctype="multipart/form-data">
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category_id" class="form-control">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Price</label>
                <input type="number" step="0.01" name="price" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label>Product Image</label>
                <input type="file" name="image" class="form-control" accept="image/*" required>
            </div>
            <button type="submit" class="auth-btn">Add Product</button>
            <a href="dashboard.php" class="auth-link">Back to Dashboard</a>
        </form>
    </div>
</body>
</html>
