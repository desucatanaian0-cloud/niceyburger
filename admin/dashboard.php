<?php
session_start();
require_once '../config/db.php';

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch all products
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
$products = $stmt->fetchAll();

// Handle product deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: dashboard.php");
    exit;
}

// Handle AJAX Product Operations
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $response = ['success' => false, 'message' => ''];
    
    if ($_POST['action'] == 'add' || $_POST['action'] == 'edit') {
        $name = trim($_POST['name']);
        $category_id = (int)$_POST['category_id'];
        $price = (float)$_POST['price'];
        $description = trim($_POST['description']);
        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;

        if (empty($name) || empty($price)) {
            $response['message'] = "Name and Price are required.";
        } else {
            $image_name = '';
            if ($_POST['action'] == 'edit') {
                $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
                $stmt->execute([$id]);
                $image_name = $stmt->fetchColumn();
            }

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $target_dir = "../assets/images/";
                $new_image_name = time() . '_' . basename($_FILES["image"]["name"]);
                $target_file = $target_dir . $new_image_name;
                
                if (getimagesize($_FILES["image"]["tmp_name"])) {
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        $image_name = $new_image_name;
                    }
                }
            }

            if ($_POST['action'] == 'add') {
                $stmt = $pdo->prepare("INSERT INTO products (name, category_id, price, description, image) VALUES (?, ?, ?, ?, ?)");
                $success = $stmt->execute([$name, $category_id, $price, $description, $image_name]);
            } else {
                $stmt = $pdo->prepare("UPDATE products SET name = ?, category_id = ?, price = ?, description = ?, image = ? WHERE id = ?");
                $success = $stmt->execute([$name, $category_id, $price, $description, $image_name, $id]);
            }

            if ($success) {
                $response['success'] = true;
                $response['message'] = "Product " . ($_POST['action'] == 'add' ? "added" : "updated") . " successfully!";
            } else {
                $response['message'] = "Database error.";
            }
        }
    }
    
    if (isset($_GET['ajax'])) {
        echo json_encode($response);
        exit;
    }
}

