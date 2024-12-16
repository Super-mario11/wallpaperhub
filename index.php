<?php
session_start();
require_once 'db.php';

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$is_admin = $is_logged_in && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1; // Check if user is admin
$wallpapers = [];

// Fetch wallpapers only if logged in
if ($is_logged_in) {
    $wallpapers = array_map(function ($n) {
        return "assets/img$n.jpg";
    }, range(1, 12));
}

// Fetch user's favorites
$favorites = [];
if ($is_logged_in) {
    $stmt = $pdo->prepare("SELECT image_path FROM favorites WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $favorites = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
$wallpapers = glob('assets/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallpaper Hub</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Navbar -->
    <nav>
        <div class="logo">Wallpaper Hub</div>
        <ul class="nav-links">
        <li><a href="home.php">Home</a></li>
    <?php if ($is_logged_in): ?>
        <li><a href="#" onclick="showSection('home')">Wallpaper</a></li>
        <li><a href="#favorites" onclick="showSection('favorites')">Favorites</a></li>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
            <li><a href="admin/admin.php">Admin</a></li>
        <?php endif; ?>
        <li><a href="categories.php">Categories</a></li>
        <li><a href="contact.php">Contact Us</a></li>
        <li><a href="logout.php">Logout</a></li>
    <?php else: ?>
        <li><a href="contact.php">Contact Us</a></li>
        <li><a href="login.php">Login</a></li>
        <li><a href="signup.php">Signup</a></li>
    <?php endif; ?>
</ul>

    </nav>

    <main>
        <!-- Home Section -->
        <div id="home" class="section active">
            <h2>Welcome to the Wallpaper Gallery!</h2>
            <div class="wallpaper-grid">
                <?php foreach ($wallpapers as $wallpaper): ?>
                    <div class="wallpaper-container">
                        <img src="<?php echo $wallpaper; ?>" alt="wallpaper">
                        <div class="image-overlay">
                            <button class="favorite-btn" onclick="toggleFavorite('<?php echo $wallpaper; ?>')">❤️</button>
                            <button class="download-btn" onclick="downloadImage('<?php echo $wallpaper; ?>')">⬇️</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Favorites Section -->
        <div id="favorites" class="section">
            <h2>Your Favorite Wallpapers</h2>
            <div class="favorite-grid" id="favoritesGrid">
                <!-- Dynamically populated by JS -->
            </div>
        </div>
    </main>

    <script>
        let favorites = <?php echo json_encode($favorites); ?>;

        function toggleFavorite(imagePath) {
            if (favorites.includes(imagePath)) {
                favorites = favorites.filter(img => img !== imagePath);
            } else {
                favorites.push(imagePath);
            }
            updateFavorites();
            saveFavorites();
        }

        function saveFavorites() {
            fetch('save_favorites.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ favorites })
            });
        }

        function updateFavorites() {
            const grid = document.getElementById('favoritesGrid');
            grid.innerHTML = '';
            favorites.forEach(imagePath => {
                const div = document.createElement('div');
                div.className = 'wallpaper-container';
                div.innerHTML = `
                    <img src="${imagePath}" alt="wallpaper">
                    <div class="image-overlay">
                        <button class="download-btn" onclick="downloadImage('${imagePath}')">⬇️</button>
                    </div>
                `;
                grid.appendChild(div);
            });
        }

        function downloadImage(imagePath) {
            const link = document.createElement('a');
            link.href = imagePath;
            link.download = '';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function showSection(sectionId) {
            document.querySelectorAll('.section').forEach(sec => sec.classList.remove('active'));
            document.getElementById(sectionId).classList.add('active');
        }

        // Initialize favorites on page load
        window.onload = function () {
            updateFavorites();
        };
    </script>
</body>
</html>
