<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Handle profile image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    $upload_dir = 'assets/images/profiles/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $file = $_FILES['profile_image'];
    $file_name = time() . '_' . basename($file['name']);
    $target_file = $upload_dir . $file_name;
    
    // Validate image
    $check = getimagesize($file['tmp_name']);
    if ($check !== false) {
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            // Update database
            $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
            $stmt->execute([$file_name, $user_id]);
            
            // Update user data
            $user['profile_image'] = $file_name;
        }
    }
    
    // Redirect to prevent form resubmission
    header("Location: profile.php");
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Burger App</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background:
                linear-gradient(135deg, rgba(10, 10, 10, 0.68), rgba(24, 24, 24, 0.74)),
                url('assets/images/bg.jpg') center center / cover no-repeat fixed !important;
        }

        .modal {
            padding: 14px;
            box-sizing: border-box;
        }

        .modal .modal-content {
            width: min(92vw, 430px);
            max-height: calc(100vh - 90px);
            overflow-y: auto;
            padding: 20px 18px;
            border-radius: 22px;
            box-sizing: border-box;
        }

        .modal .modal-content h2 {
            margin: 0;
            line-height: 1.2;
        }

        .modal .form-group {
            margin-bottom: 12px;
        }

        .modal .form-group label {
            font-size: 0.95rem;
        }

        .modal .form-control {
            min-height: 46px;
            font-size: 0.95rem;
        }

        .modal textarea.form-control {
            min-height: 84px !important;
        }

        .modal .auth-btn {
            min-height: 46px;
            font-size: 1rem;
        }

        .profile-toast {
            position: fixed;
            left: 50%;
            bottom: 100px;
            transform: translateX(-50%);
            background: #ffc107;
            color: #000;
            padding: 10px 22px;
            border-radius: 999px;
            font-size: 0.95rem;
            font-weight: 700;
            z-index: 2500;
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.28);
            white-space: nowrap;
            max-width: 90vw;
            overflow: hidden;
            text-overflow: ellipsis;
            animation: profileToastInOut 2s forwards;
        }

        .profile-toast.error {
            background: #ff4d4f;
            color: #fff;
        }

        @keyframes profileToastInOut {
            0% {
                opacity: 0;
                transform: translate(-50%, 14px);
            }
            12% {
                opacity: 1;
                transform: translate(-50%, 0);
            }
            85% {
                opacity: 1;
                transform: translate(-50%, 0);
            }
            100% {
                opacity: 0;
                transform: translate(-50%, -12px);
            }
        }

        @media (max-width: 420px) {
            .modal {
                padding: 10px;
            }

            .modal .modal-content {
                width: 94vw;
                max-height: calc(100vh - 74px);
                padding: 14px 12px;
                border-radius: 16px;
            }

            .modal .modal-content h2 {
                font-size: 1.02rem !important;
            }

            .modal .icon-btn {
                font-size: 1rem !important;
            }

            .modal .form-group {
                margin-bottom: 10px;
            }

            .modal .form-group label {
                font-size: 0.88rem;
                margin-bottom: 5px;
                display: block;
            }

            .modal .form-control {
                min-height: 40px;
                font-size: 0.88rem;
                padding: 9px 11px;
            }

            .modal textarea.form-control {
                min-height: 72px !important;
            }

            .modal .auth-btn {
                min-height: 42px;
                font-size: 0.92rem;
                padding: 10px 12px;
            }

            .profile-toast {
                bottom: 90px;
                font-size: 0.86rem;
                padding: 9px 16px;
            }

        }
    </style>
