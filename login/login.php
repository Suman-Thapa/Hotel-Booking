<?php
session_start();
include '../includes/functions.php';
include '../includes/connection.php';

$msg = "";
$email_value = ""; // To preserve email input on error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = sanitize($con, $_POST['email']);
    $password = sanitize($con, $_POST['password']);
    $email_value = htmlspecialchars($email); // Keep email in input

    $query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['level']   = $row['level'];
            $_SESSION['name']   = $row['name'];
            

            if ($row['level'] == 'hoteladmin') {
                header("Location: ../hotel-admin/admin_dashboard.php");
            } 
            elseif($row['level'] == 'admin'){
                header("Location: ../admin/index.php");
            }
            else {
                header("Location: ../index.php");
            }
            exit();
        } else {
            $msg = "Invalid email or password!";
        }
    } else {
        $msg = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="../style/formstyle.css">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<style>

</style>
</head>
<body>


<div class="login-container">
    <div class="login-box">
        
        <h2>Login</h2>
        
        <form method="POST" class="login-form">
            <input type="text" name="email" placeholder="Email">
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="submit">Login</button>
        </form>

        <?php if($msg != ""): ?>
            <div class="msg"><?php echo $msg; ?></div>
        <?php endif; ?>

        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</div>

</body>
</html>
