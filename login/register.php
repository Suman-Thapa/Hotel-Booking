<?php
include '../includes/functions.php';
include '../includes/connection.php';

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
                  VALUES ('$username', '$email', '$hashed_password', 'user')";

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
    <link rel="stylesheet" href="../style/formstyle.css">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register</title>
<style>

</style>
</head>
<body>

<div class="register-container">
    <div class="register-box">
        <h2>Register</h2>

        <form id="registerForm" method="POST" class="register-form">
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
