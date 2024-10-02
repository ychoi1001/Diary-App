<?php

require __DIR__ . '/inc/db-connect.php';

// Get the entry ID from the form POST data
$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    // Perform the deletion
    $stmt = $pdo->prepare('DELETE FROM `entries` WHERE `id` = :id');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}

// Redirect back to the index page after deletion
header('Location: index.php');
exit;

?>