// Fetch categories for modal
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Burger App</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            max-width: 800px;
            padding-bottom: 20px;
            background:
                linear-gradient(135deg, rgba(10, 10, 10, 0.68), rgba(24, 24, 24, 0.74)),
                url('../assets/images/bg.jpg') center center / cover no-repeat fixed !important;
        }

        .add-product-btn {
            width: 100%;
            margin: 10px 0 18px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #ffd54f, #ffc107);
            color: #161616;
            font-weight: 700;
            font-size: 0.96rem;
            padding: 12px 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(255, 193, 7, 0.24);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .add-product-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 24px rgba(255, 193, 7, 0.3);
        }

        .add-product-btn .btn-plus {
            width: 26px;
            height: 26px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.14);
        }

        #product-modal {
            padding: 12px;
            box-sizing: border-box;
        }

        #product-modal .modal-content.product-modal-content {
            width: min(94vw, 470px);
            max-height: calc(100vh - 70px);
            overflow-y: auto;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.16);
            background: rgba(30, 30, 35, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 24px 55px rgba(0, 0, 0, 0.46);
            padding: 16px 14px 14px;
        }

        #product-modal .modal-header {
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        #product-modal .modal-header h2 {
            font-size: 1.05rem;
            margin: 0;
        }

        #product-modal .close-modal {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.22);
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            font-size: 1.2rem;
            line-height: 1;
            cursor: pointer;
        }

        #product-modal .form-group {
            margin-bottom: 11px;
        }

        #product-modal .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.88);
            font-weight: 500;
        }

        #product-modal .form-control {
            background: rgba(255, 255, 255, 0.09);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 12px;
            color: #fff;
            min-height: 42px;
            padding: 10px 12px;
        }

        #product-modal .form-control:focus {
            border-color: rgba(255, 193, 7, 0.95);
            box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.17);
            outline: none;
        }

        #product-modal textarea.form-control {
            min-height: 85px;
            resize: vertical;
        }

        #product-modal #current-image-preview {
            border: 1px dashed rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.03);
        }

        #product-modal #submit-btn {
            margin-top: 8px;
            min-height: 44px;
            font-weight: 700;
        }

        @media (max-width: 420px) {
            #product-modal {
                padding: 8px;
            }

            #product-modal .modal-content.product-modal-content {
                width: 96vw;
                max-height: calc(100vh - 56px);
                border-radius: 15px;
                padding: 12px 10px;
            }

            #product-modal .modal-header h2 {
                font-size: 0.95rem;
            }

            #product-modal .form-group label {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>Admin Panel</h1>
        <div>
            <a href="../index.php" style="color: var(--accent); text-decoration: none; margin-right: 15px;">View Site</a>
            <a href="../logout.php" style="color: #ff4444; text-decoration: none;">Logout</a>
        </div>
    </div>

    <div class="product-list">
        <div class="admin-stats">
            <div class="stat-box">
                <span class="value"><?php echo count($products); ?></span>
                <span class="label">Total Products</span>
            </div>
            <div class="stat-box">
                <?php
                $stmt_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
                $pending_orders = $stmt_orders->fetchColumn();
                ?>
                <span class="value"><?php echo $pending_orders; ?></span>
                <span class="label">Pending Orders</span>
            </div>
        </div>

        <button onclick="openAddModal()" class="add-product-btn">
            <span class="btn-plus"><i class="fas fa-plus"></i></span>
            <span>Add New Product</span>
        </button>
        
        <div id="products-container">
            <?php foreach ($products as $product): ?>
                <div class="admin-card" id="product-<?php echo $product['id']; ?>">
                    <img src="../assets/images/<?php echo $product['image']; ?>" alt="">
                    <div class="admin-card-info">
                        <div class="name"><?php echo htmlspecialchars($product['name']); ?></div>
                        <div class="meta"><?php echo $product['category_name']; ?> • ₱<?php echo number_format($product['price'], 2); ?></div>
                    </div>
                    <div class="admin-actions">
                        <button onclick='openEditModal(<?php echo json_encode($product); ?>)' class="btn-icon btn-edit" title="Edit">
                            <i class="fas fa-pen"></i>
                        </button>
                        <a href="dashboard.php?delete=<?php echo $product['id']; ?>" class="btn-icon btn-delete" onclick="return confirm('Are you sure?')" title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Product Modal -->
    <div class="modal" id="product-modal">
        <div class="modal-content product-modal-content">
            <div class="modal-header">
                <h2 id="modal-title">Add Product</h2>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <form id="product-form" onsubmit="handleProductSubmit(event)" enctype="multipart/form-data">
                <input type="hidden" name="action" id="form-action" value="add">
                <input type="hidden" name="id" id="product-id">
                
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" id="p-name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" id="p-category" class="form-control">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" id="p-price" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="p-desc" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label id="image-label">Product Image</label>
                    <div id="current-image-preview" style="margin-bottom: 10px; display: none; justify-content: center;">
                        <img src="" id="preview-img" style="width: 80px; height: 80px; object-fit: contain; border: 1px solid #444; border-radius: 10px;">
                    </div>
                    <input type="file" name="image" id="p-image" class="form-control" accept="image/*">
                </div>
                
                <button type="submit" class="auth-btn" id="submit-btn">Save Product</button>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('product-modal');
        const form = document.getElementById('product-form');
        const imageInput = document.getElementById('p-image');
        const previewWrap = document.getElementById('current-image-preview');
        const previewImg = document.getElementById('preview-img');

        function resetImagePreview() {
            if (imageInput) imageInput.value = '';
            if (previewImg) previewImg.src = '';
            if (previewWrap) previewWrap.style.display = 'none';
        }

        if (imageInput) {
            imageInput.addEventListener('change', (e) => {
                const file = e.target.files && e.target.files[0];
                if (!file) {
                    // If user cancels selection, keep current preview as-is
                    return;
                }

                // Preview newly selected image
                const url = URL.createObjectURL(file);
                previewImg.src = url;
                previewWrap.style.display = 'flex';

                previewImg.onload = () => {
                    URL.revokeObjectURL(url);
                };
            });
        }

        function openAddModal() {
            document.getElementById('modal-title').textContent = 'Add New Product';
            document.getElementById('form-action').value = 'add';
            document.getElementById('product-id').value = '';
            resetImagePreview();
            document.getElementById('image-label').textContent = 'Product Image';
            form.reset();
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function openEditModal(product) {
            document.getElementById('modal-title').textContent = 'Edit Product';
            document.getElementById('form-action').value = 'edit';
            document.getElementById('product-id').value = product.id;
            document.getElementById('p-name').value = product.name;
            document.getElementById('p-category').value = product.category_id;
            document.getElementById('p-price').value = product.price;
            document.getElementById('p-desc').value = product.description;

            // Always clear the file input when opening edit modal
            if (imageInput) imageInput.value = '';
            
            if (product.image) {
                previewImg.src = '../assets/images/' + product.image;
                previewWrap.style.display = 'flex';
                document.getElementById('image-label').textContent = 'Update Image (Optional)';
            } else {
                resetImagePreview();
                document.getElementById('image-label').textContent = 'Product Image';
            }
            
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function closeModal() {
            modal.classList.remove('show');
            setTimeout(() => modal.style.display = 'none', 300);
        }

        async function handleProductSubmit(e) {
            e.preventDefault();
            const formData = new FormData(form);
            
            try {
                const response = await fetch('dashboard.php?ajax=1', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    location.reload(); // Refresh to show changes
                } else {
                    alert(data.message || 'An error occurred');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred');
            }
        }

        window.onclick = (event) => {
            if (event.target == modal) closeModal();
        };
    </script>
</body>
</html>
