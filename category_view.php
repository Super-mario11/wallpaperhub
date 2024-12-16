<?php
session_start();
require_once 'db.php';

// Get the selected category from URL query string
if (isset($_GET['category'])) {
    $selected_category = strtolower($_GET['category']);
} else {
    header("Location: categories.php");
    exit;
}

// Fetch all wallpapers from the selected category
$wallpapers = glob("assets/{$selected_category}*.{jpg,jpeg,png,gif}", GLOB_BRACE);

// Handle search functionality
$search_results = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $search_query = strtolower($_GET['search']);
    foreach ($wallpapers as $file) {
        if (strpos(strtolower(basename($file)), $search_query) !== false) {
            $search_results[] = $file;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($selected_category); ?> Wallpapers</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <div class="logo">Wallpaper Hub</div>
        <ul class="nav-links">
            <li><a href="home.php">Home</a></li>
            <li><a href="index.php">Wallpaper</a></li>
            <li><a href="favorites.php">Favorites</a></li>
            <li><a href="categories.php">Categories</a></li>
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                <li><a href="admin/admin.php">Admin</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="signup.php">Signup</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Main Content -->
    <main>
        <div class="header-container">
            <h1><?php echo ucfirst($selected_category); ?> Wallpapers</h1>
            <form method="GET" action="category_view.php" class="search-container">
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($selected_category); ?>">
                <input type="text" name="search" placeholder="Search wallpapers..." required>
                <button type="submit">🔍 Search</button>
            </form>
            <a href="categories.php" class="back-button">← Back to Categories</a>
        </div>

        <!-- Search Results Section -->
        <?php if (!empty($search_results)): ?>
            <div class="wallpaper-results">
                <h2>Search Results in <?php echo ucfirst($selected_category); ?></h2>
                <div class="wallpaper-grid">
                    <?php foreach ($search_results as $wallpaper): ?>
                        <div class="wallpaper-container">
                            <img src="<?php echo htmlspecialchars($wallpaper); ?>" alt="Wallpaper">
                            <p><?php echo htmlspecialchars(basename($wallpaper)); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php elseif (isset($_GET['search'])): ?>
            <div class="wallpaper-results">
                <h2>No Results Found in <?php echo ucfirst($selected_category); ?></h2>
                <p>No wallpapers matched your search query.</p>
            </div>
        <?php else: ?>
            <div class="wallpaper-section">
                <h2>All Wallpapers in <?php echo ucfirst($selected_category); ?></h2>
                <div class="wallpaper-grid">
                    <?php foreach ($wallpapers as $wallpaper): ?>
                        <div class="wallpaper-container">
                            <img src="<?php echo htmlspecialchars($wallpaper); ?>" alt="Wallpaper">
                            <p><?php echo htmlspecialchars(basename($wallpaper)); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
