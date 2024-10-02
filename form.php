<?php

require __DIR__ . '/inc/functions.php';
require __DIR__ . '/inc/db-connect.php';

$success = false;  // Initialize success flag

if (!empty($_POST)) {
    $title = (string) ($_POST['title'] ?? '');
    $created_at = (string) ($_POST['created_at'] ?? date('Y-m-d'));
    $message = (string) ($_POST['message'] ?? '');
    $imageName = null;

    if (!empty($_FILES) && !empty($_FILES['image'])) {
        if ($_FILES['image']['error'] === 0 && $_FILES['image']['size'] !== 0) {
            $nameWithoutExtension = pathinfo($_FILES['image']['name'], PATHINFO_FILENAME);
            $name = preg_replace('/[^a-zA-Z0-9]/', '', $nameWithoutExtension);
    
            $originalImage = $_FILES['image']['tmp_name'];
            $imageName = $name . '-' . time() . '.jpg';
            $destImage = __DIR__ . '/uploads/' . $imageName;
    
            $imageSize = getimagesize($originalImage);
            if (!empty($imageSize)) {
                [$width, $height] = getimagesize($originalImage);
    
                $maxDim = 400;
                $scaleFactor = $maxDim / max($width, $height);
        
                $newWidth = $width * $scaleFactor;
                $newHeight = $height * $scaleFactor;
        
                $im = imagecreatefromjpeg($originalImage);
                if (!empty($im)) {
                    $newImg = imagecreatetruecolor($newWidth, $newHeight);
                    imagecopyresampled($newImg, $im, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            
                    imagejpeg($newImg, $destImage);
                }               
            }
            
        }
    }

    $stmt = $pdo->prepare('INSERT INTO `entries` (`title`, `created_at`, `message`, `image` ) VALUES (:title, :created_at, :message, :image)');
    $stmt->bindValue(':title',$title);
    $stmt->bindValue(':created_at',$created_at);
    $stmt->bindValue(':message',$message);
    $stmt->bindValue(':image', $imageName);
    $stmt ->execute();

    $success = true;  // Set success flag

    //echo '<a href="index.php">Continue to the diary</a>';
    //die();
}

?>

<?php require __DIR__ . '/views/header.php'; ?>
<h1 class="main-heading">New Entry</h1>

<form method="POST" action="form.php" enctype="multipart/form-data">
    <div class="form-group">
        <label class="form-group__label" for="title">Title:</label>
        <input class="form-group__input" type="text" id="title" name="title" required />
    </div>
    <div class="form-group">
        <label class="form-group__label" for="date">Date:</label>
        <input class="form-group__input" type="date" id="date" name="date" required />
    </div>
    <div class="form-group">
        <label class="form-group__label" for="image">Image:</label>
        <input class="form-group__input" type="file" id="image" name="image" />
    </div>
    <div class="form-group">
        <label class="form-group__label" for="message">Message:</label>
        <textarea class="form-group__input" id="message" name="message" rows="6" required></textarea>
    </div>
    <div class="form-submit">
        <button class="button">
            Save
        </button>
    </div>
</form>

<!-- Pop-up JavaScript -->
<?php if ($success): ?>
<script>
    // Show a success pop-up
    alert("Your entry has been saved successfully!");
    // Redirect after closing the pop-up
    window.location.href = "index.php";  // Optional redirect after showing the alert
</script>
<?php endif; ?>

<?php require __DIR__ .'/views/footer.php'; ?> 
