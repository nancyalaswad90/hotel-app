<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Offers Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <header class="main-header bg-light shadow-sm">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a href="<?php echo BASE_URL; ?>" class="navbar-brand">Hotel<span class="text-primary">Offers</span></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <?php if (isLoggedIn()): ?>
                            <li class="nav-item"><a href="<?php echo BASE_URL; ?>/offers" class="nav-link">My Offers</a></li>
                            <li class="nav-item"><a href="<?php echo BASE_URL; ?>/offers/create.php" class="nav-link">Add Offer</a></li>
                            <li class="nav-item"><a href="<?php echo BASE_URL; ?>/auth/logout.php" class="nav-link">Logout</a></li>
                        <?php else: ?>
                            <li class="nav-item"><a href="<?php echo BASE_URL; ?>/auth/login.php" class="nav-link">Login</a></li>
                            <li class="nav-item"><a href="<?php echo BASE_URL; ?>/auth/register.php" class="nav-link">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
        </div>
    </header>
    <main class="container mt-4">
