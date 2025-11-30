<?php
session_start();
include '../includes/connection.php';
// include '../includes/functions.php';
// include '../includes/navbar.php';

// if ($_SESSION['level'] != 1) {
//     header("Location: login.php");
//     exit();
// }

$id = $_GET['id'] ?? 0;
$result = mysqli_query($con, "SELECT * FROM users WHERE user_id='$id'");
$user = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = mysqli_real_escape_string($con, $_POST['user_name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $role = $_POST['role'];

    

    

    $update = "UPDATE users 
               SET name='$user_name', email='$email', level = '$role'
               WHERE user_id='$id'";

    mysqli_query($con, $update);
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
     <link rel="stylesheet" href="../style/formstyle.css">
    <title>Edit User</title>
    
</head>
<body>
    
    <div class="edit-user-container">
    <div class="edituser">
    <h2>Edit User</h2>
    <form method="POST" enctype="multipart/form-data">
        
        <div class="form-container">
        <div>
            <label>User Name:</label>
            <input type="text" name="user_name" value="<?= htmlspecialchars($user['name']); ?>" required>
        </div>

        <div>
            <label>Email:</label>
            <input type="text" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
        </div>

        <div>
            <label>Role</label>
            <select name="role">
                
                <!-- <option value="user"<?php if($user['level']=='user') echo "selected"?>>user</option> -->
                <option value="hoteladmin"<?php if($user['level']=='hoteladmin') echo "selected"?>>Hotel-Admin</option>
                <option value="admin"<?php if($user['level']=='admin') echo "selected"?>>Admin</option>
            </select>
        </div>


        <button type="submit">Update User</button>
        </div>
      </div>
      </div>
    </form>


    </body>
    </html>