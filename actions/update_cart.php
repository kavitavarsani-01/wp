<?php
require_once '../includes/functions.php';

$product_id = intval($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if (!$product_id || !in_array($action, ['increase', 'decrease'])) {
    redirect('../cart.php');
}

$stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
$stmt->bind_param('i', $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if ($action === 'increase') {
    if ($_SESSION['cart'][$product_id] < ($product['stock'] ?? 999)) {
        $_SESSION['cart'][$product_id]++;
    }
} else {
    $_SESSION['cart'][$product_id]--;
    if ($_SESSION['cart'][$product_id] < 1) {
        unset($_SESSION['cart'][$product_id]);
    }
}

redirect('../cart.php');

