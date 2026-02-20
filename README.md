# Burger Ordering System - Setup Guide

## 1. Prerequisites
- XAMPP or any local server with PHP and MySQL.

## 2. Database Setup
1. Open **phpMyAdmin**.
2. Create a new database named `burger_app`.
3. Import the `database.sql` file located in the root directory.

## 3. Configuration
- Check `config/db.php` and update the database credentials if necessary (default is `root` with no password).

## 4. Admin Access
- To access the admin panel, you need to register a user first.
- Then, manually change the `role` to `admin` in the `users` table via phpMyAdmin.
- Admin dashboard is located at `/admin/dashboard.php`.

## 5. Assets
- Make sure to place product images in the `assets/images/` folder.
- Suggested images for sample data: `burger1.png`, `burger2.png`, `cola.png`, `juice.png`, `fries.png`, `combo.png`.

## 6. Project Structure
- `index.php`: Main store front.
- `login.php`/`register.php`: Authentication.
- `orders.php`: User order history.
- `profile.php`: User profile and logout.
- `admin/`: Product management.
- `assets/`: Styling, images, and frontend logic.
