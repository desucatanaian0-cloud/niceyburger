document.addEventListener('DOMContentLoaded', () => {
    let cart = JSON.parse(localStorage.getItem('burger_cart')) || [];
    const cartBadge = document.querySelector('.cart-badge');
    const productsGrid = document.querySelector('.products-grid');
    const categoryTabs = document.querySelectorAll('.category-tab');

    // Update Cart Badge
    const updateCartBadge = () => {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        if (cartBadge) {
            cartBadge.textContent = totalItems;
            cartBadge.style.display = totalItems > 0 ? 'block' : 'none';
        }
    };

    // Save Cart to Local Storage
    const saveCart = () => {
        localStorage.setItem('burger_cart', JSON.stringify(cart));
        updateCartBadge();
    };

    // Toast Notification
    const showToast = (message) => {
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = message;
        document.body.appendChild(toast);
        
        // Add toast styling dynamically if not in CSS
        Object.assign(toast.style, {
            position: 'fixed',
            bottom: '100px',
            left: '50%',
            transform: 'translateX(-50%)',
            backgroundColor: '#FFC107',
            color: '#000',
            padding: '10px 20px',
            borderRadius: '20px',
            fontSize: '0.8rem',
            fontWeight: '600',
            zIndex: '2000',
            boxShadow: '0 4px 15px rgba(0,0,0,0.3)',
            animation: 'fadeInOut 2s forwards'
        });

        setTimeout(() => toast.remove(), 2000);
    };

    // Add to Cart Function
    window.addToCart = (id, name, price, image) => {
        const existingItem = cart.find(item => item.id === id);
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({ id, name, price, image, quantity: 1 });
        }
        saveCart();
        showToast(`${name} added to cart!`);
    };

    // Category Filtering (Simplified for Frontend)
    categoryTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            categoryTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            const category = tab.textContent.trim();
            
            // In a real app, this might fetch via AJAX. 
            // For now, it's a visual placeholder if handled by index.php
        });
    });

    // Initialize
    updateCartBadge();
});

// Animation for toast
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInOut {
        0% { opacity: 0; transform: translate(-50%, 20px); }
        20% { opacity: 1; transform: translate(-50%, 0); }
        80% { opacity: 1; transform: translate(-50%, 0); }
        100% { opacity: 0; transform: translate(-50%, -20px); }
    }
`;
document.head.appendChild(style);
