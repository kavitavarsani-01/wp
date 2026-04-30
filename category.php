<?php
require_once 'includes/functions.php';
require_once 'includes/header.php';

$type = $_GET['type'] ?? '';

$stmt = $conn->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p
    JOIN categories c ON p.category_id = c.id
    WHERE c.name LIKE ?
    AND p.status = 'active'
");

$search = "%$type%";
$stmt->bind_param("s", $search);
$stmt->execute();

$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<main>
    <section class="products-section">
        <div class="container">

          <h2 class="category-title"><?php echo ucfirst($type); ?> Collection</h2>

            <div class="products-grid">

                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                        <?php echo renderProductCard($product); ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No products found.</p>
                <?php endif; ?>

            </div>

        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
<?php echo "TESTING"; ?>