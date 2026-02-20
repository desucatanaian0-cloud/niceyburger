<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$flash = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order_id'])) {
    $cancel_order_id = (int)$_POST['cancel_order_id'];
    $cancel_stmt = $pdo->prepare(
        "UPDATE orders
         SET status = 'cancelled'
         WHERE id = ?
           AND user_id = ?
           AND status = 'pending'
           AND TIMESTAMPDIFF(MINUTE, order_date, NOW()) < 10"
    );
    $cancel_stmt->execute([$cancel_order_id, $_SESSION['user_id']]);

    if ($cancel_stmt->rowCount() > 0) {
        $_SESSION['order_flash'] = ['type' => 'success', 'message' => 'Order cancelled successfully.'];
    } else {
        $_SESSION['order_flash'] = ['type' => 'error', 'message' => 'Order can only be cancelled within 10 minutes while pending.'];
    }

    header("Location: orders.php");
    exit;
}

if (isset($_SESSION['order_flash'])) {
    $flash = $_SESSION['order_flash'];
    unset($_SESSION['order_flash']);
}

// Handle AJAX Order Details
if (isset($_GET['ajax']) && isset($_GET['order_id'])) {
    $order_id = (int)$_GET['order_id'];
    // Verify order belongs to user
    $stmt = $pdo->prepare("SELECT oi.*, p.name, p.image FROM order_items oi 
                           JOIN products p ON oi.product_id = p.id 
                           JOIN orders o ON oi.order_id = o.id
                           WHERE oi.order_id = ? AND o.user_id = ?");
    $stmt->execute([$order_id, $_SESSION['user_id']]);
    $items = $stmt->fetchAll();
    echo json_encode($items);
    exit;
}

$stmt = $pdo->prepare(
    "SELECT o.*,
            (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) AS item_count,
            TIMESTAMPDIFF(MINUTE, o.order_date, NOW()) AS minutes_since_order
     FROM orders o
     WHERE user_id = ?
     ORDER BY order_date DESC"
);
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Burger App</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background:
                linear-gradient(135deg, rgba(10, 10, 10, 0.68), rgba(24, 24, 24, 0.74)),
                url('assets/images/bg.jpg') center center / cover no-repeat fixed !important;
        }

        .swal-pill-toast {
            background: #ffc107 !important;
            color: #000 !important;
            border-radius: 999px !important;
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.28) !important;
            padding: 10px 20px !important;
            min-height: auto !important;
        }

        .swal-pill-toast.swal-pill-toast-error {
            background: #ff4d4f !important;
            color: #fff !important;
        }

        .swal-pill-title {
            font-size: 0.95rem !important;
            font-weight: 700 !important;
            margin: 0 !important;
            line-height: 1.25 !important;
        }

        .swal-order-popup {
            width: min(90vw, 300px) !important;
            background: #232326 !important;
            color: #fff !important;
            border-radius: 14px !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            padding: 12px !important;
        }

        .swal-order-title {
            font-size: 1rem !important;
            margin: 0 0 4px !important;
        }

        .swal-order-text {
            font-size: 0.82rem !important;
        }

        .swal-order-confirm,
        .swal-order-cancel {
            font-size: 0.76rem !important;
            padding: 7px 12px !important;
            border-radius: 8px !important;
        }

        @media (max-width: 480px) {
            .swal-pill-title {
                font-size: 0.86rem !important;
            }

            .swal-order-text {
                font-size: 0.78rem !important;
            }
        }
    </style>
