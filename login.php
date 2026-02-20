<?php
session_start();
require_once 'config/db.php';

if (isset($_SESSION['user_id'])) {
    $sessionRole = strtolower(trim((string)($_SESSION['role'] ?? 'user')));
    if ($sessionRole === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

$error = '';
$register_success = $_SESSION['register_success'] ?? '';
if ($register_success) {
    unset($_SESSION['register_success']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $role = strtolower(trim((string)($user['role'] ?? 'user')));
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $role;
            
            // Redirect admin to dashboard, users to index
            if ($role === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Burger App</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 14px;
            background: url('assets/images/bg.jpg') center center / cover no-repeat fixed;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, rgba(10, 10, 10, 0.68), rgba(24, 24, 24, 0.74));
            z-index: 0;
        }

        .auth-container {
            position: relative;
            z-index: 1;
            width: min(92vw, 430px);
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.28);
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            padding: 30px 22px;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .circle-logo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ffc107, #ff8f00);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 25px rgba(255, 193, 7, 0.35);
            border: 2px solid rgba(255, 255, 255, 0.9);
        }
        
        .circle-logo i {
            font-size: 50px;
            color: white;
        }
        
        .circle-logo span {
            font-size: 40px;
            font-weight: bold;
            color: white;
        }

        .form-group label {
            color: rgba(255, 255, 255, 0.88);
            font-weight: 500;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.24);
            color: #fff;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.65);
        }

        .form-control:focus {
            border-color: rgba(255, 193, 7, 0.95);
            box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.18);
        }

        .auth-btn {
            background: linear-gradient(135deg, #ffd54f, #ffc107);
            color: #111;
            font-weight: 700;
            box-shadow: 0 10px 22px rgba(255, 193, 7, 0.28);
        }

        .auth-link {
            color: rgba(255, 255, 255, 0.82);
        }

        .login-back {
            position: fixed;
            top: 16px;
            left: 16px;
            z-index: 2;
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.28);
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .login-back i {
            font-size: 1.1rem;
        }

        .swal-login-popup {
            width: min(86vw, 300px) !important;
            padding: 0.9rem !important;
            border-radius: 14px !important;
        }

        .swal-login-title {
            font-size: 1.45rem !important;
            margin-bottom: 0.2rem !important;
        }

        .swal-login-text {
            font-size: 0.9rem !important;
        }

        .swal-login-btn {
            font-size: 0.88rem !important;
            padding: 0.55rem 0.95rem !important;
            border-radius: 9px !important;
        }

        @media (max-width: 420px) {
            .swal-login-popup {
                width: 90vw !important;
                padding: 0.75rem !important;
            }

            .swal-login-title {
                font-size: 1.2rem !important;
            }

            .swal-login-text {
                font-size: 0.84rem !important;
            }

            .swal-login-btn {
                font-size: 0.8rem !important;
                padding: 0.5rem 0.85rem !important;
            }
        }
    </style>
</head>
<body>
    <a href="landing.php" class="login-back">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div class="auth-container">
        <h1 class="header-title">Welcome Back!</h1>

        <!-- Circle Logo -->
        <div class="logo-container">
            <div class="circle-logo">
                <!-- Option 1: Burger Icon -->
                <i class="fas fa-hamburger"></i>
                <!-- Option 2: Text Logo (uncomment to use instead) -->
                <!-- <span>&#x1F354;</span> -->
            </div>
        </div>

        <div class="auth-brand-title">nicey burger</div>

        <p style="color: var(--text-gray); margin-top: 10px;">Login to order your favorite burgers</p>

        <?php if ($error): ?>
            <div style="color: #ff4444; margin-top: 20px; font-size: 0.9rem;"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="auth-form">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="auth-btn">Login</button>
        </form>

        <a href="register.php" class="auth-link">Don't have an account? <span>Register Now</span></a>
    </div>
    <script>
        const registerSuccessMessage = <?php echo json_encode($register_success); ?>;
        if (registerSuccessMessage) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: registerSuccessMessage,
                confirmButtonText: 'OK',
                confirmButtonColor: '#ffc107',
                background: '#232326',
                color: '#fff',
                customClass: {
                    popup: 'swal-login-popup',
                    title: 'swal-login-title',
                    htmlContainer: 'swal-login-text',
                    confirmButton: 'swal-login-btn'
                }
            });
        }
    </script>
</body>
</html>
