<?php
session_start();
require_once 'db.php';

$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Validate inputs
    if (!empty($name) && !empty($email) && !empty($message)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $message]);
            $success_message = "Thank you for reaching out. We'll get back to you soon.";
        } catch (PDOException $e) {
            $error_message = "Error submitting your message: " . $e->getMessage();
        }
    } else {
        $error_message = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
    font-family: Arial, sans-serif;
    color: White;
    margin: 0;
    padding: 0;
    background-color: #f4f4f9;
}

/* Glass Form Styling */
.glass-form {
    margin: auto;
    padding: 20px;
    max-width: 500px;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 10px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    color: #333; /* Ensures text is dark and visible */
    font-size: 16px;
}

.glass-form input, 
.glass-form textarea {
    width: calc(100% - 20px);
    margin: 10px 0;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
    color: #333; /* Ensures input text is dark and visible */
    background-color: rgba(255, 255, 255, 0.9);
}

.glass-form textarea {
    height: 100px;
    resize: none;
}

.glass-form input::placeholder, 
.glass-form textarea::placeholder {
    color: #777; /* Placeholder text color for better visibility */
}

.glass-form button {
    background-color: #3498db;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    font-size: 16px;
}

.glass-form button:hover {
    background-color: #2980b9;
}

.success-message, .error-message {
    text-align: center;
    padding: 10px;
    color: white;
    margin: 10px 0;
    border-radius: 5px;
}

.success-message {
    background-color: #27ae60;
}

.error-message {
    background-color: #e74c3c;
}


    </style>
</head>
<body>
    <nav>
        <div class="logo">Wallpaper Hub</div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="index.php#favorites">Favorites</a></li>
            <li><a href="categories.php">Categories</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                <li><a href="admin/admin.php">Admin</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="contact.php">Contact Us</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="signup.php">Signup</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main>
        <h1>Contact Us</h1>
        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php elseif (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" class="glass-form">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <textarea name="message" placeholder="Your Message" required></textarea>
            <button type="submit">Send</button>
        </form>
    </main>
</body>
</html>
