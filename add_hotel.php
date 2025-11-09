<?php
session_start();
include 'connection.php';
include 'functions.php';
include 'navbar.php';

if ($_SESSION['level'] != 1) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hotel_name = sanitize($con, $_POST['hotel_name']);
    $location = sanitize($con, $_POST['location']);
    $available_rooms = sanitize($con, $_POST['available_rooms']);
    $price_per_room = sanitize($con, $_POST['price_per_room']);
    $hotel_image = "";

    // Handle image upload
    if (isset($_FILES['hotel_image']) && $_FILES['hotel_image']['error'] == 0) {
        $uploadDir = "uploads/hotels/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $hotel_image = basename($_FILES['hotel_image']['name']);
        $targetFile = $uploadDir . $hotel_image;
        move_uploaded_file($_FILES['hotel_image']['tmp_name'], $targetFile);
    }

    $query = "INSERT INTO hotels (hotel_name, location, available_rooms, price_per_room, hotel_image)
              VALUES ('$hotel_name', '$location', '$available_rooms', '$price_per_room', '$hotel_image')";

    if (mysqli_query($con, $query)) {
        $_SESSION['hotel_msg'] = ['type' => 'success', 'text' => 'Hotel added successfully.'];
    } else {
        $_SESSION['hotel_msg'] = ['type' => 'error', 'text' => 'Error: ' . mysqli_error($con)];
    }

    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Hotel</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }

        form {
            background: white;
            width: 420px;
            margin: 30px auto;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0px 3px 15px rgba(0,0,0,0.1);
        }

        label { display: block; font-weight: bold; margin-top: 10px; color: #444; }
        input[type="text"], input[type="number"], input[type="file"] {
            width: 100%; padding: 10px; border-radius: 6px;
            border: 1px solid #ccc; margin-top: 5px; font-size: 15px;
        }

        button {
            margin-top: 20px;
            width: 100%;
            padding: 10px;
            background: #8cc78e;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: 0.3s;
        }

        button:hover { background: #7bb97e; }

        .form-container { display: flex; flex-direction: column; }
    </style>
</head>
<body>
    <h2>Add Hotel</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-container">
            <label>Hotel Name:</label>
            <input type="text" name="hotel_name" required>

            <label>Location:</label>
            <input type="text" name="location" required>

            <label>Available Rooms:</label>
            <input type="number" name="available_rooms" required>

            <label>Price per Room:</label>
            <input type="number" name="price_per_room" required>

            <label>Hotel Image:</label>
            <input type="file" name="hotel_image" accept="image/*">

            <button type="submit">Add Hotel</button>
        </div>
    </form>
</body>
</html>
