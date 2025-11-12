<?php
include '../includes/functions.php';
include '../includes/connection.php';
include '../includes/navbar.php';

$successMsg = '';
$errorMsg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize($con, $_POST['username']);
    $email    = sanitize($con, $_POST['email']);
    $password = sanitize($con, $_POST['password']);

    // Check if email already exists
    $checkQuery = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($con, $checkQuery);

    if(mysqli_num_rows($result) > 0){
        $errorMsg = "Email already exists. Please use another email.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert into database
        $query = "INSERT INTO users (name, email, password, level) 
                  VALUES ('$username', '$email', '$hashed_password', 0)";

        if (mysqli_query($con, $query)) {
            $successMsg = "Registration successful. <a href='login.php'>Login here</a>";
        } else {
            $errorMsg = "Error: " . mysqli_error($con);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register</title>
<style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background: #f4f6f9;
}

.register-container {
    width: 100%;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.register-box {
    background: white;
    padding: 30px 40px;
    border-radius: 10px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.2);
    width: 350px;
    text-align: center;
}

.register-box h2 {
    margin-bottom: 25px;
    color: #2E7D32;
    font-size: 24px;
}

.register-box input[type="text"],
.register-box input[type="email"],
.register-box input[type="password"] {
    width: 100%;
    padding: 12px 14px;
    margin: 8px 0 18px 0;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 15px;
    outline: none;
    transition: 0.3s;
}

.register-box input:focus {
    border-color: #2E7D32;
    box-shadow: 0 0 4px rgba(46, 125, 50, 0.5);
}

.register-box button {
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

.register-box button:hover {
    background: #256528;
}

.register-box p {
    margin-top: 15px;
    font-size: 14px;
    color: #555;
}

.register-box a {
    color: #2E7D32;
    text-decoration: none;
    font-weight: bold;
}

.register-box a:hover {
    text-decoration: underline;
}

.error  {
    color: red;
    font-size: 14px;
    margin-top: 20px;

}
.success  {
    color: green;
    font-size: 14px;
    margin-top: 20px;

}
</style>
</head>
<body>

<div class="register-container">
    <div class="register-box">
        <h2>Register</h2>

        <form id="registerForm" method="POST" action="">
            <div class="error" id="error"></div>
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit" name="submit">Register</button>

            <!-- Simple Success/Error message -->
            <?php 
            if($successMsg != '') echo "<div class='success'>$successMsg</div>"; 
            if($errorMsg != '') echo "<div class='error'>$errorMsg</div>"; 
            ?>
        </form>

        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', function(event) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const errorDiv = document.getElementById('error');

    errorDiv.textContent = '';

    if(password !== confirmPassword) {
        errorDiv.textContent = 'Passwords do not match!';
        event.preventDefault();
        return;
    }

    const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/;
    if(!passwordPattern.test(password)) {
        errorDiv.textContent = 'Password must be at least 6 characters long and include at least one number and one letter.';
        event.preventDefault();
        return;
    }
});
</script>

</body>
</html>
