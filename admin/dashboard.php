<?php
require_once '../includes/functions.php';
requireAdmin();

// Stats
$total_orders = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
$total_users = $conn->query("SELECT COUNT(*) as c FROM users WHERE role = 'customer'")->fetch_assoc()['c'];
$total_products = $conn->query("SELECT COUNT(*) as c FROM products WHERE status = 'active'")->fetch_assoc()['c'];
$total_revenue = $conn->query("SELECT COALESCE(SUM(total), 0) as c FROM orders WHERE status != 'cancelled'")->fetch_assoc()['c'];

// Recent orders
$recent_orders = $conn->query("SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Threadify</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <a href="../index.php" class="logo" style="color:#fff;">Threadify Admin</a>
            <nav class="admin-nav">
                <a href="dashboard.php" class="active">Dashboard</a>
                <a href="products.php">Products</a>
                <a href="orders.php">Orders</a>
                <a href="../actions/logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <main class="admin-section">
        <div class="container">
            <h1 style="font-size:1.8rem;font-weight:700;margin-bottom:32px;">Dashboard</h1>

            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-shopping-bag"></i>
                    <h4><?php echo $total_orders; ?></h4>
                    <p>Total Orders</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <h4><?php echo $total_users; ?></h4>
                    <p>Customers</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-tag"></i>
                    <h4><?php echo $total_products; ?></h4>
                    <p>Products</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-dollar-sign"></i>
                    <h4>$<?php echo number_format($total_revenue, 2); ?></h4>
                    <p>Revenue</p>
                </div>
            </div>

            <div class="admin-card">
                <h3>Recent Orders</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo e($order['user_name']); ?></td>
                            <td><?php echo formatPrice($order['total']); ?></td>
                            <td><span class="status-badge status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>

