<?php
session_start();

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: landing.php");
    exit;
}

require_once 'config/db.php';

// Fetch categories
$cat_stmt = $pdo->query("SELECT * FROM categories");
$categories = $cat_stmt->fetchAll();

// Fetch products based on category
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : $categories[0]['id'];
$prod_stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND is_available = 1");
$prod_stmt->execute([$category_id]);
$products = $prod_stmt->fetchAll();

// Fetch user's favorites
$fav_stmt = $pdo->prepare("SELECT product_id FROM favorites WHERE user_id = ?");
$fav_stmt->execute([$_SESSION['user_id']]);
$user_favorites = $fav_stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Burger App - Order Now</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background:
                linear-gradient(135deg, rgba(10, 10, 10, 0.68), rgba(24, 24, 24, 0.74)),
                url('assets/images/bg.jpg') center center / cover no-repeat fixed !important;
        }

        #cart-modal {
            padding: 12px;
            box-sizing: border-box;
        }

        #cart-modal .modal-content {
            width: min(92vw, 360px);
            max-height: calc(100vh - 95px);
            overflow-y: auto;
            padding: 16px 14px;
            border-radius: 18px;
        }

        #cart-modal h2 {
            font-size: 1.08rem !important;
        }

        #cart-modal .icon-btn {
            font-size: 1rem !important;
        }

        #cart-modal #cart-items-list p {
            margin-top: 10px !important;
            font-size: 0.9rem;
        }

        #cart-modal #cart-total-price,
        #cart-modal #cart-items-list .cart-item-price {
            font-size: 0.95rem;
        }

        #cart-modal .checkout-btn {
            min-height: 42px;
            font-size: 0.95rem;
            margin-top: 12px;
        }

        @media (max-width: 420px) {
            #cart-modal {
                padding: 8px;
            }

            #cart-modal .modal-content {
                width: 95vw;
                max-height: calc(100vh - 80px);
                padding: 12px 10px;
                border-radius: 14px;
            }

            #cart-modal h2 {
                font-size: 0.98rem !important;
            }

            #cart-modal .checkout-btn {
                min-height: 40px;
                font-size: 0.9rem;
            }
        }

        #product-view-modal {
            padding: 10px;
            box-sizing: border-box;
        }

        #product-view-modal .modal-content {
            width: min(92vw, 350px);
            max-height: calc(100vh - 70px);
            overflow-y: auto;
            padding: 12px;
            border-radius: 16px;
        }

        #product-view-image {
            width: 100%;
            height: 145px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 10px;
        }

        #product-view-name {
            margin: 0 0 6px;
            font-size: 1.06rem;
        }

        #product-view-desc {
            color: var(--text-gray);
            font-size: 0.86rem;
            line-height: 1.35;
            margin-bottom: 10px;
        }

        #product-view-price {
            color: var(--accent);
            font-size: 1.02rem;
            font-weight: 700;
            margin-bottom: 12px;
        }

        #product-modal-add-btn {
            width: 100%;
            min-height: 40px;
            border: none;
            border-radius: 12px;
            background: var(--accent);
            color: #000;
            font-size: 0.92rem;
            font-weight: 700;
            cursor: pointer;
        }

        .product-card.openable {
            cursor: pointer;
        }

        @media (max-width: 420px) {
            #product-view-modal .modal-content {
                width: 95vw;
                padding: 10px;
                border-radius: 14px;
            }

            #product-view-image {
                height: 130px;
            }

            #product-view-name {
                font-size: 0.98rem;
            }

            #product-view-desc {
                font-size: 0.82rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <button class="icon-btn"><i class="fas fa-bars"></i></button>
        <div class="header-title">Burger App</div>
        <button class="icon-btn" id="cart-icon">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-badge" style="display: none;">0</span>
        </button>
    </header>

    <div class="search-container">
        <input type="text" class="search-bar" placeholder="Search your favorite burger...">
    </div>

    <div class="categories-container">
        <?php foreach ($categories as $cat): ?>
            <a href="index.php?category=<?php echo $cat['id']; ?>" 
               class="category-tab <?php echo $category_id == $cat['id'] ? 'active' : ''; ?>" 
               style="text-decoration: none;">
                <?php echo $cat['name']; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="products-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card openable"
                 data-id="<?php echo $product['id']; ?>"
                 data-name="<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>"
                 data-description="<?php echo htmlspecialchars($product['description'], ENT_QUOTES); ?>"
                 data-price="<?php echo $product['price']; ?>"
                 data-image="<?php echo htmlspecialchars($product['image'], ENT_QUOTES); ?>">
                <button class="favorite-btn <?php echo in_array($product['id'], $user_favorites ?? []) ? 'active' : ''; ?>" 
                        onclick="toggleFavorite(<?php echo $product['id']; ?>, this)">
                    <i class="<?php echo in_array($product['id'], $user_favorites ?? []) ? 'fas' : 'far'; ?> fa-heart"></i>
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
    </div>

    <nav class="bottom-nav">
        <a href="index.php" class="nav-item active">
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
        <a href="profile.php" class="nav-item">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
    </nav>

    <!-- Cart Modal -->
    <div class="modal" id="cart-modal">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-size: 1.2rem;">My Cart</h2>
                <button class="icon-btn" id="close-cart" style="color: var(--text-gray); font-size: 1.2rem;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="cart-items-list">
                <!-- Dynamic Cart Items -->
            </div>
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

    <!-- Product View Modal -->
    <div class="modal" id="product-view-modal">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <h2 style="font-size: 1rem; margin: 0;">Product Details</h2>
                <button class="icon-btn" id="close-product-view" style="color: var(--text-gray); font-size: 1rem;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <img id="product-view-image" src="" alt="Product image">
            <h3 id="product-view-name"></h3>
            <p id="product-view-desc"></p>
            <div id="product-view-price"></div>
            <button id="product-modal-add-btn" type="button">Add to Cart</button>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
    <script>
        // Additional Logic for Cart Modal in index.php
        const cartModal = document.getElementById('cart-modal');
        const cartIcon = document.getElementById('cart-icon');
        const closeCart = document.getElementById('close-cart');
        const cartItemsList = document.getElementById('cart-items-list');
        const cartTotalPrice = document.getElementById('cart-total-price');
        const cartDataInput = document.getElementById('cart-data-input');
        const productViewModal = document.getElementById('product-view-modal');
        const closeProductView = document.getElementById('close-product-view');
        const productViewImage = document.getElementById('product-view-image');
        const productViewName = document.getElementById('product-view-name');
        const productViewDesc = document.getElementById('product-view-desc');
        const productViewPrice = document.getElementById('product-view-price');
        const productModalAddBtn = document.getElementById('product-modal-add-btn');
        let selectedProduct = null;

        const renderCart = () => {
            const cart = JSON.parse(localStorage.getItem('burger_cart')) || [];
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
            let cart = JSON.parse(localStorage.getItem('burger_cart')) || [];
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

        document.querySelectorAll('.product-card.openable').forEach(card => {
            card.addEventListener('click', (event) => {
                if (event.target.closest('.favorite-btn') || event.target.closest('.add-to-cart')) {
                    return;
                }

                selectedProduct = {
                    id: Number(card.dataset.id),
                    name: card.dataset.name,
                    description: card.dataset.description,
                    price: Number(card.dataset.price),
                    image: card.dataset.image
                };

                productViewImage.src = `assets/images/${selectedProduct.image}`;
                productViewImage.alt = selectedProduct.name;
                productViewName.textContent = selectedProduct.name;
                productViewDesc.textContent = selectedProduct.description;
                productViewPrice.textContent = `₱${selectedProduct.price.toFixed(2)}`;

                productViewModal.style.display = 'flex';
                setTimeout(() => productViewModal.classList.add('show'), 10);
            });
        });

        closeProductView.addEventListener('click', () => {
            productViewModal.classList.remove('show');
            setTimeout(() => productViewModal.style.display = 'none', 300);
        });

        productModalAddBtn.addEventListener('click', () => {
            if (!selectedProduct) return;
            addToCart(
                selectedProduct.id,
                selectedProduct.name,
                selectedProduct.price,
                selectedProduct.image
            );
            productViewModal.classList.remove('show');
            setTimeout(() => productViewModal.style.display = 'none', 300);
        });

        window.onclick = (event) => {
            if (event.target == cartModal) {
                cartModal.classList.remove('show');
                setTimeout(() => cartModal.style.display = 'none', 300);
            }
            if (event.target == productViewModal) {
                productViewModal.classList.remove('show');
                setTimeout(() => productViewModal.style.display = 'none', 300);
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
                    const icon = btn.querySelector('i');
                    if (data.action === 'added') {
                        btn.classList.add('active');
                        icon.classList.replace('far', 'fas');
                    } else {
                        btn.classList.remove('active');
                        icon.classList.replace('fas', 'far');
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
