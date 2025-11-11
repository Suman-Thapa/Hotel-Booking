<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../includes/connection.php';
include '../includes/navbar.php';

if (!isset($_SESSION['user_id'])) {
    die("You must <a href='../login/login.php'>login</a> to view the invoice.");
}

if (!isset($_GET['booking_id'])) {
    die("Booking ID not provided!");
}

$booking_id = (int) $_GET['booking_id'];
$user_id = $_SESSION['user_id'];

$query = "SELECT b.*, u.name, u.email, h.hotel_name, h.location, h.price_per_room, h.hotel_image,
                 p.payment_status, p.amount
          FROM bookings b
          JOIN users u ON b.user_id = u.user_id
          JOIN hotels h ON b.hotel_id = h.hotel_id
          JOIN payments p ON b.booking_id = p.booking_id
          WHERE b.booking_id = ? AND b.user_id = ?";

$stmt = $con->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $con->error);
}

$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {

    $check_in = new DateTime($row['check_in']);
    $check_out = new DateTime($row['check_out']);
    $nights = $check_in->diff($check_out)->days;
    $total_price = $row['rooms_booked'] * $row['price_per_room'] * $nights;

    $imagePath = "../uploads/hotels/" . $row['hotel_image'];
    if (empty($row['hotel_image']) || !file_exists($imagePath)) {
        $imagePath = "https://via.placeholder.com/400x300?text=No+Image";
    }
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
                <b>Total Price:</b> Rs. <?= number_format($total_price, 2); ?><br>
                <b>Booking Status:</b> <?= ucfirst($row['status']); ?><br>
                <b>Payment Status:</b>
                <span style="color:<?= ($row['payment_status'] == 'paid') ? 'green' : 'red'; ?>;">
                    <?= ucfirst($row['payment_status']); ?>
                </span>
            </p>

            <?php if ($row['payment_status'] != 'paid') : ?>
                <button id="pay-with-khalti"
                        style="background-color:#5a2a82;color:white;padding:10px 20px;border:none;border-radius:5px;cursor:pointer;">
                    Pay with Khalti
                </button>
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
    echo "Invoice not found.";
}
?>

<script src="https://khalti.com/static/khalti-checkout.js"></script>
<script>
<?php if ($row && $row['payment_status'] != 'paid') : ?>
const config = {
    publicKey: "test_public_key_dc74a8c3cf944c10b2b7c9cbd95f41be",
    productIdentity: "<?= $row['booking_id']; ?>",
    productName: "<?= addslashes($row['hotel_name']); ?>",
    productUrl: "http://localhost/Hotel%20Booking/user/invoice.php?booking_id=<?= $row['booking_id']; ?>",
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
