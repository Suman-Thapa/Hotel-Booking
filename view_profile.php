<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include 'connection.php';
include 'functions.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    die("You must <a href='login.php'>login</a> to view profile.");
}

$user_id = $_SESSION['user_id'];

// Fetch current user data
$result = mysqli_query($con, "SELECT * FROM users WHERE user_id='$user_id'");
$row = mysqli_fetch_assoc($result);
?>

<h2 style="text-align:center;">My Profile</h2>

<div style="width:400px; margin:auto; border:1px solid #ccc; padding:20px; text-align:center;">
    <?php
    // Display current user image
    $img_path = "uploads/user/" . $row['user_image'];
    if (!empty($row['user_image']) && file_exists($img_path)) {
        echo "<img src='" . str_replace('\\','/',$img_path) . "' style='width:120px; height:120px; object-fit:cover; border-radius:50%; margin-bottom:10px;'>";
    } else {
        echo "<img src='uploads/user/default.png' style='width:120px; height:120px; object-fit:cover; border-radius:50%; margin-bottom:10px;'>";
    }
    ?>

    <p><b>Name:</b> <?php echo htmlspecialchars($row['name']); ?></p>
    <p><b>Email:</b> <?php echo htmlspecialchars($row['email']); ?></p>

    <a href="edit_profile.php" style="display:inline-block; padding:10px 20px; background:green; color:white; text-decoration:none; border-radius:5px; margin-top:10px;">Edit Profile</a>
</div>