</head>
<body>
    <header>
        <button class="icon-btn" onclick="history.back()"><i class="fas fa-arrow-left"></i></button>
        <div class="header-title">Profile</div>
        <div style="width: 30px;"></div>
    </header>

    <div style="padding: 40px 20px; text-align: center;">
        <div style="position: relative; display: inline-block; margin-bottom: 20px;">
            <div style="width: 100px; height: 100px; background: var(--bg-secondary); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; border: 3px solid var(--accent); overflow: hidden;">
                <?php if (!empty($user['profile_image'])): ?>
                    <img src="assets/images/profiles/<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <i class="fas fa-user" style="font-size: 3rem; color: var(--accent);"></i>
                <?php endif; ?>
            </div>
            <form action="profile.php" method="POST" enctype="multipart/form-data" style="position: absolute; bottom: 0; right: 0;">
                <label for="profile_image" style="background: var(--accent); color: #000; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.8rem; box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3); transition: var(--transition);">
                    <i class="fas fa-camera"></i>
                </label>
                <input type="file" id="profile_image" name="profile_image" accept="image/*" style="display: none;" onchange="this.form.submit()">
            </form>
        </div>
        <h2 style="margin-bottom: 5px;"><?php echo htmlspecialchars($user['username']); ?></h2>
        <p style="color: var(--text-gray); font-size: 0.9rem; margin-bottom: 30px;"><?php echo htmlspecialchars($user['email']); ?></p>

        <div style="text-align: left; width: 100%;">
            <div class="product-card" onclick="openModal('profile-modal')" style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; width: 100%; text-align: left; cursor: pointer;">
                <i class="fas fa-edit" style="color: var(--accent);"></i>
                <span>Edit Profile</span>
                <i class="fas fa-chevron-right" style="margin-left: auto; color: var(--text-gray);"></i>
            </div>
            <div class="product-card" onclick="openModal('address-modal')" style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; width: 100%; text-align: left; cursor: pointer;">
                <i class="fas fa-map-marker-alt" style="color: var(--accent);"></i>
                <span>Delivery Address</span>
                <i class="fas fa-chevron-right" style="margin-left: auto; color: var(--text-gray);"></i>
            </div>
            <div class="product-card" onclick="openModal('payment-modal')" style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; width: 100%; text-align: left; cursor: pointer;">
                <i class="fas fa-credit-card" style="color: var(--accent);"></i>
                <span>Payment Methods</span>
                <i class="fas fa-chevron-right" style="margin-left: auto; color: var(--text-gray);"></i>
            </div>

            <a href="profile.php?logout=1" style="text-decoration: none;">
                <button class="auth-btn" style="background: transparent; border: 1px solid #ff4444; color: #ff4444; margin-top: 20px;">Logout</button>
            </a>
        </div>
    </div>

    <nav class="bottom-nav">
        <a href="index.php" class="nav-item">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="favorites.php" class="nav-item">
            <i class="fas fa-heart"></i>
            <span>Favorites</span>
        </a>
        <a href="orders.php" class="nav-item">
            <i class="fas fa-receipt"></i>
            <span>Orders</span>
        </a>
        <a href="profile.php" class="nav-item active">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
    </nav>

    <!-- Modals -->
    <div class="modal" id="profile-modal">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-size: 1.2rem;">Edit Profile</h2>
                <button class="icon-btn" onclick="closeModal('profile-modal')" style="color: var(--text-gray); font-size: 1.2rem;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="profile-form">
                <input type="hidden" name="action" value="update_profile">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <button type="submit" class="auth-btn">Save Changes</button>
            </form>
        </div>
    </div>

    <div class="modal" id="address-modal">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-size: 1.2rem;">Delivery Address</h2>
                <button class="icon-btn" onclick="closeModal('address-modal')" style="color: var(--text-gray); font-size: 1.2rem;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="address-form">
                <input type="hidden" name="action" value="update_address">
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control" style="height: 100px; resize: none;"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="auth-btn">Save Address</button>
            </form>
        </div>
    </div>

    <div class="modal" id="payment-modal">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-size: 1.2rem;">Payment Method</h2>
                <button class="icon-btn" onclick="closeModal('payment-modal')" style="color: var(--text-gray); font-size: 1.2rem;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="payment-form">
                <input type="hidden" name="action" value="update_payment">
                <div class="form-group">
                    <label>Preferred Payment</label>
                    <select name="payment_method" class="form-control">
                        <option value="Cash on Delivery" <?php echo ($user['payment_method'] ?? '') === 'Cash on Delivery' ? 'selected' : ''; ?>>Cash on Delivery</option>
                        <option value="Credit Card" <?php echo ($user['payment_method'] ?? '') === 'Credit Card' ? 'selected' : ''; ?>>Credit Card</option>
                        <option value="Debit Card" <?php echo ($user['payment_method'] ?? '') === 'Debit Card' ? 'selected' : ''; ?>>Debit Card</option>
                        <option value="G-Cash" <?php echo ($user['payment_method'] ?? '') === 'G-Cash' ? 'selected' : ''; ?>>G-Cash</option>
                    </select>
                </div>
                <button type="submit" class="auth-btn">Save Payment</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            const modal = document.getElementById(id);
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            modal.classList.remove('show');
            setTimeout(() => modal.style.display = 'none', 300);
        }

        function closeModalAndWait(id) {
            return new Promise((resolve) => {
                const modal = document.getElementById(id);
                if (!modal || modal.style.display === 'none') {
                    resolve();
                    return;
                }
                modal.classList.remove('show');
                setTimeout(() => {
                    modal.style.display = 'none';
                    resolve();
                }, 300);
            });
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                closeModal(event.target.id);
            }
        }

        const forms = ['profile-form', 'address-form', 'payment-form'];
        const formModalMap = {
            'profile-form': 'profile-modal',
            'address-form': 'address-modal',
            'payment-form': 'payment-modal'
        };

        async function showUpdateAlert(isSuccess, message) {
            return new Promise((resolve) => {
                const oldToast = document.querySelector('.profile-toast');
                if (oldToast) oldToast.remove();

                const toast = document.createElement('div');
                toast.className = `profile-toast${isSuccess ? '' : ' error'}`;
                toast.textContent = message;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.remove();
                    resolve();
                }, 2000);
            });
        }

        forms.forEach(formId => {
            const form = document.getElementById(formId);
            if (form) {
                form.onsubmit = async (e) => {
                    e.preventDefault();
                    const formData = new FormData(form);
                    
                    try {
                        const response = await fetch('update_profile.php', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await response.json();
                        
                        if (data.success) {
                            await closeModalAndWait(formModalMap[formId]);
                            await showUpdateAlert(true, data.message);
                            location.reload();
                        } else {
                            await showUpdateAlert(false, data.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        await showUpdateAlert(false, 'An error occurred while updating.');
                    }
                };
            }
        });
    </script>
</body>
</html>
