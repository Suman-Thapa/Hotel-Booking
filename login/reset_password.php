<?php
include '../includes/connection.php';
session_start();
if(!(isset($_SESSION['otp-verified']) || $_SESSION['password-verified'])){
    header('location:forget_password.php');
}
$email = $_SESSION['reset_email'];
$msg_error = "";
$sucess_msg = "";

if(isset($_POST['change_password'])){
    $new_password = $_POST['new_password'];
    $conform_password = $_POST['conform_password'];
    if($new_password != $conform_password){
        $msg_error = "Conform Password Doesn't Match with New Password";
    }
    else{

        $hashed_password = password_hash($new_password,PASSWORD_DEFAULT);
        $updatequery = "update users set password ='$hashed_password' where(email ='$email')";
        if(mysqli_query($con,$updatequery)){
            $sucess_msg = "Password update sucessfully"."<a href='login.php'>Login here</a>";
            unset($_SESSION['otp-verified']);
            unset($_SESSION['reset_email']);
        }
        else
        {
            $msg_error =  "Problem in Password update";
        }
    }

}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/formstyle.css">
    <title>Change Password</title>
</head>
<body>
    <div class="container">
        <form method="post">
            <div class="form-box">
                <h2>Change Password</h2>
                
                    <p><b>Your Email:</b><b style="color: blue"><?=  $email?></b></p>
                
                <div>
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" id="new_password" placeholder="Enter the New Password " required>
                </div>
                <div>
                    <label for="conform_password">Conform Password</label>
                    <input type="password" name="conform_password" id="conform_password" placeholder="Enter the Conform Password" required>
                    <p class="error"><?= $msg_error ?></p>
                </div>
                <button type="submit" name="change_password">Change Password</button>
                <p class="success"><?= $sucess_msg?></p>
            </div>
        </form>
    </div>
</body>
</html>