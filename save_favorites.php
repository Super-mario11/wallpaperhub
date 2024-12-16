<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $favorites = $data['favorites'];
    $userId = $_SESSION['user_id'];

    $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ?");
    $stmt->execute([$userId]);

    foreach ($favorites as $fav) {
        $stmt = $pdo->prepare("INSERT INTO favorites (user_id, image_path) VALUES (?, ?)");
        $stmt->execute([$userId, $fav]);
    }
}
