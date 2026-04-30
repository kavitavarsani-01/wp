document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const nav = document.querySelector('.nav');
    if (mobileMenuBtn && nav) {
        mobileMenuBtn.addEventListener('click', function() {
            nav.classList.toggle('active');
        });
    }

    // Quantity Selectors
    document.querySelectorAll('.quantity-selector').forEach(function(selector) {
        const minus = selector.querySelector('.qty-minus');
        const plus = selector.querySelector('.qty-plus');
        const input = selector.querySelector('input');
        if (minus && plus && input) {
            minus.addEventListener('click', function() {
                let val = parseInt(input.value) || 1;
                if (val > 1) input.value = val - 1;
            });
            plus.addEventListener('click', function() {
                let val = parseInt(input.value) || 1;
                input.value = val + 1;
            });
        }
    });

    // Flash messages auto-dismiss
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s ease';
            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 5000);
    });

    // Add to Cart AJAX
    document.querySelectorAll('.add-to-cart-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.id;
            const qtyInput = document.querySelector('#quantity');
            const quantity = qtyInput ? qtyInput.value : 1;

            fetch('actions/add_to_cart.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'product_id=' + encodeURIComponent(productId) + '&quantity=' + encodeURIComponent(quantity)
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) {
                    const badge = document.querySelector('.cart-badge');
                    if (badge) badge.textContent = data.count;
                    showNotification('Added to cart!', 'success');
                } else {
                    showNotification(data.message || 'Error adding to cart', 'error');
                }
            })
            .catch(function() {
                showNotification('Error adding to cart', 'error');
            });
        });
    });
});

function showNotification(message, type) {
    const existing = document.querySelector('.notification');
    if (existing) existing.remove();

    const div = document.createElement('div');
    div.className = 'notification notification-' + type;
    div.textContent = message;
    div.style.cssText = 'position:fixed;top:90px;right:20px;padding:14px 24px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:9999;font-weight:500;transition:all 0.3s ease;';
    if (type === 'success') {
        div.style.background = '#d4edda';
        div.style.color = '#155724';
    } else {
        div.style.background = '#f8d7da';
        div.style.color = '#721c24';
    }

    document.body.appendChild(div);
    setTimeout(function() {
        div.style.opacity = '0';
        div.style.transform = 'translateY(-20px)';
        setTimeout(function() { div.remove(); }, 300);
    }, 3000);
}



// Wishlist Toggle

document.querySelectorAll('.wishlist-box').forEach(btn => {

    btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        let el = this;
        let productId = el.dataset.id;

        fetch('wishlist.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'product_id=' + productId
        })
        .then(res => res.json())
        .then(data => {

            if (data.message === 'login_required') {
                window.location.href = 'login.php';
                return;
            }

            if (data.status === 'added') {
                el.classList.add('active');
            } 
            else if (data.status === 'removed') {
                el.classList.remove('active');
            }

        })
        .catch(() => {
            alert("Something went wrong");
        });

    });

});





// Category Carousel Scroll
function scrollCategories(direction) {
    const container = document.getElementById("categoryCarousel");
    const scrollAmount = 250;

    container.scrollBy({
        left: direction * scrollAmount,
        behavior: "smooth"
    });
}