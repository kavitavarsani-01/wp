<?php
require_once '../includes/functions.php';

$product_id = intval($_GET['id'] ?? 0);

if ($product_id && isset($_SESSION['cart'][$product_id])) {
    unset($_SESSION['cart'][$product_id]);
}

redirect('../cart.php');

