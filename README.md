# Threadify - Women's Clothing E-Commerce

A modern, responsive e-commerce website for women's fashion built with HTML, CSS, JavaScript (frontend) and PHP with MySQL (backend).

## Features

### Frontend
- **Responsive Design** - Mobile-first approach with smooth navigation
- **Product Catalog** - Browse by category, price filtering, sorting
- **Product Details** - Image gallery, quantity selector, related products
- **Shopping Cart** - Session-based cart with add/remove/update functionality
- **Checkout** - Secure checkout with shipping information
- **User Account** - Order history, profile management
- **Newsletter** - Email subscription functionality

### Backend
- **Authentication** - Secure login/register with password hashing
- **Admin Dashboard** - Manage products, view orders, update order status
- **Database** - MySQL with proper relationships and indexing
- **Session Management** - Cart persistence and user sessions
- **Security** - SQL injection prevention, XSS protection, CSRF awareness

## Technology Stack
- **Frontend:** HTML5, CSS3 (Custom Properties, Grid, Flexbox), JavaScript (ES6+)
- **Backend:** PHP 8.0+
- **Database:** MySQL 5.7+
- **Icons:** Font Awesome 6

## Installation

### 1. Clone or Download
```bash
cd "thredify wp"
```

### 2. Database Setup
```bash
mysql -u root -p < threadify.sql
```

### 3. Configuration
Edit `includes/db.php` with your database credentials:
```php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'threadify';
```

### 4. Start Server
```bash
php -S localhost:8000
```

Visit `http://localhost:8000` in your browser.

## Default Login
- **User:** test@example.com / password
- **Admin:** admin@threadify.com / admin123

## File Structure
```
thredify wp/
├── index.php              # Homepage with hero, featured products
├── shop.php               # Product listing with filters
├── product.php            # Single product detail
├── cart.php               # Shopping cart
├── checkout.php           # Checkout & order placement
├── login.php / register.php  # Authentication
├── account.php            # User dashboard
├── about.php / contact.php   # Static pages
├── actions/               # AJAX handlers (cart, auth)
├── admin/                 # Admin panel (dashboard, products, orders)
├── includes/              # Database, functions, header, footer
├── assets/                # CSS, JS, images
└── threadify.sql          # Database schema + sample data
```

## License
MIT License

