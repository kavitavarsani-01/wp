<?php
require_once 'includes/functions.php';
require_once 'includes/header.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) redirect('shop.php');

// Get product
$stmt = $conn->prepare("
SELECT p.*, c.name as category_name, c.slug as category_slug
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE p.slug = ? AND p.status='active'
");
$stmt->bind_param("s", $slug);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) redirect('shop.php');

$price = $product['sale_price'] ?: $product['price'];

// Related
$stmt = $conn->prepare("
SELECT * FROM products 
WHERE category_id=? AND id!=? AND status='active'
LIMIT 4
");
$stmt->bind_param("ii", $product['category_id'], $product['id']);
$stmt->execute();
$related = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<main class="product-page">
<div class="container product-layout">

    <div class="product-image">
        <img src="images/<?php echo $product['image']; ?>">
    </div>

    <div class="product-info">
        <?php echo $product['image']; ?> <!-- 🔥 debug -->
        <p class="category"><?php echo $product['category_name']; ?></p>
        <h1><?php echo $product['name']; ?></h1>

        <div class="price">
            <?php if ($product['sale_price']): ?>
                <span class="sale">₹<?php echo $product['sale_price']; ?></span>
                <span class="old">₹<?php echo $product['price']; ?></span>
            <?php else: ?>
                <span class="normal">₹<?php echo $product['price']; ?></span>
            <?php endif; ?>
        </div>

        <p class="desc"><?php echo $product['description']; ?></p>

        <form method="POST" action="cart.php">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            
            <div class="qty-box">
                <input type="number" name="quantity" value="1" min="1">
            </div>

            <button class="btn-primary">Add to Cart</button>
        </form>

    </div>

</div>

<!-- Related -->
<section class="related">
    <h2>You May Also Like</h2>
    <div class="products-grid">
        <?php foreach($related as $p): ?>
            <?php echo renderProductCard($p); ?>
        <?php endforeach; ?>
    </div>
</section>
</main>

<?php require_once 'includes/footer.php'; ?>