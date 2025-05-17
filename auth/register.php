<?php
require_once '../includes/config.php';
require_once '../includes/auth_functions.php';

if (isLoggedIn()) {
    header("Location: " . BASE_URL . "/offers");
    exit();
}

$errors = [];
$username = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate inputs
    if (empty($username)) {
        $errors['username'] = 'Username is required';
    } elseif (strlen($username) < 4) {
        $errors['username'] = 'Username must be at least 4 characters';
    }
    
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!validateEmail($email)) {
        $errors['email'] = 'Please enter a valid email address';
    }
    
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } elseif (!validatePassword($password)) {
        $errors['password'] = 'Password must be at least 8 characters';
    }
    
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match';
    }
    
    // Check if username or email already exists
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->rowCount() > 0) {
                $errors['general'] = 'Username or email already exists';
            }
        } catch (PDOException $e) {
            $errors['general'] = 'Database error: ' . $e->getMessage();
        }
    }
    // Register user if no errors
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashed_password]);
            
            $_SESSION['success_message'] = 'Registration successful! Please login.';
            header("Location: login.php");
            exit();
        } catch (PDOException $e) {
            $errors['general'] = 'Registration failed: ' . $e->getMessage();
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/auth.css">

<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow-sm" style="width: 100%; max-width: 400px;">
        <h1 class="text-center mb-4">Create Account</h1>
        
        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" 
                       value="<?php echo htmlspecialchars($username); ?>" required>
                <?php if (!empty($errors['username'])): ?>
                    <small class="text-danger"><?php echo $errors['username']; ?></small>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" 
                       value="<?php echo htmlspecialchars($email); ?>" required>
                <?php if (!empty($errors['email'])): ?>
                    <small class="text-danger"><?php echo $errors['email']; ?></small>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
                <?php if (!empty($errors['password'])): ?>
                    <small class="text-danger"><?php echo $errors['password']; ?></small>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                <?php if (!empty($errors['confirm_password'])): ?>
                    <small class="text-danger"><?php echo $errors['confirm_password']; ?></small>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
        
        <div class="text-center mt-3">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
