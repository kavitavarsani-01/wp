<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

// Get filter parameters
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'newest';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));

$per_page = 12;
$offset = ($page - 1) * $per_page;

// Build WHERE clause
$where = ["p.status = 'active'", "c.status = 'active'"];
$params = [];
$types = '';

if ($category) {
    $where[] = "c.slug = ?";
    $params[] = $category;
    $types .= 's';
}

if ($search) {
    $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}

if ($min_price !== '') {
    $where[] = "COALESCE(p.sale_price, p.price) >= ?";
    $params[] = floatval($min_price);
    $types .= 'd';
}

if ($max_price !== '') {
    $where[] = "COALESCE(p.sale_price, p.price) <= ?";
    $params[] = floatval($max_price);
    $types .= 'd';
}

$where_clause = implode(' AND ', $where);

// Sorting
switch ($sort) {
    case 'price_low':
        $order = 'COALESCE(p.sale_price, p.price) ASC';
        break;
    case 'price_high':
        $order = 'COALESCE(p.sale_price, p.price) DESC';
        break;
    case 'name':
        $order = 'p.name ASC';
        break;
    default:
        $order = 'p.created_at DESC';
}

// Count total products
$count_sql = "SELECT COUNT(*) as total
              FROM products p
              JOIN categories c ON p.category_id = c.id
              WHERE $where_clause";

$stmt = $conn->prepare($count_sql);
if ($types) $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total / $per_page);

// Fetch products
$sql = "SELECT p.*, c.name as category_name
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE $where_clause
        ORDER BY $order
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);

if ($types) {
    $stmt->bind_param($types . 'ii', ...array_merge($params, [$per_page, $offset]));
} else {
    $stmt->bind_param('ii', $per_page, $offset);
}

$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get categories
$categories = $conn->query("SELECT * FROM categories WHERE status='active' ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

// Current category name
$current_category_name = '';
foreach ($categories as $cat) {
    if ($cat['slug'] === $category) {
        $current_category_name = $cat['name'];
        break;
    }
}
?>

<main>

<!-- HERO -->
<section class="shop-hero">
    <div class="container">
        <h1><?php echo $current_category_name ?: 'All Products'; ?></h1>
        <p>Explore our latest fashion collection</p>
    </div>
</section>

<div class="container shop-container">

<!-- SIDEBAR -->
<aside class="sidebar">

    <!-- Categories -->
    <div class="filter-group">
        <h3>Categories</h3>
        <ul class="filter-list">
            <li>
                <a href="shop.php" class="<?php echo !$category ? 'active' : ''; ?>">
                    All Products
                </a>
            </li>

            <?php foreach ($categories as $cat): ?>
            <li>
                <a href="shop.php?category=<?php echo $cat['slug']; ?>"
                   class="<?php echo $category === $cat['slug'] ? 'active' : ''; ?>">
                    <?php echo e($cat['name']); ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Price Filter -->
    <div class="filter-group">
        <h3>Price Range</h3>

        <form method="GET">
            <?php if ($category): ?><input type="hidden" name="category" value="<?php echo e($category); ?>"><?php endif; ?>
            <?php if ($search): ?><input type="hidden" name="search" value="<?php echo e($search); ?>"><?php endif; ?>

            <input type="number" name="min_price" placeholder="Min" value="<?php echo e($min_price); ?>">
            <input type="number" name="max_price" placeholder="Max" value="<?php echo e($max_price); ?>">

            <button class="btn btn-primary" style="width:100%;margin-top:10px;">
                Apply
            </button>
        </form>
    </div>

</aside>

<!-- PRODUCTS -->
<div class="shop-content">

    <!-- Toolbar -->
    <div class="shop-toolbar">
        <p><?php echo $total; ?> Products Found</p>

        <form method="GET">
            <?php if ($category): ?><input type="hidden" name="category" value="<?php echo e($category); ?>"><?php endif; ?>

            <select name="sort" onchange="this.form.submit()">
                <option value="newest" <?php echo $sort==='newest'?'selected':''; ?>>Newest</option>
                <option value="price_low" <?php echo $sort==='price_low'?'selected':''; ?>>Low → High</option>
                <option value="price_high" <?php echo $sort==='price_high'?'selected':''; ?>>High → Low</option>
                <option value="name" <?php echo $sort==='name'?'selected':''; ?>>Name A-Z</option>
            </select>
        </form>
    </div>

    <!-- Products Grid -->
    <div class="products-grid">
        <?php if ($products): ?>
            <?php foreach ($products as $product): ?>
                <?php echo renderProductCard($product); ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products found</p>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): 
            $query = $_GET;
            $query['page'] = $i;
        ?>
            <a href="shop.php?<?php echo http_build_query($query); ?>"
               class="<?php echo $i == $page ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

</div>
</div>

</main>

<?php require_once 'includes/footer.php'; ?>