</head>
<body>
    <header>
        <button class="icon-btn" onclick="history.back()"><i class="fas fa-arrow-left"></i></button>
        <div class="header-title">My Orders</div>
        <div style="width: 30px;"></div>
    </header>

    <div class="orders-container">
        <?php if (empty($orders)): ?>
            <div class="empty-orders">
                <i class="fas fa-receipt"></i>
                <p>You haven't placed any orders yet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <?php $can_cancel = ($order['status'] === 'pending' && (int)$order['minutes_since_order'] < 10); ?>
                <?php $cancel_window_expired = ($order['status'] === 'pending' && !$can_cancel); ?>
                <div class="order-card">
                    <div class="order-header">
                        <span class="order-id">Order #<?php echo $order['id']; ?></span>
                        <span class="order-price">₱<?php echo number_format($order['total_price'], 2); ?></span>
                    </div>
                    <div class="order-meta">
                        <span><i class="fas fa-calendar-alt"></i> <?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></span>
                        <span><i class="fas fa-shopping-bag"></i> <?php echo $order['item_count']; ?> items</span>
                    </div>
                    <div class="order-footer">
                        <span class="order-status <?php echo strtolower($order['status']); ?>"><?php echo ucfirst($order['status']); ?></span>
                        <div class="order-actions">
                            <?php if ($can_cancel): ?>
                                <form method="POST" class="cancel-order-form">
                                    <input type="hidden" name="cancel_order_id" value="<?php echo $order['id']; ?>">
                                    <button type="button" class="btn-cancel" onclick="confirmCancel(this.form)">Cancel</button>
                                </form>
                            <?php elseif ($cancel_window_expired): ?>
                                <button type="button" class="btn-cancel disabled" disabled title="Cancellation time expired">Cancel Expired</button>
                            <?php endif; ?>

                            <button onclick="viewOrderDetails(<?php echo $order['id']; ?>)" class="btn-view">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Order Details Modal -->
    <div class="modal" id="order-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="order-modal-title">Order Details</h2>
                <button class="close-modal" onclick="closeOrderModal()">&times;</button>
            </div>
            <div id="order-items-container">
                <!-- Items will be loaded here -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const orderModal = document.getElementById('order-modal');
        const itemsContainer = document.getElementById('order-items-container');
        const orderFlash = <?php echo json_encode($flash); ?>;

        function showMiniAlert(type, title, text) {
            const isError = type === 'error';
            Swal.fire({
                toast: true,
                position: 'bottom',
                title: text || title,
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: false,
                customClass: {
                    popup: isError ? 'swal-pill-toast swal-pill-toast-error' : 'swal-pill-toast',
                    title: 'swal-pill-title'
                },
                showClass: {
                    popup: 'swal2-show'
                },
                hideClass: {
                    popup: 'swal2-hide'
                }
            });
        }

        function confirmCancel(form) {
            Swal.fire({
                icon: 'warning',
                title: 'Cancel order?',
                text: 'This action cannot be undone.',
                showCancelButton: true,
                confirmButtonText: 'Yes, cancel',
                cancelButtonText: 'Keep order',
                background: '#232326',
                color: '#ffffff',
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#3a3a3f',
                customClass: {
                    popup: 'swal-order-popup',
                    title: 'swal-order-title',
                    htmlContainer: 'swal-order-text',
                    confirmButton: 'swal-order-confirm',
                    cancelButton: 'swal-order-cancel'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        if (orderFlash && orderFlash.type && orderFlash.message) {
            showMiniAlert(
                orderFlash.type === 'success' ? 'success' : 'error',
                orderFlash.type === 'success' ? 'Success' : 'Not allowed',
                orderFlash.message
            );
        }

        async function viewOrderDetails(orderId) {
            document.getElementById('order-modal-title').textContent = 'Order #' + orderId;
            itemsContainer.innerHTML = '<p style="text-align: center; color: var(--text-gray);">Loading...</p>';
            
            orderModal.style.display = 'flex';
            setTimeout(() => orderModal.classList.add('show'), 10);

            try {
                const response = await fetch(`orders.php?ajax=1&order_id=${orderId}`);
                const items = await response.json();
                
                if (items.length > 0) {
                    itemsContainer.innerHTML = items.map(item => `
                        <div class="cart-item">
                            <img src="assets/images/${item.image}" alt="${item.name}" class="cart-item-img">
                            <div class="cart-item-info">
                                <div class="cart-item-name">${item.name}</div>
                                <div class="cart-item-price">₱${parseFloat(item.price).toFixed(2)} x ${item.quantity}</div>
                            </div>
                            <div style="font-weight: 600;">₱${(item.price * item.quantity).toFixed(2)}</div>
                        </div>
                    `).join('');
                } else {
                    itemsContainer.innerHTML = '<p style="text-align: center; color: var(--text-gray);">No items found.</p>';
                }
            } catch (error) {
                console.error('Error:', error);
                itemsContainer.innerHTML = '<p style="text-align: center; color: #ff4444;">Error loading details.</p>';
            }
        }

        function closeOrderModal() {
            orderModal.classList.remove('show');
            setTimeout(() => orderModal.style.display = 'none', 300);
        }

        window.onclick = (event) => {
            if (event.target == orderModal) closeOrderModal();
        };
    </script>
    <nav class="bottom-nav">
        <a href="index.php" class="nav-item">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="favorites.php" class="nav-item">
            <i class="fas fa-heart"></i>
            <span>Favorites</span>
        </a>
        <a href="orders.php" class="nav-item active">
            <i class="fas fa-receipt"></i>
            <span>Orders</span>
        </a>
        <a href="profile.php" class="nav-item">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
    </nav>
</body>
</html>
