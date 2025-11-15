<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include 'includes/connection.php';
include 'includes/functions.php';
include 'includes/navbar.php';

if (!isset($_SESSION['user_id'])) {
    die("You must <a href='login.php'>login</a> to view profile.");
}

$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = sanitize($con, $_POST['name']);
    $email = sanitize($con, $_POST['email']);

    // Handle image upload
    $image_sql = "";
    if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] == 0) {
        // Move uploaded file to uploads/user/ folder
        $target_file = "uploads/user/" . $_FILES['user_image']['name'];
        move_uploaded_file($_FILES['user_image']['tmp_name'], $target_file);

        // Save filename in DB
        $image_sql = ", user_image='" . $_FILES['user_image']['name'] . "'";
    }

    // Optional password update
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET name='$name', email='$email', password='$password' $image_sql WHERE user_id='$user_id'";
    } else {
        $update_query = "UPDATE users SET name='$name', email='$email' $image_sql WHERE user_id='$user_id'";
    }

    mysqli_query($con, $update_query);
}

// Fetch current user data
$result = mysqli_query($con, "SELECT * FROM users WHERE user_id='$user_id'");
$row = mysqli_fetch_assoc($result);
?>
<div class="wrapper">
 <div class="content">   

<h2 style="text-align:center;">View / Edit Profile</h2>

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

    <form method="POST" action="" enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:15px; width:100%;">
    <div style="display:flex; align-items:center;">
        <label style="width:120px; text-align:right; margin-right:10px;">Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required style="flex:1; padding:5px;">
    </div>

    <div style="display:flex; align-items:center;">
        <label style="width:120px; text-align:right; margin-right:10px;">Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required style="flex:1; padding:5px;">
    </div>

    <div style="display:flex; align-items:center;">
        <label style="width:120px; text-align:right; margin-right:10px;">Password:</label>
        <input type="password" name="password" placeholder="Leave blank to keep current" style="flex:1; padding:5px;">
    </div>

    <div style="display:flex; align-items:center;">
        <label style="width:120px; text-align:right; margin-right:10px;">Upload Image:</label>
        <input type="file" name="user_image" accept="image/*" style="flex:1;">
    </div>

    <div style="text-align:center; margin-top:10px;">
        <button type="submit" style="padding:10px 20px;">Update Profile</button>
    </div>
</form>

</div>
</div>

<?php include 'includes/footer.php' ?>
</div>
