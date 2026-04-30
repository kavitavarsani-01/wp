<?php
require_once 'includes/functions.php';

requireAuth();

$user = $_SESSION['user'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? $user['name']);
    $phone = sanitize($_POST['phone'] ?? $user['phone']);
    $address = sanitize($_POST['address'] ?? '');

    $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?");
    $stmt->bind_param('sssi', $name, $phone, $address, $user['id']);
    
    if ($stmt->execute()) {
        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['phone'] = $phone;
        $_SESSION['user']['address'] = $address;
        setFlash('Profile updated successfully', 'success');
        redirect('account.php');
    }
}

require_once 'includes/header.php';
?>

<main>
    <section class="account-section">
        <div class="container">
            <h1 style="font-size:2rem;font-weight:700;margin-bottom:32px;">My Account</h1>

            <div class="account-grid">
                <div class="account-sidebar">
                    <h3>Account Menu</h3>
                    <a href="#orders" class="active"><i class="fas fa-shopping-bag"></i> My Orders</a>
                    <a href="#profile"><i class="fas fa-user"></i> Profile</a>
                    <a href="actions/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>

                <div class="account-content">
                    <h2 id="orders">My Orders</h2>
                    <?php if (empty($orders)): ?>
                        <p style="color:var(--text-light);padding:24px 0;">You haven't placed any orders yet.</p>
                    <?php else: ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    <td><?php echo formatPrice($order['total']); ?></td>
                                    <td><span class="status-badge status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <h2 id="profile" style="margin-top:40px;">Profile Settings</h2>
                    <form method="POST" action="" style="max-width:480px;">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" value="<?php echo e($user['name']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" value="<?php echo e($user['email']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="tel" name="phone" value="<?php echo e($user['phone'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" rows="3"><?php echo e($user['address'] ?? ''); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>

