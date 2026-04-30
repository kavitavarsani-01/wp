<?php
require_once __DIR__ . '/db.php';

// Get current user
function getCurrentUser() {
    if (isset($_SESSION['user_id'])) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    return null;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    $user = getCurrentUser();
    return $user && $user['role'] === 'admin';
}

// Redirect helper
function redirect($url) {
    header("Location: " . $url);
    exit;
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Format price
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

// Generate unique order number
function generateOrderNumber() {
    return 'THF-' . strtoupper(uniqid());
}

// Get cart count
function getCartCount() {
    if (isLoggedIn()) {
        global $conn;
        $stmt = $conn->prepare("SELECT SUM(quantity) as count FROM cart WHERE user_id = ?");
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] ?? 0;
    }
    return 0;
}

// Get cart items with product details
function getCartItems() {
    if (isLoggedIn()) {
        global $conn;
        $stmt = $conn->prepare("
            SELECT c.*, p.name, p.slug, p.price, p.sale_price, p.image, p.stock
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = ?
        ");
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

// Get cart total
function getCartTotal() {
    $items = getCartItems();
    $total = 0;
    foreach ($items as $item) {
        $price = $item['sale_price'] ?? $item['price'];
        $total += $price * $item['quantity'];
    }
    return $total;
}

// Get session-based cart for guest users (used by cart.php, checkout.php)
function getCart($conn) {
    $items = [];
    $total = 0;

    if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        $ids = array_keys($_SESSION['cart']);
        if (!empty($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $types = str_repeat('i', count($ids));

            $stmt = $conn->prepare("
                SELECT p.*, c.name as category_name
                FROM products p
                JOIN categories c ON p.category_id = c.id
                WHERE p.id IN ($placeholders) AND p.status = 'active'
            ");
            $stmt->bind_param($types, ...$ids);
            $stmt->execute();
            $result = $stmt->get_result();
            $products = $result->fetch_all(MYSQLI_ASSOC);

            foreach ($products as $product) {
                $qty = $_SESSION['cart'][$product['id']] ?? 1;
                $price = $product['sale_price'] ?? $product['price'];
                $product['quantity'] = $qty;
                $product['discount_price'] = $product['sale_price'];
                $items[] = $product;
                $total += $price * $qty;
            }
        }
    }

    return ['items' => $items, 'total' => $total];
}

// Render a single product card HTML
function renderProductCard($product) {

    $name = e($product['name']);
    $slug = $product['slug'];

    $isWishlisted = isInWishlist($product['id']) ? 'active' : '';

    $image = !empty($product['image']) 
        ? 'images/' . $product['image'] 
        : 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=400&h=500&fit=crop';

    $hoverImage = !empty($product['image_hover']) 
        ? 'images/' . $product['image_hover'] 
        : $image;

    $price = $product['sale_price'] ?: $product['price'];

    $discount = $product['sale_price'] 
        ? round((($product['price'] - $product['sale_price']) / $product['price']) * 100)
        : 0;

    $rating = $product['rating'] ?? 4.2;
    $buyers = $product['buyers'] ?? rand(50, 500);

   return '
<div class="product-card">

    <div class="product-image">

        <a href="product.php?slug='.$slug.'">
            <img src="'.$image.'" class="main-img" alt="'.$name.'">
            <img src="'.$hoverImage.'" class="hover-img" alt="'.$name.'">
        </a>

        <div class="rating-badge">
            '.$rating.' ★ | '.$buyers.'
        </div>

        <div class="wishlist-box '.$isWishlisted.'" data-id="'.$product['id'].'">
            <span class="heart">♥</span>
            <span>Wishlist</span>
        </div>

    </div>

    <div class="product-info">
        <h3>
            <a href="product.php?slug='.$slug.'">'.$name.'</a>
        </h3>

        <div class="price">
            ₹'.$price.'
            '.($discount ? '<span class="discount">'.$discount.'% OFF</span>' : '').'
        </div>
    </div>

</div>';
}

// Escape HTML entities shorthand
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// Flash messages
function setFlash($message, $type = 'info') {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function showFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return '<div class="alert alert-' . $flash['type'] . '">' . e($flash['message']) . '</div>';
    }
    return '';
}

// Require authentication
function requireAuth() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}


// Check if product is in user's wishlist
function isInWishlist($product_id) {
    if (!isLoggedIn()) return false;

    global $conn;
    $stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id=? AND product_id=?");
    $stmt->bind_param("ii", $_SESSION['user_id'], $product_id);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}


// Get wishlist count for header
function getWishlistCount() {
    if (!isLoggedIn()) return 0;

    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM wishlist WHERE user_id=?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    return $result['count'] ?? 0;
}