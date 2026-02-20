<?php
session_start();
require_once 'config/db.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user's favorite products
$stmt = $pdo->prepare("
    SELECT p.* 
    FROM products p 
    JOIN favorites f ON p.id = f.product_id 
    WHERE f.user_id = ? AND p.is_available = 1
");
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll();

// Fetch all product IDs that are favorited by the user (for the toggle logic)
$fav_ids_stmt = $pdo->prepare("SELECT product_id FROM favorites WHERE user_id = ?");
$fav_ids_stmt->execute([$user_id]);
$user_favorite_ids = $fav_ids_stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Favorites - Burger App</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background:
                linear-gradient(135deg, rgba(10, 10, 10, 0.68), rgba(24, 24, 24, 0.74)),
                url('assets/images/bg.jpg') center center / cover no-repeat fixed !important;
        }
    </style>
</head>
<body>
    <header>
        <a href="index.php" class="icon-btn" style="text-decoration: none; color: white;">
            <i class="fas fa-chevron-left"></i>
        </a>
        <div class="header-title">My Favorites</div>
        <button class="icon-btn" id="cart-icon">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-badge" style="display: none;">0</span>
        </button>
    </header>

    <div class="products-grid" style="margin-top: 20px;">
        <?php if (empty($favorites)): ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 50px 20px; color: var(--text-gray);">
                <i class="far fa-heart" style="font-size: 3rem; margin-bottom: 20px; display: block;"></i>
                <p>You haven't added any favorites yet.</p>
                <a href="index.php" class="checkout-btn" style="display: inline-block; text-decoration: none; width: auto; padding: 10px 30px; margin-top: 20px;">Go Shopping</a>
            </div>
        <?php else: ?>
            <?php foreach ($favorites as $product): ?>
                <div class="product-card" id="product-<?php echo $product['id']; ?>">
                    <button class="favorite-btn active" 
                            onclick="toggleFavorite(<?php echo $product['id']; ?>, this)">
                        <i class="fas fa-heart"></i>
                    </button>
                    <img src="assets/images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="product-image">
                    <div class="product-name"><?php echo $product['name']; ?></div>
                    <div class="product-desc"><?php echo $product['description']; ?></div>
                    <div class="product-footer">
                        <div class="product-price">₱<?php echo number_format($product['price'], 2); ?></div>
                        <button class="add-to-cart" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>, '<?php echo $product['image']; ?>')">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <nav class="bottom-nav">
        <a href="index.php" class="nav-item">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="favorites.php" class="nav-item active">
            <i class="fas fa-heart"></i>
            <span>Favorites</span>
        </a>
        <a href="orders.php" class="nav-item">
            <i class="fas fa-receipt"></i>
            <span>Orders</span>
        </a>
        <a href="profile.php" class="nav-item">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
    </nav>

    <!-- Cart Modal (Same as index.php) -->
    <div class="modal" id="cart-modal">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-size: 1.2rem;">My Cart</h2>
                <button class="icon-btn" id="close-cart" style="color: var(--text-gray); font-size: 1.2rem;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="cart-items-list"></div>
            <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #444;">
                <div style="display: flex; justify-content: space-between; font-weight: 600; font-size: 1.1rem;">
                    <span>Total</span>
                    <span id="cart-total-price">₱0.00</span>
                </div>
                <form action="checkout.php" method="POST" id="checkout-form">
                    <input type="hidden" name="cart_data" id="cart-data-input">
                    <button type="submit" class="checkout-btn">Checkout</button>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
    <script>
        const cartModal = document.getElementById('cart-modal');
        const cartIcon = document.getElementById('cart-icon');
        const closeCart = document.getElementById('close-cart');
        const cartItemsList = document.getElementById('cart-items-list');
        const cartTotalPrice = document.getElementById('cart-total-price');
        const cartDataInput = document.getElementById('cart-data-input');

        const getCart = () => {
            try {
                const parsed = JSON.parse(localStorage.getItem('burger_cart'));
                return Array.isArray(parsed) ? parsed : [];
            } catch (e) {
                localStorage.removeItem('burger_cart');
                return [];
            }
        };

        const renderCart = () => {
            const cart = getCart();
            cartItemsList.innerHTML = '';
            let total = 0;

            if (cart.length === 0) {
                cartItemsList.innerHTML = '<p style="text-align: center; color: var(--text-gray); margin-top: 20px;">Your cart is empty.</p>';
            } else {
                cart.forEach(item => {
                    total += item.price * item.quantity;
                    const itemEl = document.createElement('div');
                    itemEl.className = 'cart-item';
                    itemEl.innerHTML = `
                        <img src="assets/images/${item.image}" alt="${item.name}" class="cart-item-img">
                        <div class="cart-item-info">
                            <div class="cart-item-name">${item.name}</div>
                            <div class="cart-item-price">₱${item.price.toFixed(2)}</div>
                        </div>
                        <div class="quantity-controls">
                            <button class="qty-btn" onclick="updateQty(${item.id}, -1)">-</button>
                            <span>${item.quantity}</span>
                            <button class="qty-btn" onclick="updateQty(${item.id}, 1)">+</button>
                        </div>
                    `;
                    cartItemsList.appendChild(itemEl);
                });
            }
            cartTotalPrice.textContent = `₱${total.toFixed(2)}`;
            cartDataInput.value = JSON.stringify(cart);
        };

        window.updateQty = (id, delta) => {
            let cart = getCart();
            const item = cart.find(i => i.id === id);
            if (item) {
                item.quantity += delta;
                if (item.quantity <= 0) {
                    cart = cart.filter(i => i.id !== id);
                }
                localStorage.setItem('burger_cart', JSON.stringify(cart));
                renderCart();
                if (window.updateCartBadge) window.updateCartBadge();
            }
        };

        cartIcon.addEventListener('click', () => {
            renderCart();
            cartModal.style.display = 'flex';
            setTimeout(() => cartModal.classList.add('show'), 10);
        });

        closeCart.addEventListener('click', () => {
            cartModal.classList.remove('show');
            setTimeout(() => cartModal.style.display = 'none', 300);
        });

        window.onclick = (event) => {
            if (event.target == cartModal) {
                cartModal.classList.remove('show');
                setTimeout(() => cartModal.style.display = 'none', 300);
            }
        };

        async function toggleFavorite(productId, btn) {
            const formData = new FormData();
            formData.append('product_id', productId);

            try {
                const response = await fetch('toggle_favorite.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    if (data.action === 'removed') {
                        // In favorites page, we remove the card
                        const card = document.getElementById(`product-${productId}`);
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.8)';
                        setTimeout(() => {
                            card.remove();
                            if (document.querySelectorAll('.product-card').length === 0) {
                                location.reload(); // Show empty state
                            }
                        }, 300);
                    }
                } else {
                    alert(data.message || 'Error updating favorites');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred');
            }
        }
    </script>
</body>
</html>
