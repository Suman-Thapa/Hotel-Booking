<?php
if (session_status() == PHP_SESSION_NONE) 
session_start();
include 'connection.php';

// Default values
$level = null;
$user_id = $_SESSION['user_id'] ?? null;

if ($user_id) {
    $user = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM users WHERE user_id='$user_id'"));
    $level = (int)$user['level']; // 0 = normal user, 1 = admin
}
?>

<nav style="background:#4CAF50; padding:20px; display:flex; justify-content:space-between; align-items:center;">
    <div>
        <!-- Home button works even if not logged in -->
        <a href="<?php 
            if ($level === 1) {
                echo 'admin_dashboard.php';
            } else {
                echo 'index.php';
            }
        ?>">Home</a>
        
        <?php if ($user_id && $level==0): ?>
            <a href="my_bookings.php">View Bookings</a>
        <?php endif; ?>

        <?php if ($user_id && $level==1): ?>
            <a href="admin_invoices.php">Invoices</a>
            <a href="admin_pending_cancellations.php">Pending Cancellations</a>
        <?php endif; ?>
    </div>

    <div>
        <?php if ($user_id): ?>
            <a href="view_profile.php">View Profile</a>
            
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>
<style>
body {
    margin: 0;
    padding: 0;
}

nav {
    background: #2E7D32;
    padding: 16px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-family: Arial, sans-serif;
    font-size: 17px;
}

nav a {
    color: white;
    text-decoration: none;
    margin-right: 20px;
    padding: 8px 14px;
    border-radius: 6px;
    transition: background 0.3s, color 0.3s, border-bottom 0.3s;
}

nav a:hover {
    background: rgba(255, 255, 255, 0.25);
}

nav a.active {
    background: white;
    color: #2E7D32;
    font-weight: bold;
    border-bottom: 3px solid #2E7D32; /* underline effect */
}

nav div:last-child a {
    margin-right: 0;
}
</style>

