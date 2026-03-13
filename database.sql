-- فایلی database.sql لە phpMyAdmin ئەپڵۆد بکە

CREATE DATABASE IF NOT EXISTS market_system;
USE market_system;

-- خشتەی بەکارهێنەران
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(200) NOT NULL,
    role ENUM('super_admin', 'admin', 'store', 'cashier') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- زیادکردنی بەکارهێنەرەکان
INSERT INTO users (username, password, full_name, role) VALUES 
('super', MD5('admin123'), 'Super Admin', 'super_admin'),
('admin', MD5('admin123'), 'Admin User', 'admin'),
('store', MD5('store123'), 'Store Keeper', 'store'),
('cashier', MD5('cash123'), 'Cashier User', 'cashier');

-- خشتەی کاتێگۆریەکان
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    shelf_name VARCHAR(100)
);

-- خشتەی کاڵاکان
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    barcode VARCHAR(100) UNIQUE,
    name VARCHAR(255) NOT NULL,
    category_id INT,
    purchase_price DECIMAL(10,2) NOT NULL,
    selling_price DECIMAL(10,2) NOT NULL,
    quantity INT DEFAULT 0,
    min_quantity INT DEFAULT 5,
    expiry_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- خشتەی فرۆشتنەکان
CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(50) UNIQUE,
    user_id INT,
    customer_name VARCHAR(255),
    total_amount DECIMAL(10,2),
    paid_amount DECIMAL(10,2),
    remaining_amount DECIMAL(10,2),
    sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- خشتەی وردەکاری فرۆشتن
CREATE TABLE sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT,
    product_id INT,
    quantity INT,
    price DECIMAL(10,2),
    total DECIMAL(10,2),
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- خشتەی پارە وەرگرتن و خەرجکردن
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('income', 'expense', 'damage') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description TEXT,
    user_id INT,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- خشتەی قەرزەکان
CREATE TABLE debts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20),
    total_amount DECIMAL(10,2) NOT NULL,
    paid_amount DECIMAL(10,2) DEFAULT 0,
    remaining_amount DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    due_date DATE,
    status ENUM('active', 'paid', 'overdue') DEFAULT 'active'
);

-- خشتەی پارەدانی قەرز
CREATE TABLE debt_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    debt_id INT,
    amount DECIMAL(10,2) NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INT,
    FOREIGN KEY (debt_id) REFERENCES debts(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
