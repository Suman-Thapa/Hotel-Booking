<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'navbar.php';
include 'connection.php';
include 'functions.php';

$search = '';
if (isset($_GET['search'])) {
    $search = sanitize($con, $_GET['search']);
}
?>


<!-- Search form -->
<form method="GET" action="">
    <input type="text" name="search" placeholder="Search by hotel name or location" 
    value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>

<hr>

<?php
// Show all hotels or filter by search
$query = "SELECT * FROM hotels WHERE available_rooms > 0";
if (!empty($search)) {
    $search_safe = mysqli_real_escape_string($con, $search);
    $query .= " AND (hotel_name LIKE '%$search_safe%' OR location LIKE '%$search_safe%')";
}

$result = mysqli_query($con, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='hotel-card'>";

        // Hotel image
        // Hotel image
    $imagePath = "uploads/hotels/" . $row['hotel_image'];

    if (!empty($row['hotel_image']) && file_exists($imagePath)) {
        echo "<div class='hotel-image'>
            <a href='$imagePath' target='_blank'>
                <img src='$imagePath' alt='Hotel Image'>
            </a>
        </div>";
} 

    else {
        echo "<div class='hotel-image'>
            <img src='https://via.placeholder.com/150x100?text=No+Image' alt='No Image'>
        </div>";
    }


        // Hotel details
        echo "<div class='hotel-details'>";
        echo "<b>" . $row['hotel_name'] . "</b> (" . $row['location'] . ")<br>";
        echo "Available Rooms: " . $row['available_rooms'] . "<br>";
        echo "Price per Room: $" . $row['price_per_room'] . "<br>";

        // Booking form only if rooms available
        if ($row['available_rooms'] > 0) {
            $maxRooms = $row['available_rooms'] < 5 ? $row['available_rooms'] : 5;
            echo "<form class='bookingForm' method='POST' action='book_process.php'>
                    <input type='hidden' name='hotel_id' value='" . $row['hotel_id'] . "'>
                    Rooms to book: <input type='number' name='rooms' min='1' max='$maxRooms' required><br>
                    Check-in: <input type='date' name='check_in' required><br>
                    Check-out: <input type='date' name='check_out' required><br>
                    <button type='submit'>Book Now</button>
                  </form>";
        } else {
            echo "<b class='full'>Rooms Full</b>";
        }

        echo "</div>"; // hotel-details
        echo "</div>"; // hotel-card
    }
} else {
    echo "<p>No hotels available at the moment.</p>";
}
?>

<style>
/* Hotel card styles */
.hotel-card {
    border: 1px solid #c8e6c9;
    background-color: #f0fdf4;
    padding: 15px;
    margin: 15px 0;
    display: flex;
    border-radius: 8px;
}

.hotel-image img {
    width: 150px;
    height: 100px;
    object-fit: cover;
    border-radius: 6px;
    margin-right: 20px;
}

.hotel-details {
    flex: 1;
    font-size: 14px;
    color: #1b5e20;
}

.hotel-details b {
    font-size: 16px;
    color: #2e7d32;
}

.bookingForm input[type="number"],
.bookingForm input[type="date"] {
    padding: 5px;
    margin: 5px 0;
    border-radius: 5px;
    border: 1px solid #a5d6a7;
}

.bookingForm button {
    padding: 8px 12px;
    background-color: #2e7d32;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 5px;
    font-weight: bold;
}

.bookingForm button:hover {
    background-color: #1b5e20;
}

.full {
    color: red;
    font-weight: bold;
}
/* Search form styling */
form[method="GET"] {
    width: 350px;
    /* top | right | bottom | left */
    padding: 20px 10px 8px 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

form[method="GET"] input[type="text"] {
    flex: 1;
    padding: 8px 10px;
    border: 1px solid #a5d6a7;
    border-radius: 6px;
    font-size: 14px;
    outline: none;
    transition: 0.3s;
}

form[method="GET"] input[type="text"]:focus {
    border-color: #2e7d32;
    box-shadow: 0 0 4px rgba(46,125,50,0.5);
}

form[method="GET"] button {
    padding: 8px 15px;
    background-color: #2e7d32;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    transition: 0.3s;
}

form[method="GET"] button:hover {
    background-color: #1b5e20;
}

</style>

<script>
const today = new Date().toISOString().split('T')[0];
const forms = document.querySelectorAll('.bookingForm');

forms.forEach(form => {
    const checkIn = form.querySelector('input[name="check_in"]');
    const checkOut = form.querySelector('input[name="check_out"]');
    const roomsInput = form.querySelector('input[name="rooms"]');

    // Set minimum dates
    checkIn.setAttribute('min', today);
    checkOut.setAttribute('min', today);

    // Update check-out min dynamically
    checkIn.addEventListener('change', function() {
        checkOut.setAttribute('min', this.value);
    });

    // Form submission validation
    form.addEventListener('submit', function(e) {
        const rooms = parseInt(roomsInput.value);
        const maxRooms = parseInt(roomsInput.getAttribute('max'));

        if (rooms < 1 || rooms > maxRooms) {
            e.preventDefault();
            alert("You can book between 1 and " + maxRooms + " rooms per booking.");
            return;
        }

        const checkInDate = new Date(checkIn.value);
        const checkOutDate = new Date(checkOut.value);

        if (checkInDate < new Date(today)) {
            e.preventDefault();
            alert("Check-in date cannot be in the past.");
            return;
        }

        if (checkOutDate <= checkInDate) {
            e.preventDefault();
            alert("Check-out date must be after check-in date.");
            return;
        }
    });
});
</script>
