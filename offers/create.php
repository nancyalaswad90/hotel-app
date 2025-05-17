<?php
require_once '../includes/config.php';
require_once '../includes/auth_functions.php';

redirectIfNotLoggedIn();

$errors = [];
$title = $description = $price = $location = $amenities = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    
    // Handle file upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/images/offers/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_name = time() . '_' . basename($_FILES['image']['name']);
        $target_path = $upload_dir . $file_name;
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $image_path = 'assets/images/offers/' . $file_name;
            } else {
                $errors['image'] = 'Failed to upload image';
            }
        } else {
            $errors['image'] = 'Only JPG, PNG, and GIF files are allowed';
        }
    }
    
    // Create offer if no errors
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO hotel_offers (user_id, title, description, price, location, amenities, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_SESSION['user_id'],
                $title,
                $description,
                $price,
                $location,
                $amenities,
                $image_path
            ]);
            
            $_SESSION['success_message'] = 'Offer created successfully!';
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $errors['general'] = 'Failed to create offer: ' . $e->getMessage();
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<h1>Create New Offer</h1>

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
        <textarea id="description" name="description" class="form-control" required><?php echo htmlspecialchars($description); ?></textarea>
        <?php if (!empty($errors['description'])): ?>
            <small class="text-danger"><?php echo $errors['description']; ?></small>
        <?php endif; ?>
    </div>
    
    <div class="form-group">
        <label for="price" class="form-label">Price per night ($)</label>
        <input type="number" step="0.01" id="price" name="price" class="form-control" 
               value="<?php echo htmlspecialchars($price); ?>" required>
        <?php if (!empty($errors['price'])): ?>
            <small class="text-danger"><?php echo $errors['price']; ?></small>
        <?php endif; ?>
    </div>
    
    <div class="form-group">
        <label for="location" class="form-label">Location</label>
        <input type="text" id="location" name="location" class="form-control" 
               value="<?php echo htmlspecialchars($location); ?>" required>
        <?php if (!empty($errors['location'])): ?>
            <small class="text-danger"><?php echo $errors['location']; ?></small>
        <?php endif; ?>
    </div>
    
    <div class="form-group">
        <label for="amenities" class="form-label">Amenities (comma separated)</label>
        <input type="text" id="amenities" name="amenities" class="form-control" 
               value="<?php echo htmlspecialchars($amenities); ?>">
        <small>Example: Pool, WiFi, Breakfast, Gym</small>
    </div>
    
    <div class="form-group">
        <label for="image" class="form-label">Image</label>
        <input type="file" id="image" name="image" class="form-control">
        <?php if (!empty($errors['image'])): ?>
            <small class="text-danger"><?php echo $errors['image']; ?></small>
        <?php endif; ?>
    </div>
    
    <button type="submit" class="btn btn-secondary">Create Offer</button>
    <a href="index.php" class="btn">Cancel</a>
</form>

<?php include '../includes/footer.php'; ?>
