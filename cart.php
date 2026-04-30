<?php
session_start();
require_once 'includes/functions.php';
require_once 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['product_id'];
    $qty = $_POST['quantity'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + $qty;

    header("Location: cart.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$items = [];
$total = 0;

if ($cart) {
    $ids = implode(',', array_keys($cart));
    $result = $conn->query("SELECT * FROM products WHERE id IN ($ids)");

    while ($row = $result->fetch_assoc()) {
        $row['qty'] = $cart[$row['id']];
        $row['subtotal'] = $row['qty'] * ($row['sale_price'] ?: $row['price']);
        $total += $row['subtotal'];
        $items[] = $row;
    }
}
?>

<main class="cart-page">
<div class="container">

<h1>Your Cart</h1>

<?php if($items): ?>

<table class="cart-table">
<tr>
<th>Product</th>
<th>Price</th>
<th>Qty</th>
<th>Total</th>
</tr>

<?php foreach($items as $item): ?>
<tr>
<td><?php echo $item['name']; ?></td>
<td>₹<?php echo $item['price']; ?></td>
<td><?php echo $item['qty']; ?></td>
<td>₹<?php echo $item['subtotal']; ?></td>
</tr>
<?php endforeach; ?>

</table>

<h2>Total: ₹<?php echo $total; ?></h2>

<?php else: ?>
<p>Your cart is empty</p>
<?php endif; ?>

</div>
</main>

<?php require_once 'includes/footer.php'; ?>