<?php
require_once 'includes/functions.php';

$cart = getCart($conn);
if (empty($cart['items'])) {
    redirect('cart.php');
}

// If not logged in, redirect to login
if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    setFlash('Please login to complete your order.', 'warning');
    redirect('login.php');
}

require_once 'includes/header.php';

$cart_total = $cart['total'];
$user = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_name = sanitize($_POST['shipping_name'] ?? $user['name']);
    $shipping_email = sanitize($_POST['shipping_email'] ?? $user['email']);
    $shipping_phone = sanitize($_POST['shipping_phone'] ?? '');
    $shipping_address = sanitize($_POST['shipping_address'] ?? '');
    $shipping_city = sanitize($_POST['shipping_city'] ?? '');
    $shipping_state = sanitize($_POST['shipping_state'] ?? '');
    $shipping_zip = sanitize($_POST['shipping_zip'] ?? '');
    $payment_method = sanitize($_POST['payment_method'] ?? 'cod');

    $errors = [];
    if (empty($shipping_address)) $errors[] = 'Shipping address is required';
    if (empty($shipping_city)) $errors[] = 'City is required';
    if (empty($shipping_phone)) $errors[] = 'Phone is required';

    if (empty($errors)) {
        $shipping_full = "$shipping_address, $shipping_city, $shipping_state $shipping_zip";

        // Create order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total, status, payment_status, shipping_name, shipping_email, shipping_phone, shipping_address, payment_method) VALUES (?, ?, 'pending', 'pending', ?, ?, ?, ?, ?)");
        $stmt->bind_param('idsssss', $user['id'], $cart_total, $shipping_name, $shipping_email, $shipping_phone, $shipping_full, $payment_method);
        $stmt->execute();
        $order_id = $stmt->insert_id;

        // Add order items
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($cart['items'] as $item) {
            $price = $item['discount_price'] ?: $item['price'];
            $stmt->bind_param('iiid', $order_id, $item['id'], $item['quantity'], $price);
            $stmt->execute();

            // Update stock
            $conn->query("UPDATE products SET stock = stock - {$item['quantity']} WHERE id = {$item['id']}");
        }

        // Clear cart
        $_SESSION['cart'] = [];

        setFlash('Order placed successfully! Your order number is #' . $order_id, 'success');
        redirect('account.php');
    } else {
        foreach ($errors as $error) {
            setFlash($error, 'error');
        }
    }
}
?>

<main>
    <section class="checkout-section">
        <div class="container">
            <h1 style="font-size:2rem;font-weight:700;margin-bottom:32px;">Checkout</h1>

            <div class="checkout-grid">
                <div class="checkout-form">
                    <form method="POST" action="">
                        <h3 class="form-section-title">Shipping Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="shipping_name" value="<?php echo e($user['name'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="shipping_email" value="<?php echo e($user['email'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="tel" name="shipping_phone" value="<?php echo e($user['phone'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="shipping_address" rows="3" required><?php echo e($user['address'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>City</label>
                                <input type="text" name="shipping_city" required>
                            </div>
                            <div class="form-group">
                                <label>State</label>
                                <input type="text" name="shipping_state" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>ZIP Code</label>
                            <input type="text" name="shipping_zip" required>
                        </div>

                        <h3 class="form-section-title" style="margin-top:32px;">Payment Method</h3>
                        <div class="form-group">
                            <label style="display:flex;align-items:center;gap:8px;padding:12px;border:1px solid var(--border);border-radius:var(--radius);cursor:pointer;">
                                <input type="radio" name="payment_method" value="cod" checked style="width:auto;">
                                <i class="fas fa-money-bill-wave"></i> Cash on Delivery
                            </label>
                        </div>
                        <div class="form-group">
                            <label style="display:flex;align-items:center;gap:8px;padding:12px;border:1px solid var(--border);border-radius:var(--radius);cursor:pointer;">
                                <input type="radio" name="payment_method" value="card" style="width:auto;">
                                <i class="fas fa-credit-card"></i> Credit/Debit Card
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;margin-top:24px;">
                            Place Order - <?php echo formatPrice($cart_total); ?>
                        </button>
                    </form>
                </div>

                <div class="cart-summary">
                    <h3>Order Summary</h3>
                    <?php foreach ($cart['items'] as $item):
                        $price = $item['discount_price'] ?: $item['price'];
                    ?>
                    <div class="summary-row">
                        <span><?php echo e($item['name']); ?> x<?php echo $item['quantity']; ?></span>
                        <span><?php echo formatPrice($price * $item['quantity']); ?></span>
                    </div>
                    <?php endforeach; ?>
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span><?php echo formatPrice($cart_total); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span><?php echo formatPrice($cart_total); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>

