<?php
require_once 'includes/functions.php';
require_once 'includes/header.php';

// Get featured products
$featured = $conn->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.featured = 1 AND p.status = 'active' ORDER BY p.created_at DESC LIMIT 8");
$featured_products = $featured ? $featured->fetch_all(MYSQLI_ASSOC) : [];

// Get categories
$cat_result = $conn->query("SELECT * FROM categories ORDER BY name ASC LIMIT 4");
$categories = $cat_result ? $cat_result->fetch_all(MYSQLI_ASSOC) : [];
?>

<main>
    <!-- Hero -->
    <section class="hero">
        <div class="container hero-container">
            <div class="hero-content">
                <h1>Elevate Your Style with <span>Threadify</span></h1>
                <p>Discover the latest trends in women's fashion. From elegant dresses to casual wear, find your perfect look.</p>
                <a href="shop.php" class="btn btn-primary btn-lg">Shop Now <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="hero-image">
                <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?w=800&h=600&fit=crop" alt="Fashion">
            </div>
    </section>

   <section class="category-section">
    <h2>Shop by Category</h2>
    <p>Find exactly what you're looking for</p>

    <div class="carousel-container">
        <button class="arrow left" onclick="scrollCategories(-1)">&#10094;</button>

        <div class="category-carousel" id="categoryCarousel">

            <a href="category.php?type=casual" class="category-card">
                <img src="images/casual.jpg">
                <h3>Casual Wear</h3>
            </a>

            <a href="category.php?type=ethnic" class="category-card">
                <img src="images/ethnic.jpg">
                <h3>Ethnic Wear</h3>
            </a>

            <a href="category.php?type=formal" class="category-card">
                <img src="images/professional.jpg">
                <h3>Professional Wear</h3>
            </a>

            <a href="category.php?type=party" class="category-card">
                <img src="images/party.jpg">
                <h3>Party Wear</h3>
            </a>

            <a href="category.php?type=sleep" class="category-card">
                <img src="images/sleep.jpg">
                <h3>Sleep Wear</h3>
            </a>

            <a href="category.php?type=accessories" class="category-card">
                <img src="images/accesories.jpg">
                <h3>Accessories</h3>
            </a>

        </div>

        <button class="arrow right" onclick="scrollCategories(1)">&#10095;</button>
    </div>
</section>

    <!-- Featured Products -->
    <section class="products-section">
        <div class="container">
            <div class="section-heading">
                <h2>Trending Now</h2>
                <p>Our most popular picks this season</p>
            </div>
            <div class="products-grid">
                <?php foreach ($featured_products as $product): ?>
                <?php echo renderProductCard($product); ?>
                <?php endforeach; ?>
            </div>
            <div style="text-align:center;margin-top:40px;">
                <a href="shop.php" class="btn btn-secondary btn-lg">View All Products <i class="fas fa-arrow-right"></i></a>
            </div>
    </section>

    <!-- Features -->
    <section class="features-section">
        <div class="container">
            <div class="features-grid">
                <div class="feature-item">
                    <i class="fas fa-shipping-fast"></i>
                    <h3>Free Shipping</h3>
                    <p>On all orders over $50</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-undo"></i>
                    <h3>Easy Returns</h3>
                    <p>30-day return policy</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-lock"></i>
                    <h3>Secure Payment</h3>
                    <p>100% secure checkout</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-headset"></i>
                    <h3>24/7 Support</h3>
                    <p>Dedicated support team</p>
                </div>
        </div>
    </section>

    <!-- Newsletter Banner -->
    <section class="newsletter-banner">
        <div class="container">
            <h2>Join Our Newsletter</h2>
            <p>Subscribe to get special offers, free giveaways, and early access to new arrivals.</p>
            <form action="actions/subscribe.php" method="POST" onsubmit="event.preventDefault(); showNotification('Thank you for subscribing!', 'success');">
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
