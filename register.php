<?php
session_start();
require_once 'config/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$username_input = '';
$email_input = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username_input = trim($_POST['username'] ?? '');
    $email_input = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $username = preg_replace('/\s+/', ' ', $username_input);
    $email = strtolower(filter_var($email_input, FILTER_SANITIZE_EMAIL));
    $username_length = mb_strlen($username);
    $password_length = strlen($password);

    if ($username === '' || $email === '' || $password === '' || $confirm_password === '') {
        $error = "Please fill in all fields.";
    } elseif ($username_length < 3 || $username_length > 30) {
        $error = "Full name must be between 3 and 30 characters.";
    } elseif (!preg_match('/^[A-Za-z ]{3,30}$/', $username)) {
        $error = "Full name must be 3-30 characters and contain letters only.";
    } elseif (preg_match('/[\x00-\x1F\x7F]/', $username)) {
        $error = "Full name contains invalid characters.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif ($password_length < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif ($password_length > 72) {
        $error = "Password must be 72 characters or less.";
    } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
        $error = "Password must contain at least one letter and one number.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        if ($stmt->fetch()) {
            $error = "Full name or Email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashed_password])) {
                $_SESSION['register_success'] = "Registered successfully.";
                header("Location: login.php");
                exit;
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Burger App</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        .register-back {
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

        .register-back i {
            font-size: 1.1rem;
        }

        .password-wrap {
            position: relative;
        }

        .password-wrap .form-control {
            padding-right: 44px;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.85);
            cursor: pointer;
            font-size: 1rem;
            line-height: 1;
        }
    </style>
</head>
<body>
    <a href="landing.php" class="register-back">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div class="auth-container">
        <h1 class="header-title">Create Account</h1>
        <p style="color: var(--text-gray); margin-top: 10px;">Join us and start ordering</p>

        <?php if ($error): ?>
            <div style="color: #ff4444; margin-top: 20px; font-size: 0.9rem;"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="auth-form">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="username" class="form-control" placeholder="Enter your full name" value="<?php echo htmlspecialchars($username_input); ?>" minlength="3" maxlength="30" pattern="[A-Za-z ]{3,30}" autocomplete="name" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email" value="<?php echo htmlspecialchars($email_input); ?>" maxlength="254" autocomplete="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="password-wrap">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Create a password" minlength="8" maxlength="72" autocomplete="new-password" required>
                    <button type="button" class="password-toggle" data-target="password" aria-label="Show password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <div class="password-wrap">
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm your password" minlength="8" maxlength="72" autocomplete="new-password" required>
                    <button type="button" class="password-toggle" data-target="confirm_password" aria-label="Show confirm password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="auth-btn">Register</button>
        </form>

        <a href="login.php" class="auth-link">Already have an account? <span>Login Now</span></a>
    </div>
    <script>
        document.querySelectorAll('.password-toggle').forEach((btn) => {
            btn.addEventListener('click', () => {
                const targetId = btn.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = btn.querySelector('i');
                if (!input || !icon) return;

                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                icon.classList.toggle('fa-eye', !show);
                icon.classList.toggle('fa-eye-slash', show);
                btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            });
        });
    </script>
</body>
</html>
