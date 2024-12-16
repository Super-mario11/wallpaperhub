<?php
session_start();
require_once 'db.php';

// Fetch all wallpapers
$wallpapers = glob('assets/*.{jpg,jpeg,png,gif}', GLOB_BRACE);

// Group wallpapers by category
$categories = [];
foreach ($wallpapers as $file) {
    $filename = basename($file);
    $parts = explode('_', $filename, 2); // Split by underscore to get category prefix
    $category = strtolower(pathinfo($parts[0], PATHINFO_FILENAME)); // Extract category name
    if (!empty($category)) {
        $categories[$category][] = $file; // Group wallpapers under the category
    }
}

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
    <title>Categories</title>
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
                <li><a href="contact.php">Contact Us</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="contact.php">Contact Us</a></li>
                <li><a href="signup.php">Signup</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Main Content -->
    <main>
        <h1>Wallpaper Categories</h1>

        <!-- Search Engine -->
        <div class="search-container">
            <form method="GET" action="categories.php">
                <input type="text" name="search" placeholder="Search wallpapers..." required>
                <button type="submit">🔍 Search</button>
            </form>
        </div>

        <!-- Search Results Section -->
        <?php if (!empty($search_results)): ?>
            <div class="search-results">
                <h2>Search Results</h2>
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
            <div class="search-results">
                <h2>Search Results</h2>
                <p>No wallpapers found for "<?php echo htmlspecialchars($_GET['search']); ?>".</p>
            </div>
        <?php endif; ?>

        <!-- Categories Section -->
        <section class="categories-section">
            <h2>Explore by Category</h2>
            <ul class="categories-list">
                <?php foreach ($categories as $category => $files): ?>
                    <li>
                        <a href="category_view.php?category=<?php echo urlencode($category); ?>">
                            <?php echo ucfirst($category) . " (" . count($files) . ")"; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </main>
</body>
</html>
