CREATE DATABASE IF NOT EXISTS burger_app;
USE burger_app;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    address TEXT,
    payment_method VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    is_available BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY (user_id, product_id)
);

-- Insert sample categories
INSERT IGNORE INTO categories (name) VALUES ('Burgers'), ('Drinks'), ('Fries'), ('Combo');

-- Insert sample products
INSERT IGNORE INTO products (category_id, name, description, price, image) VALUES 
(1, 'Beef Burger', 'Juicy beef patty with cheese and lettuce', 8.99, 'beefburger.jpg'),
(1, 'Chicken Burger', 'Crispy chicken breast with spicy mayo', 7.99, 'burger2.png'),
(1, 'Bacon Cheeseburger', 'Classic beef patty with crispy bacon and cheddar', 10.99, 'burger3.png'),
(1, 'Veggie Burger', 'Plant-based patty with avocado and sprouts', 9.49, 'burger4.png'),
(1, 'Mushroom Swiss', 'Beef patty topped with sautéed mushrooms and swiss cheese', 9.99, 'burger5.png'),
(2, 'Coca Cola', 'Chilled 330ml can', 1.99, 'cola.png'),
(2, 'Orange Juice', 'Freshly squeezed oranges', 2.99, 'juice.png'),
(2, 'Strawberry Milkshake', 'Creamy shake made with real strawberries', 4.49, 'shake1.png'),
(2, 'Iced Coffee', 'Cold brew with a splash of milk', 3.99, 'coffee.png'),
(2, 'Lemonade', 'Classic homemade style lemonade', 2.49, 'lemonade.png'),
(3, 'French Fries', 'Crispy golden potato fries', 3.49, 'fries.png'),
(3, 'Cheese Fries', 'Fries smothered in melted cheddar cheese', 4.99, 'fries2.png'),
(3, 'Sweet Potato Fries', 'Fried sweet potato strips with cinnamon sugar', 4.49, 'fries3.png'),
(3, 'Onion Rings', 'Crispy battered onion rings', 3.99, 'rings.png'),
(4, 'Family Combo', '2 Burgers, 2 Fries, 2 Drinks', 19.99, 'combo.png'),
(4, 'Solo Combo', '1 Burger, 1 Fries, 1 Drink', 12.99, 'combo2.png'),
(4, 'Couple Combo', '2 Burgers, 1 Large Fries, 2 Drinks', 22.99, 'combo3.png');
