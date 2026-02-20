<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$order_id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - Burger App</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .success-screen {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 14px;
            background: rgba(0, 0, 0, 0.45);
        }

        .success-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            width: min(92vw, 340px);
            padding: 18px 14px;
            border-radius: 20px;
            background: #2b2b2e;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 14px 32px rgba(0, 0, 0, 0.45);
        }

        .success-icon-wrap {
            background-color: var(--accent);
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
        }

        .success-icon {
            font-size: 2rem;
            color: #000;
        }

        .success-title {
            font-size: 1.28rem;
            margin-bottom: 6px;
        }

        .success-text {
            color: var(--text-gray);
            margin-top: 8px;
            padding: 0 6px;
            font-size: 0.88rem;
            line-height: 1.4;
        }

        .success-actions {
            margin-top: 18px;
            width: 100%;
            max-width: 100%;
        }

        .success-actions .auth-btn {
            text-decoration: none;
            display: block;
            width: 100%;
            min-height: 44px;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
        }

        @media (max-width: 480px) {
            .success-screen {
                padding: 10px;
            }

            .success-container {
                width: 94vw;
                padding: 14px 11px;
                border-radius: 16px;
            }

            .success-icon-wrap {
                width: 56px;
                height: 56px;
                margin-bottom: 12px;
            }

            .success-icon {
                font-size: 1.55rem;
            }

            .success-title {
                font-size: 1.08rem;
            }

            .success-text {
                margin-top: 6px;
                padding: 0 2px;
                font-size: 0.82rem;
            }

            .success-actions {
                margin-top: 14px;
            }

            .success-actions .auth-btn {
                min-height: 40px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 380px) {
            .success-icon-wrap {
                width: 48px;
                height: 48px;
                margin-bottom: 10px;
            }

            .success-icon {
                font-size: 1.35rem;
            }

            .success-title {
                font-size: 1rem;
            }

            .success-text {
                font-size: 0.78rem;
            }

            .success-actions {
                margin-top: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="success-screen">
        <div class="auth-container success-container">
            <div class="success-icon-wrap">
                <i class="fas fa-check success-icon"></i>
            </div>
            <h1 class="header-title success-title">Order Successful!</h1>
            <p class="success-text">Your delicious meal is being prepared. Order ID: #<?php echo $order_id; ?></p>
            
            <div class="success-actions">
                <a href="index.php" class="auth-btn">Back to Home</a>
            </div>
        </div>
    </div>

    <script>
        // Clear cart from local storage on success
        localStorage.removeItem('burger_cart');
    </script>
</body>
</html>
