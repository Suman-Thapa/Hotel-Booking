<?php

include '../includes/connection.php';
$sucessmsg = '';
$name = '';
$email = '';
$password = '';

if(isset($_POST['submit'])){
    $name = trim($_POST['username']);
    $password = trim($_POST['password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $value = trim($_POST['select-user']);
    $email = trim($_POST['email']);

    $query = "INSERT INTO users (name, email, password, level)
              VALUES ('$name', '$email', '$hashed_password', '$value')";

    if(mysqli_query($con,$query)){
        header('location:index.php');
        exit();
    } else {
        $sucessmsg = "Problem in user creation: " . mysqli_error($con);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add new user</title>
    <link rel="stylesheet" href="../style/formstyle.css">
</head>
<body>
    

<div class="add-user-container">
    <div class="adduser">
        <h2>Add User</h2>
        
        <form method="post">
            <div class="form-container">
            <div>
                <label>User Name</label>
                <input type="text" name="username" value="<?= $name ?>" required>
            </div>

            <div>
                <label>Password</label>
                <input type="password" name="password" value="<?= $password ?>" required>
            </div>

            <div>
                <label>Email</label>
                <input type="text" name="email" value="<?= $email ?>" required>
            </div>

            <div>
                <label>User Role</label>
                <select name="select-user">
                    <option value="hoteladmin">Hotel Admin</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <button type="submit" name="submit">Submit</button>

            <p style="color:red;"><?= $sucessmsg ?></p>
        </form>
        </div>

    </div>
</div>

</body>
</html>