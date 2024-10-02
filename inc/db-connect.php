<?php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=diary;charset=utf8mb4', 'root', 'mypasswordhere', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
}
catch (PDOException $e) {
    // var_dump($e->getMessage());
    echo 'A problem occured with the database connection...';
    die();
}

?>
