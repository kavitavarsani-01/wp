<?php
require_once '../includes/functions.php';
requireAdmin();

$products = $conn->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Threadify Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <a href="../index.php" class="logo" style="color:#fff;">Threadify Admin</a>
            <nav class="admin-nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="products.php" class="active">Products</a>
                <a href="orders.php">
