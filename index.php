<?php
require_once 'includes/config.php';
require_once 'includes/auth_functions.php';

// Get featured offers (for non-logged in users) or all offers (for logged in users)
try {
    if (isLoggedIn()) {
        // For logged in users, show their offers
        $stmt = $pdo->prepare("SELECT * FROM hotel_offers WHERE user_id = ? ORDER BY created_at DESC LIMIT 6");
        $stmt->execute([$_SESSION['user_id']]);
        $offers = $stmt->fetchAll();
        $page_title = "My Offers";
    } else {
        // For guests, show featured offers
        $stmt = $pdo->prepare("SELECT * FROM hotel_offers ORDER BY created_at DESC LIMIT 6");
        $stmt->execute();
        $offers = $stmt->fetchAll();
        $page_title = "Featured Hotel Offers";
    }
} catch (PDOException $e) {
    die("Error fetching offers: " . $e->getMessage());
}
?>

<?php include 'includes/header.php'; ?>

<div class="hero-section bg-primary text-white text-center py-5">
    <div class="container">
        <h1 class="display-4">Find Your Perfect Stay</h1>
        <p class="lead">Discover amazing hotel offers at the best prices</p>
        <?php if (!isLoggedIn()): ?>
            <div class="mt-4">
                <a href="<?php echo BASE_URL; ?>/auth/register.php" class="btn btn-secondary btn-lg me-2">Sign Up</a>
                <a href="<?php echo BASE_URL; ?>/auth/login.php" class="btn btn-light btn-lg">Login</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="container mt-5">
    <h2 class="section-title text-center mb-4"><?php echo $page_title; ?></h2>
    
    <?php if (empty($offers)): ?>
        <p class="text-center">No offers found.</p>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($offers as $offer): ?>
                <div class="col-md-4">
                    <div class="card h-100">
                        <?php if (!empty($offer['image_path'])): ?>
                            <img src="<?php echo BASE_URL . '/' . $offer['image_path']; ?>" alt="<?php echo htmlspecialchars($offer['title']); ?>" class="card-img-top">
                        <?php else: ?>
                            <img src="<?php echo BASE_URL; ?>/assets/images/default-hotel.jpg" alt="Default hotel image" class="card-img-top">
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($offer['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars(substr($offer['description'], 0, 100)); ?>...</p>
                            <p class="card-text"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($offer['location']); ?></p>
                            <p class="card-text fw-bold">$<?php echo number_format($offer['price'], 2); ?></p>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <?php if (isLoggedIn()): ?>
                                <div class="d-flex justify-content-between">
                                    <a href="offers/edit.php?id=<?php echo $offer['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="offers/delete.php?id=<?php echo $offer['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this offer?')">Delete</a>
                                </div>
                            <?php else: ?>
                                <a href="auth/login.php" class="btn btn-secondary btn-sm">View Details</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (isLoggedIn()): ?>
            <div class="text-center mt-4">
                <a href="offers/" class="btn btn-secondary">View All My Offers</a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>