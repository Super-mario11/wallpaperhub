<nav>
    <div class="logo">Wallpaper Downloader</div>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="admin.php">Admin Panel</a></li>
        <?php endif; ?>
    </ul>
</nav>
