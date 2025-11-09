<?php
echo "<h2>❌ Payment Failed or Cancelled</h2>";
if (!empty($_POST)) {
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
}
?>
