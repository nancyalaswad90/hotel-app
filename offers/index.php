<?php
require_once '../includes/config.php';
require_once '../includes/auth_functions.php';

redirectIfNotLoggedIn();

// Get all offers for the current user
try {
    $stmt = $pdo->prepare("SELECT * FROM hotel_offers WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $offers = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching offers: " . $e->getMessage());
}

$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">My Hotel Offers</h1>
        <a href="create.php" class="btn btn-primary">Add New Offer</a>
    </div>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($offers)): ?>
        <div class="alert alert-info text-center">No offers found. Create your first offer!</div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($offers as $offer): ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($offer['image_path'])): ?>
                            <img src="<?php echo BASE_URL . '/' . $offer['image_path']; ?>" alt="<?php echo htmlspecialchars($offer['title']); ?>" class="card-img-top">
                        <?php else: ?>
                            <img src="<?php echo BASE_URL; ?>/assets/images/default-hotel.jpg" alt="Default hotel image" class="card-img-top">
                        <?php endif; ?>

                        <div class="card-body">
                            <h5 class="card-title text-truncate"><?php echo htmlspecialchars($offer['title']); ?></h5>
                            <p class="card-text text-muted"><?php echo htmlspecialchars(substr($offer['description'], 0, 100)); ?>...</p>
                            <p class="card-text">
                                <i class="fas fa-map-marker-alt text-danger"></i>
                                <?php echo htmlspecialchars($offer['location']); ?>
                            </p>
                            <p class="card-text fw-bold">$<?php echo number_format($offer['price'], 2); ?></p>
                        </div>

                        <div class="card-footer d-flex justify-content-between">
                            <a href="edit.php?id=<?php echo $offer['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete.php?id=<?php echo $offer['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this offer?')">Delete</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
