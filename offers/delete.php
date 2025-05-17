<?php
require_once '../includes/config.php';
require_once '../includes/auth_functions.php';

redirectIfNotLoggedIn();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$offer_id = $_GET['id'];

// Verify the offer belongs to the current user before deleting
try {
    $stmt = $pdo->prepare("SELECT image_path FROM hotel_offers WHERE id = ? AND user_id = ?");
    $stmt->execute([$offer_id, $_SESSION['user_id']]);
    $offer = $stmt->fetch();
    
    if (!$offer) {
        header("Location: index.php");
        exit();
    }
    
    // Delete the offer
    $stmt = $pdo->prepare("DELETE FROM hotel_offers WHERE id = ? AND user_id = ?");
    $stmt->execute([$offer_id, $_SESSION['user_id']]);
    
    // Delete the associated image if it exists
    if (!empty($offer['image_path']) && file_exists('../' . $offer['image_path'])) {
        unlink('../' . $offer['image_path']);
    }
    
    $_SESSION['success_message'] = 'Offer deleted successfully!';
} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Failed to delete offer: ' . $e->getMessage();
}

header("Location: index.php");
exit();
?>
