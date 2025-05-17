<?php
require_once '../includes/config.php';
require_once '../includes/auth_functions.php';

if (isLoggedIn()) {
    header("Location: " . BASE_URL . "/offers");
    exit();
}

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    
    // Validate inputs
    if (empty($username)) {
        $errors['username'] = 'Username is required';
    }
    
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    }
    
    // Authenticate user
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                header("Location: " . BASE_URL . "/offers");
                exit();
            } else {
                $errors['general'] = 'Invalid username or password';
            }
        } catch (PDOException $e) {
            $errors['general'] = 'Login failed: ' . $e->getMessage();
        }
    }
}

// Check for success message from registration
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>

<?php include '../includes/header.php'; ?>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/auth.css">

<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow-sm" style="width: 100%; max-width: 400px;">
        <h1 class="text-center mb-4">Login</h1>
        
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username or Email</label>
                <input type="text" id="username" name="username" class="form-control" 
                       value="<?php echo htmlspecialchars($username); ?>" required>
                <?php if (!empty($errors['username'])): ?>
                    <small class="text-danger"><?php echo $errors['username']; ?></small>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
                <?php if (!empty($errors['password'])): ?>
                    <small class="text-danger"><?php echo $errors['password']; ?></small>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        
        <div class="text-center mt-3">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
