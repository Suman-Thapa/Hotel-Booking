<?php
session_start();
include '../includes/navbar.php';
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

            if ($row['level'] == 1) {
                header("Location: ../admin/admin_dashboard.php");
            } else {
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
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background: #f4f6f9;
}

.login-container {
    width: 100%;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.login-box {
    background: white;
    padding: 30px 40px;
    border-radius: 10px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.2);
    width: 350px;
    text-align: center;
}

.login-box h2 {
    margin-bottom: 25px;
    color: #2E7D32;
    font-size: 24px;
}

.login-box input[type="text"],
.login-box input[type="email"],
.login-box input[type="password"] {
    width: 100%;
    padding: 12px 14px;
    margin: 8px 0 18px 0;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 15px;
    outline: none;
    transition: 0.3s;
}

.login-box input:focus {
    border-color: #2E7D32;
    box-shadow: 0 0 4px rgba(46, 125, 50, 0.5);
}

.login-box button {
    width: 100%;
    padding: 12px;
    background: #2E7D32;
    color: white;
    font-size: 16px;
    font-weight: bold;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s;
}

.login-box button:hover {
    background: #256528;
}

.login-box p {
    margin-top: 15px;
    font-size: 14px;
    color: #555;
}

.login-box a {
    color: #2E7D32;
    text-decoration: none;
    font-weight: bold;
}

.login-box a:hover {
    text-decoration: underline;
}

.msg {
    color: red;
    font-size: 14px;
    margin-top: 10px;
    font-weight: bold;
}
</style>
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <h2>Login</h2>

        <form method="POST">
            <input type="text" name="email" placeholder="Email" value="<?php echo $email_value; ?>" required>
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
