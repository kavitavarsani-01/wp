<?php
require_once 'includes/functions.php';
require_once 'includes/header.php';

requireAuth(); // 🔥 login protection

$stmt = $conn->prepare("
    SELECT p.* FROM wishlist w
    JOIN products p ON w.product_id = p.id
    WHERE w.user_id = ?
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<main>
    <div class="container">
        <h2>Your Wishlist</h2>

        <div class="products-grid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <?php echo renderProductCard($product); ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No items in wishlist</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>