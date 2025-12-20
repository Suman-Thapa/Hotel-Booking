<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
include '../includes/connection.php';
session_start();
$error_msg = '';

if(isset($_POST['submit'])){
    $email = mysqli_real_escape_string($con,$_POST['email']);
    $result = mysqli_query($con,"select *from users where(email = '$email') limit 1");
    if(mysqli_num_rows($result)==1){
        session_start();
        $_SESSION['reset_email'] = $email;
        $otp = rand(100000, 999999);
        if(mysqli_query($con,"Insert into otp(email,otp) values('$email','$otp')")){
            
        

        require '../PHPMailer/Exception.php';
        require '../PHPMailer/PHPMailer.php';
        require '../PHPMailer/SMTP.php';

        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER; // turn ON for testing
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'hellowitsmesuman123@gmail.com';
            $mail->Password   = 'aegisenykdqryclf'; // APP PASSWORD
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            $mail->setFrom('hellowitsmesuman123@gmail.com', 'Hotel Booking  System');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Verify Your OTP';
            $mail->Body    = "<p>Reset Your Password from the given otp:</p><br><h2>$otp</h2><br>the otp is expired with in 5 minutes";

            $mail->send();
            header('location:verify_otp.php');
        } 
        catch (Exception $e) {
            echo "Mailer Error: {$mail->ErrorInfo}";
        }

            
        }
    }
    else
        {
           $error_msg =  "Invalid email please register";
        }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
    <link rel="stylesheet" href="../style/formstyle.css">
</head>
<body>
    <div class="container">
    <form method="post">
        <div class="form-box">
            <h2>Forget Password</h2>
            <div>
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" placeholder="Enter your Email Address" required>
                <p class="error"><?= $error_msg ?></p>
            </div>
            
                <button type="submit" name="submit">Forget password</button>
           
        </div>
        
    </form>
    </div>
    
</body>
</html>



