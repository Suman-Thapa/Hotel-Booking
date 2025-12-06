<?php
session_start();
include '../includes/connection.php';
include '../includes/functions.php';
include '../includes/navbar.php';

if ($_SESSION['level'] != 'hoteladmin') {
    header("Location: ../login/login.php");
    exit();
}

$hotel_admin_id = $_SESSION['user_id'];

/* --------------------------------------------------------
   GET HOTEL ID FOR THIS ADMIN
---------------------------------------------------------*/
$hotelQuery = mysqli_query($con, "SELECT hotel_id, total_rooms FROM hotels WHERE hotel_admin_id = $hotel_admin_id");
$hotelData = mysqli_fetch_assoc($hotelQuery);

if (!$hotelData) {
    die("You do not have a hotel assigned.");
}

$hotel_id = $hotelData['hotel_id'];
$total_rooms_limit = $hotelData['total_rooms'];

/* --------------------------------------------------------
   COUNT ALREADY ADDED ROOMS
---------------------------------------------------------*/
$countQuery = mysqli_query($con, "SELECT COUNT(*) AS total FROM hotel_room WHERE hotel_id = $hotel_id");
$countData = mysqli_fetch_assoc($countQuery);
$already_added = $countData['total'];

/* --------------------------------------------------------
   STOP ADDING IF LIMIT REACHED
---------------------------------------------------------*/
if ($already_added >= $total_rooms_limit) {
    die("
        <div class='edit-hotel-container'>
        <div class='edithotel'>
            <h2 style='color: red;'>Room Limit Reached</h2>
            <p>You have already added all <b>$total_rooms_limit</b> rooms for this hotel.</p>
            <a href='admin_dashboard.php' style='color: blue;'>Go Back</a>
        </div>
        </div>
    ");
}

/* --------------------------------------------------------
   FORM SUBMISSION
---------------------------------------------------------*/
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $room_number = sanitize($con, $_POST['room_number']);
    $room_type = sanitize($con, $_POST['room_type']);
    $about = sanitize($con, $_POST['description']);

    $room_image = "";

    /* UPLOAD IMAGE */
    if (!empty($_FILES['room_image']['name'])) {

        $uploadDir = "../uploads/rooms/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $room_image = basename($_FILES['room_image']['name']);
        $targetFile = $uploadDir . $room_image;

        move_uploaded_file($_FILES['room_image']['tmp_name'], $targetFile);
    }

    /* INSERT ROOM */
    $insert = "INSERT INTO hotel_room
                (hotel_id, room_number, room_type, about_rooms, room_image)
               VALUES
                ($hotel_id, '$room_number', '$room_type', '$about', '$room_image')";

    mysqli_query($con, $insert);

    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../style/formstyle.css">
    <title>Add Room</title>
</head>
<body>

<div class="edit-hotel-container">
<div class="edithotel">

    <h2>Add Room (<?= $already_added ?> / <?= $total_rooms_limit ?>)</h2>

    <form method="POST" enctype="multipart/form-data">

        <div class="form-container">

            <div>
                <label>Room Number:</label>
                <input type="text" name="room_number" required>
            </div>

            <div>
                <label>Room Type:</label>
                <select name="room_type">
                    <option value="Single">Single Bed</option>
                    <option value="Double">Double Bed</option>
                </select>
            </div>

            <div>
                <label>Room Image:</label>
                <input type="file" name="room_image" accept="image/*">
            </div>

            <div>
                <label for="description">About</label>
                <textarea name="description" id="description" placeholder="About Room"></textarea>
            </div>

            <button type="submit">Add Room</button>

        </div>
    </form>

</div>
</div>

</body>
</html>
