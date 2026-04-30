-- Threadify E-Commerce Database Schema
-- Women"s Clothing Brand

CREATE DATABASE IF NOT EXISTS threadify;
USE threadify;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(50),
    zip VARCHAR(20),
    role ENUM("user", "admin") DEFAULT "user",
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    category_id INT,
    description TEXT,
    short_desc TEXT,
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2),
    image VARCHAR(255),
    gallery TEXT,
    stock INT DEFAULT 0,
    featured TINYINT(1) DEFAULT 0,
    status ENUM("active", "inactive") DEFAULT "active",
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Cart table
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    session_id VARCHAR(255),
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    total DECIMAL(10,2) NOT NULL,
    status ENUM("pending", "processing", "shipped", "delivered", "cancelled") DEFAULT "pending",
    payment_status ENUM("pending", "paid", "failed") DEFAULT "pending",
    shipping_name VARCHAR(100),
    shipping_email VARCHAR(100),
    shipping_phone VARCHAR(20),
    shipping_address TEXT,
    shipping_city VARCHAR(50),
    shipping_zip VARCHAR(20),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(200),
    product_image VARCHAR(255),
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Insert admin user (password: admin123)
INSERT INTO users (name, email, password, phone, role) VALUES 
("Admin User", "admin@threadify.com", "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi", "1234567890", "admin");

-- Insert categories
INSERT INTO categories (name, slug, description, image) VALUES
("Dresses", "dresses", "Elegant dresses for every occasion", "dresses.jpg"),
("Tops & Blouses", "tops-blouses", "Stylish tops and blouses", "tops.jpg"),
("Bottoms", "bottoms", "Pants, skirts and shorts", "bottoms.jpg"),
("Outerwear", "outerwear", "Jackets, coats and cardigans", "outerwear.jpg"),
("Activewear", "activewear", "Sportswear and athleisure", "activewear.jpg"),
("Accessories", "accessories", "Bags, scarves and more", "accessories.jpg");

-- Insert products
INSERT INTO products (name, slug, category_id, description, short_desc, price, sale_price, image, stock, featured, status) VALUES
("Floral Summer Dress", "floral-summer-dress", 1, "A beautiful floral summer dress perfect for warm days. Made from lightweight breathable fabric with a flattering A-line silhouette.", "Lightweight floral dress perfect for summer", 59.99, 49.99, "https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=600&h=800&fit=crop", 25, 1, "active"),
("Classic White Blouse", "classic-white-blouse", 2, "A timeless white blouse for professional and casual settings. Features a relaxed fit with elegant button details.", "Timeless white blouse for any occasion", 45.99, NULL, "https://images.unsplash.com/photo-1564257631407-4deb1f99d992?w=600&h=800&fit=crop", 30, 1, "active"),
("High-Waist Wide Leg Pants", "high-waist-wide-leg-pants", 3, "Trendy high-waist wide leg pants that elongate your silhouette. Comfortable and chic for any occasion.", "Trendy wide leg pants with high waist", 55.99, 42.99, "https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=600&h=800&fit=crop", 20, 1, "active"),
("Denim Jacket", "denim-jacket", 4, "A must-have oversized denim jacket. Perfect layering piece for transitional weather with classic button-front design.", "Versatile oversized denim jacket", 79.99, NULL, "https://images.unsplash.com/photo-1523205771623-e0faa4d2813d?w=600&h=800&fit=crop", 15, 1, "active"),
("Yoga Leggings Set", "yoga-leggings-set", 5, "High-performance yoga leggings with matching sports bra. Moisture-wicking fabric for maximum comfort during workouts.", "Matching yoga set with moisture-wicking fabric", 68.99, 58.99, "https://images.unsplash.com/photo-1518310383802-640c2de311b2?w=600&h=800&fit=crop", 40, 1, "active"),
("Silk Scarf", "silk-scarf", 6, "Luxurious silk scarf with hand-painted floral design. Adds a touch of elegance to any outfit.", "Luxurious hand-painted silk scarf", 35.99, 29.99, "https://images.unsplash.com/photo-1601924921557-45e6dea0a157?w=600&h=800&fit=crop", 50, 0, "active"),
("Knit Cardigan", "knit-cardigan", 4, "Cozy knit cardigan with button closure. Perfect for layering during cooler months.", "Cozy button-up knit cardigan", 64.99, NULL, "https://images.unsplash.com/photo-1434389677669-e08b4cac3105?w=600&h=800&fit=crop", 22, 0, "active"),
("Pleated Midi Skirt", "pleated-midi-skirt", 3, "Elegant pleated midi skirt with flowing movement. Features an elastic waist for all-day comfort.", "Elegant pleated midi skirt with elastic waist", 48.99, 39.99, "https://images.unsplash.com/photo-1583496661160-fb5886a0uj45?w=600&h=800&fit=crop", 18, 0, "active"),
("Off Shoulder Top", "off-shoulder-top", 2, "Stylish off-shoulder top with ruffled details. Perfect for summer nights and special occasions.", "Stylish off-shoulder ruffled top", 39.99, NULL, "https://images.unsplash.com/photo-1556910540-3b5c087c0f56?w=600&h=800&fit=crop", 28, 0, "active"),
("Maxi Evening Gown", "maxi-evening-gown", 1, "Stunning maxi evening gown with elegant draping. Perfect for formal events and special occasions.", "Stunning maxi gown for formal events", 129.99, 99.99, "https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=600&h=800&fit=crop", 12, 1, "active"),
("Running Sneakers", "running-sneakers", 5, "Lightweight running sneakers with cushioned sole. Designed for comfort and performance.", "Lightweight cushioned running sneakers", 89.99, NULL, "https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&h=800&fit=crop", 35, 0, "active"),
("Leather Crossbody Bag", "leather-crossbody-bag", 6, "Genuine leather crossbody bag with adjustable strap. Compact yet spacious enough for essentials.", "Genuine leather compact crossbody", 75.99, 65.99, "https://images.unsplash.com/photo-1548036328-c9fa2d1cff9c?w=600&h=800&fit=crop", 20, 0, "active");
