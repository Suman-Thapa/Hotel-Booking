<?php
include '../includes/connection.php';

if(!isset($_GET['admin_id'])){
    die("No admin ID provided!");
}

$hotel_admin_id = intval($_GET['admin_id']); // always sanitize GET

if(isset($_POST['submit'])){
    $hotelName = mysqli_real_escape_string($con, $_POST['hotel_name']);
    $location = mysqli_real_escape_string($con, $_POST['location']);
    $description = mysqli_real_escape_string($con, $_POST['description']);

    // Handle image upload
    if (isset($_FILES['hotel_image']) && $_FILES['hotel_image']['error'] == 0) {
        $uploadDir = "uploads/hotels/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $hotel_image = mysqli_real_escape_string($con, $_POST['hotel_image']);
        $targetFile = $uploadDir . $hotel_image;
        move_uploaded_file($_FILES['hotel_image']['tmp_name'], $targetFile);
    }

    $q = "INSERT INTO hotels (hotel_name, location, hotel_admin_id, about, hotel_image)
          VALUES ('$hotelName', '$location', $hotel_admin_id, '$description', '$hotelimage')";

    if(mysqli_query($con, $q)){
        header('location:index.php');
        exit;
    } else {
        die("Error inserting hotel: " . mysqli_error($con));
    }
}
?>





<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../style/formstyle.css">
    <title>Add Hotel</title>
    
</head>
<body>

    <div class="add-hotel-container">
    <div class="addhotel">
    <h2>Add Hotel</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-container">
            <div>
                <label>Hotel Name:</label>
                <input type="text" name="hotel_name" required>
            </div>

            <div>
            <label>Location:</label>
            <input type="text" name="location" required>
            </div>


            <div>
                <label>Hotel Image:</label>
                <input type="file" name="hotel_image" accept="image/*">
            </div>
            <div>
                <label for="discription">About</label>
                <textarea name="description" id="discription" placeholder="About Hotel"></textarea>
            </div>

            <button type="submit" name="submit">Add Hotel</button>
        </div>
    </form>
    </div>
    </div>
</body>
</html>