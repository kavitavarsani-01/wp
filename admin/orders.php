<?php
require_once '../includes/functions.php';

requireAuth();
if ($_SESSION['user']['role'] !== 'admin') {
    redirect('../index.php');
}

// Update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $_POST['status'], $_POST['order_id']);
    $stmt->execute();
    setFlash('Order status updated', 'success');
    redirect('orders.php');
}

$orders = $conn->query("SELECT o.*, u.name as user_name, u.email as user_email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC")->fetch_all(MYSQLI_ASSOC);

require_once '../includes/header.php';
?>

<main>
    <div class="admin-header">
        <div class="container">
            <div style="display:flex;align-items:center;gap:16px;">
                <a href="../index.php" style="font-size:1.2rem;font-weight:700;color:#fff;">Threadify Admin</a>
            </div>
            <nav class="admin-nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="products.php">Products</a>
                <a href="orders.php" class="active">Orders</a>
                <a href="../actions/logout.php">Logout</a>
            </nav>
        </div>
    </div>

    <section class="admin-section">
        <div class="container">
            <h2 style="margin-bottom:24px;">Orders</h2>
            <div class="admin-card">
                <?php echo showFlash(); ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo e($order['user_name']); ?><br><small style="color:var(--text-light);"><?php echo e($order['user_email']); ?></small></td>
                            <td><?php echo formatPrice($order['total']); ?></td>
                            <td><span class="status-badge status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            <td>
                                <form method="POST" style="display:flex;gap:8px;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="status" style="padding:6px 12px;border:1px solid var(--border);border-radius:var(--radius);">
                                        <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                        <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary" style="padding:6px 14px;font-size:0.85rem;">Update</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>

