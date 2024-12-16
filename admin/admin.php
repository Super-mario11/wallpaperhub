<?php
session_start();
require_once '../db.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit;
}

// Handle user deletion
if (isset($_GET['delete_user'])) {
    $deleteUserId = intval($_GET['delete_user']);
    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Delete associated favorites first
        $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ?");
        $stmt->execute([$deleteUserId]);

        // Then delete the user
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$deleteUserId]);

        // Commit the transaction
        $pdo->commit();

        header('Location: admin.php');
        exit;
    } catch (PDOException $e) {
        // Rollback the transaction on error
        $pdo->rollBack();
        $error_message = "Error deleting user: " . $e->getMessage();
    }
}

// Handle adding a new user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $isAdmin = isset($_POST['is_admin']) ? 1 : 0;

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, is_admin) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password, $isAdmin]);
        header('Location: admin.php');
        exit;
    } catch (PDOException $e) {
        $error_message = "Error adding user: " . $e->getMessage();
    }
}

// Fetch all users safely with error handling
try {
    $query = $pdo->prepare("SELECT id, username, is_admin FROM users");
    $query->execute();
    $users = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $users = [];
    $error_message = "Error fetching users: " . $e->getMessage();
}

// Handle wallpaper deletion
if (isset($_GET['delete_wallpaper'])) {
    $wallpaperToDelete = '../assets/' . basename($_GET['delete_wallpaper']);
    if (file_exists($wallpaperToDelete)) {
        unlink($wallpaperToDelete); // Delete the file
        header('Location: admin.php');
        exit;
    }
}

// Handle wallpaper upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['wallpaper'])) {
    $targetDir = '../assets/';
    $fileName = basename($_FILES['wallpaper']['name']);
    $targetFile = $targetDir . $fileName;

    // Check if file is a valid image
    $check = getimagesize($_FILES['wallpaper']['tmp_name']);
    if ($check !== false) {
        if (move_uploaded_file($_FILES['wallpaper']['tmp_name'], $targetFile)) {
            header('Location: admin.php');
            exit;
        } else {
            $error_message = "Error uploading file.";
        }
    } else {
        $error_message = "File is not a valid image.";
    }
}

// Fetch all wallpapers
$wallpapers = glob('../assets/*.{jpg,jpeg,png,gif}', GLOB_BRACE);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <nav>
        <div class="logo">Admin Panel</div>
        <ul class="nav-links">
            <li><a href="../index.php">Home</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>

    <main>
        <h1>Manage Users</h1>
        
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Admin Status</th>
                <th>Actions</th>
            </tr>
            <?php if (is_array($users) && count($users) > 0): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo $user['is_admin'] == 1 ? 'Admin' : 'User'; ?></td>
                        <td>
                            <a href="?delete_user=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No users found.</td>
                </tr>
            <?php endif; ?>
        </table>

        <h2>Add New User</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <label>
                <input type="checkbox" name="is_admin"> Make this user an admin
            </label>
            <button type="submit" name="add_user">Add User</button>
        </form>
    </main>
    <main>
        <h1>Manage Wallpapers</h1>

        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <h2>Upload New Wallpaper</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="wallpaper" required>
            <button type="submit">Upload</button>
        </form>

        <h2>Existing Wallpapers</h2>
        <div class="walls-container">
            <?php foreach ($wallpapers as $wallpaper): ?>
                <div>
                    <img src="<?php echo $wallpaper; ?>" alt="Wallpaper">
                    <a href="?delete_wallpaper=<?php echo basename($wallpaper); ?>" onclick="return confirm('Are you sure you want to delete this wallpaper?');">Delete</a>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
