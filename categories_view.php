<?php
session_start();
require_once 'db.php';

$category = $_GET['category'] ?? '';
$wallpapers = [];

if ($category) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM wallpapers WHERE category = ?");
        $stmt->execute([$category]);
        $wallpapers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_message = "Error fetching wallpapers: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category); ?> Wallpapers</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <nav>
        <div class="logo">Wallpaper Hub</div>
        <ul class="nav-links">
            <li><a href="home.php">Home</a></li>
            <li><a href="index.php">Wallpaper</a></li>
            <li><a href="categories.php">Categories</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main>
        <h1><?php echo htmlspecialchars($category); ?> Wallpapers</h1>

        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <div class="wallpaper-grid">
            <?php foreach ($wallpapers as $wallpaper): ?>
                <div class="wallpaper-container">
                    <img src="<?php echo htmlspecialchars($wallpaper['image_path']); ?>" alt="Wallpaper">
                    <p><?php echo htmlspecialchars($wallpaper['title']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
