<?php
require_once '../includes/config.php';
require_once '../includes/auth_functions.php';

redirectIfNotLoggedIn();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$offer_id = $_GET['id'];
$errors = [];

// Get the current offer
try {
    $stmt = $pdo->prepare("SELECT * FROM hotel_offers WHERE id = ? AND user_id = ?");
    $stmt->execute([$offer_id, $_SESSION['user_id']]);
    $offer = $stmt->fetch();
    
    if (!$offer) {
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error fetching offer: " . $e->getMessage());
}

// Initialize form fields with current offer data
$title = $offer['title'];
$description = $offer['description'];
$price = $offer['price'];
$location = $offer['location'];
$amenities = $offer['amenities'];
$current_image = $offer['image_path'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $title = sanitizeInput($_POST['title']);
    $description = sanitizeInput($_POST['description']);
    $price = sanitizeInput($_POST['price']);
    $location = sanitizeInput($_POST['location']);
    $amenities = sanitizeInput($_POST['amenities']);
    
    // Validate inputs
    if (empty($title)) {
        $errors['title'] = 'Title is required';
    }
    
    if (empty($description)) {
        $errors['description'] = 'Description is required';
    }
    
    if (empty($price)) {
        $errors['price'] = 'Price is required';
    } elseif (!is_numeric($price) || $price <= 0) {
        $errors['price'] = 'Price must be a positive number';
    }
    
    if (empty($location)) {
        $errors['location'] = 'Location is required';
    }
    
    // Handle file upload if a new image is provided
    $image_path = $current_image;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/images/offers/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Generate unique filename
        $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = 'offer_' . time() . '.' . $file_ext;
        $target_path = $upload_dir . $file_name;
        
        // Validate image type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                // Delete old image if it exists
                if (!empty($current_image) && file_exists('../../' . $current_image)) {
                    unlink('../' . $current_image);
                }
                $image_path = 'assets/images/offers/' . $file_name;
            } else {
                $errors['image'] = 'Failed to upload image';
            }
        } else {
            $errors['image'] = 'Only JPG, PNG, and GIF files are allowed';
        }
    }
    
    // Update offer if no errors
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE hotel_offers SET 
                title = ?, 
                description = ?, 
                price = ?, 
                location = ?, 
                amenities = ?, 
                image_path = ?, 
                updated_at = NOW() 
                WHERE id = ? AND user_id = ?");
            
            $stmt->execute([
                $title,
                $description,
                $price,
                $location,
                $amenities,
                $image_path,
                $offer_id,
                $_SESSION['user_id']
            ]);
            
            $_SESSION['success_message'] = 'Offer updated successfully!';
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $errors['general'] = 'Failed to update offer: ' . $e->getMessage();
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="container">
    <h1>Edit Offer</h1>
    
    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title" class="form-label">Title</label>
            <input type="text" id="title" name="title" class="form-control" 
                   value="<?php echo htmlspecialchars($title); ?>" required>
            <?php if (!empty($errors['title'])): ?>
                <small class="text-danger"><?php echo $errors['title']; ?></small>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" 
                      rows="5" required><?php echo htmlspecialchars($description); ?></textarea>
            <?php if (!empty($errors['description'])): ?>
                <small class="text-danger"><?php echo $errors['description']; ?></small>
            <?php endif; ?>
        </div>
        
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="price" class="form-label">Price per night ($)</label>
                <input type="number" step="0.01" id="price" name="price" class="form-control" 
                       value="<?php echo htmlspecialchars($price); ?>" required>
                <?php if (!empty($errors['price'])): ?>
                    <small class="text-danger"><?php echo $errors['price']; ?></small>
                <?php endif; ?>
            </div>
            
            <div class="form-group col-md-6">
                <label for="location" class="form-label">Location</label>
                <input type="text" id="location" name="location" class="form-control" 
                       value="<?php echo htmlspecialchars($location); ?>" required>
                <?php if (!empty($errors['location'])): ?>
                    <small class="text-danger"><?php echo $errors['location']; ?></small>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-group">
            <label for="amenities" class="form-label">Amenities (comma separated)</label>
            <input type="text" id="amenities" name="amenities" class="form-control" 
                   value="<?php echo htmlspecialchars($amenities); ?>">
            <small class="text-muted">Example: Pool, WiFi, Breakfast, Gym</small>
        </div>
        
        <div class="form-group">
            <label for="image" class="form-label">Image</label>
            <?php if (!empty($current_image)): ?>
                <div class="current-image mb-2">
                    <img src="<?php echo BASE_URL . '/' . $current_image; ?>" 
                         alt="Current offer image" class="img-thumbnail" style="max-height: 200px;">
                    <div class="form-check mt-2">
                        <input type="checkbox" id="remove_image" name="remove_image" class="form-check-input">
                        <label for="remove_image" class="form-check-label">Remove current image</label>
                    </div>
                </div>
            <?php endif; ?>
            <input type="file" id="image" name="image" class="form-control-file">
            <?php if (!empty($errors['image'])): ?>
                <small class="text-danger"><?php echo $errors['image']; ?></small>
            <?php endif; ?>
            <small class="text-muted">Leave blank to keep current image</small>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Update Offer</button>
            <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>