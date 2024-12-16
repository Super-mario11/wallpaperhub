<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallpaper Hub - Home</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Full Page Height */
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
    </style>
</head>
<body>

    <div class="home-container">
        <div class="home-overlay"></div>
        <div class="home-content">
            <h1>Welcome to Wallpaper Hub</h1>
            <p>Discover and download stunning wallpapers for your device!</p>
            <div class="home-actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="index.php">Enter Website</a>
                    <a href="logout.php" class="home-link">Logout</a>
                <?php else: ?>
                    <a href="index.php">Enter Website</a>
                    <a href="login.php" class="home-link">Login</a>
                    <a href="signup.php" class="home-link">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
