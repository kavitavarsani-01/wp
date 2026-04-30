<?php
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if ($name && $email && $subject && $message) {
        // Placeholder: send email logic here
        setFlash('Thank you for your message! We will get back to you soon.', 'success');
    } else {
        setFlash('Please fill in all fields.', 'error');
    }
    redirect('contact.php');
}

require_once 'includes/header.php';
?>

<main>
    <section class="shop-hero">
        <div class="container">
            <h1>Contact Us</h1>
            <p>We'd love to hear from you</p>
        </div>
    </section>

    <div class="container" style="padding:60px 0;">
        <div style="max-width:600px;margin:0 auto;">
            <?php echo showFlash(); ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" required placeholder="Your name">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required placeholder="your@email.com">
                </div>
                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="subject" required placeholder="How can we help?">
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" rows="5" required placeholder="Your message..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Send Message</button>
            </form>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:24px;margin-top:60px;text-align:center;">
                <div>
                    <i class="fas fa-envelope" style="font-size:1.5rem;color:var(--primary);margin-bottom:12px;"></i>
                    <h4 style="font-weight:600;margin-bottom:4px;">Email</h4>
                    <p style="color:var(--text-light);font-size:0.9rem;">hello@threadify.com</p>
                </div>
                <div>
                    <i class="fas fa-phone" style="font-size:1.5rem;color:var(--primary);margin-bottom:12px;"></i>
                    <h4 style="font-weight:600;margin-bottom:4px;">Phone</h4>
                    <p style="color:var(--text-light);font-size:0.9rem;">+1 (555) 123-4567</p>
                </div>
                <div>
                    <i class="fas fa-map-marker-alt" style="font-size:1.5rem;color:var(--primary);margin-bottom:12px;"></i>
                    <h4 style="font-weight:600;margin-bottom:4px;">Address</h4>
                    <p style="color:var(--text-light);font-size:0.9rem;">123 Fashion Ave, NY</p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>

