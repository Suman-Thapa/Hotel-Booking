<?php
session_start();
include '../includes/connection.php';
include '../includes/navbar.php';

if (!isset($_SESSION['user_id'])) {
    die("You must <a href='../login/login.php'>login</a> to view the invoice.");
}

if (!isset($_GET['booking_id'])) {
    die("Booking ID not provided!");
}

$booking_id = (int) $_GET['booking_id'];
$user_id = (int) $_SESSION['user_id'];

// ✅ Fetch booking + user + hotel + payment info
$query = "
    SELECT b.*, u.name, u.email, h.hotel_name, h.location, h.price_per_room, h.hotel_image,
           p.payment_status, p.amount
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN hotels h ON b.hotel_id = h.hotel_id
    JOIN payments p ON b.booking_id = p.booking_id
    WHERE b.booking_id = $booking_id AND b.user_id = $user_id
";

$result = mysqli_query($con, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

if ($row = mysqli_fetch_assoc($result)) {

    $check_in = new DateTime($row['check_in']);
    $check_out = new DateTime($row['check_out']);
    $nights = $check_in->diff($check_out)->days ?: 1;
    $total_price = $row['rooms_booked'] * $row['price_per_room'] * $nights;

    $imagePath = "../uploads/hotels/" . $row['hotel_image'];
    if (empty($row['hotel_image']) || !file_exists($imagePath)) {
        $imagePath = "https://via.placeholder.com/400x300?text=No+Image";
    }

    // ✅ eSewa sandbox credentials
    $epay_url = "../payment/payment_esewa.php"; // Redirect to your eSewa process file
?>
    <div style="width:800px; margin:auto; border:1px solid #333; padding:20px; font-family:Arial; display:flex;">
        <div style="flex:2; padding-right:20px;">
            <h2 style="text-align:center;">Hotel Booking Invoice</h2>
            <hr>
            <p><b>Invoice No:</b> <?= htmlspecialchars($row['booking_id']); ?></p>
            <p><b>Date:</b> <?= date("Y-m-d"); ?></p>

            <h3>Customer Details</h3>
            <p><b>Name:</b> <?= htmlspecialchars($row['name']); ?><br>
               <b>Email:</b> <?= htmlspecialchars($row['email']); ?></p>

            <h3>Hotel Details</h3>
            <p><b>Hotel:</b> <?= htmlspecialchars($row['hotel_name']); ?><br>
               <b>Location:</b> <?= htmlspecialchars($row['location']); ?></p>

            <h3>Booking Details</h3>
            <p>
                <b>Check-in:</b> <?= htmlspecialchars($row['check_in']); ?><br>
                <b>Check-out:</b> <?= htmlspecialchars($row['check_out']); ?><br>
                <b>Nights:</b> <?= $nights; ?><br>
                <b>Rooms:</b> <?= htmlspecialchars($row['rooms_booked']); ?><br>
                <b>Price Per Room:</b> Rs. <?= htmlspecialchars($row['price_per_room']); ?><br>
                <b>Total Price:</b> Rs. <?= number_format($total_price, 2); ?><br>
                <b>Booking Status:</b> <?= ucfirst($row['status']); ?><br>
                <b>Payment Status:</b>
                <span style="color:<?= ($row['payment_status'] == 'paid') ? 'green' : 'red'; ?>;">
                    <?= ucfirst($row['payment_status']); ?>
                </span>
            </p>

            <?php if ($row['payment_status'] != 'paid') : ?>
                <!-- ✅ Payment Buttons -->
                <div style="display:flex; gap:10px; margin-top:15px;">
                    <button id="pay-with-khalti"
                        style="background-color:#5a2a82;color:white;padding:10px 20px;border:none;border-radius:5px;cursor:pointer;">
                        Pay with Khalti
                    </button>

                    <form action="<?= $epay_url; ?>" method="POST">
                        <input type="hidden" name="booking_id" value="<?= $row['booking_id']; ?>">
                        <input type="hidden" name="total_amount" value="<?= $total_price; ?>">
                        <button type="submit"
                            style="background-color:#60BB46;color:white;padding:10px 20px;border:none;border-radius:5px;cursor:pointer;">
                            Pay with eSewa
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <div style="flex:1; text-align:center; margin-top:50px; margin-right:50px;">
            <img src="<?= $imagePath; ?>" alt="Hotel Image"
            style="width:100%; max-height:300px; object-fit:cover; border:1px solid #ccc;">
        </div>
    </div>

    <div style="width:800px; margin:auto; text-align:center; margin-top:20px;">
        <hr>
        <p>Thank you for booking with us!</p>
        <button onclick="window.print()">🖨️ Print Invoice</button>
    </div>

<?php
} else {
    echo "<p style='text-align:center;'>Invoice not found.</p>";
}
?>

<!-- ✅ Khalti Integration -->
<script src="https://khalti.com/static/khalti-checkout.js"></script>
<script>
<?php if ($row && $row['payment_status'] != 'paid') : ?>
const config = {
    publicKey: "test_public_key_dc74a8c3cf944c10b2b7c9cbd95f41be",
    productIdentity: "<?= $row['booking_id']; ?>",
    productName: "<?= addslashes($row['hotel_name']); ?>",
    productUrl: "http://localhost/Hotel-Booking/user/invoice.php?booking_id=<?= $row['booking_id']; ?>",
    eventHandler: {
        onSuccess(payload) {
            console.log("✅ Payment Success:", payload);
            fetch("verify_khalti.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    token: payload.token,
                    amount: payload.amount,
                    booking_id: "<?= $row['booking_id']; ?>"
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("✅ Payment successful!");
                    location.reload();
                } else {
                    alert("❌ Payment verification failed: " + data.message);
                }
            })
            .catch(err => alert("❌ Verification error: " + err));
        },
        onError(error) {
            console.error("❌ Khalti Error:", error);
            alert("Payment failed. Check console for details.");
        },
        onClose() {
            console.log("Khalti popup closed");
        }
    }
};

const checkout = new KhaltiCheckout(config);
document.getElementById("pay-with-khalti").addEventListener("click", function() {
    checkout.show({ amount: <?= (int)$total_price * 100; ?> });
});
<?php endif; ?>
</script>
