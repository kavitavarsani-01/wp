<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// 🔐 Login check
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if (!$product_id) {
    header("Location: shop.php");
    exit;
}

// CHECK EXISTING
$stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id=? AND product_id=?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($action === 'add' && $result->num_rows == 0) {

    $stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();

} elseif ($action === 'remove' && $result->num_rows > 0) {

    $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id=? AND product_id=?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
}

// 🔙 Redirect back
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;