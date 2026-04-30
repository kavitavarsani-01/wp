<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// Get cart count
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Threadify</title>
    <link rel="stylesheet" href="assets/css/style.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="container header-container">
            <a href="index.php" class="logo">Threadify</a>
            <button class="mobile-menu-btn" aria-label="Toggle menu">
                <i class="fas fa-bars"></i>
            </button>
            <nav class="nav">
                <ul class="nav-list">
                    <li><a href="index.php" class="nav-link">Home</a></li>
                    <li><a href="shop.php" class="nav-link">Shop</a></li>
                    <li><a href="about.php" class="nav-link">About</a></li>
                    <li><a href="contact.php" class="nav-link">Contact</a></li>
                </ul>
            </nav>
            <div class="header-actions">
                <a href="cart.php" class="header-icon" aria-label="Cart">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="cart-badge"><?php echo $cart_count; ?></span>
                </a>
                <a href="<?php echo isLoggedIn() ? 'wishlist-page.php' : 'login.php'; ?>" class="nav-icon">
                    <i class="fas fa-heart"></i>
                    <span class="count"><?php echo getWishlistCount(); ?></span>
                </a>
                <?php if(isLoggedIn()): ?>
                    <a href="account.php" class="header-icon" aria-label="Account">
                        <i class="fas fa-user"></i>
                    </a>
                    <a href="actions/logout.php" class="header-icon" aria-label="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="header-icon" aria-label="Login">
                        <i class="fas fa-user"></i>
                    </a>
                <?php endif; ?>
            </div>
    </header>
