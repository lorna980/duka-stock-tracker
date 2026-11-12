CREATE DATABASE IF NOT EXISTS dukastock_db;
USE dukastock_db;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    buying_price DECIMAL(10, 2) NOT NULL,
    selling_price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    reorder_level INT NOT NULL DEFAULT 5,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    quantity_sold INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('Cash', 'M-Pesa') NOT NULL,
    sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

INSERT INTO products (product_name, category, buying_price, selling_price, stock_quantity, reorder_level) VALUES
('Jogoo Maize Meal 2kg', 'Flour & Grains', 145.00, 170.00, 25, 10),
('Kabras Sugar 1kg', 'Groceries', 135.00, 160.00, 4, 8),
('Fresh Fri Cooking Oil 1L', 'Cooking Oils', 260.00, 310.00, 12, 5),
('Brookside Long Life Milk 500ml', 'Dairy', 55.00, 65.00, 3, 10);