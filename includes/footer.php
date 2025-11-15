<footer class="footer-full">
    <div class="footer-inner">

        <div class="footer-section">
            <strong>&copy; <?php echo date("Y"); ?> All rights reserved by Suman and Samriddha</strong>
        </div>

        <div class="footer-section contact-clean">
            <strong>Contact Us</strong><br>

            <div class="contact-line">
                <span class="icon">📧</span>
                <strong>Email:</strong>
                <a href="mailto:Suman123@gmail.com">Suman123@gmail.com</a> |
                <a href="mailto:Samriddha123@gmail.com">Samriddha123@gmail.com</a>
            </div>

            <div class="contact-line">
                <span class="icon">📞</span>
                <strong>Phone:</strong>
                9810000000 (Suman) | 9820000000 (Samriddha)
            </div>
        </div>

        <div class="footer-section">
            <strong>We accept payment via:</strong>
            <div class="payment-icons">
                <img src="/Hotel-Booking/uploads/esewa.png" alt="eSewa" class="payment-logo">
                <img src="/Hotel-Booking/uploads/khalti.png" alt="Khalti" class="payment-logo">
            </div>
        </div>

    </div>
</footer>

<style>
    html, body {
    height: 100%;
    margin: 0;
}

.wrapper {
    min-height: 100%;
    display: flex;
    flex-direction: column;
}

.content {
    flex: 1; 
}


.footer-full {
    width: 100%;
    background-color: #4CAF50;
}

.footer-inner {
    text-align: center;
    color: #000;
    padding: 30px 10px;
    font-family: Arial, sans-serif;
}

/* Footer sections */
.footer-section {
    margin-bottom: 20px;
    font-size: 18px;
    line-height: 1.6;
}


.contact-clean {
    font-size: 18px;
    line-height: 1.8;
    margin-top: 10px;
}

.contact-line {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin: 3px 0;
}

.contact-line .icon {
    font-size: 20px;
}

.contact-clean a {
    color: #000;
    text-decoration: underline;
    font-weight: bold;
}

.contact-line strong {
    margin-right: 5px;
}

.payment-icons {
    margin-top: 10px;
}

.payment-logo {
    height: 45px;
    margin: 0 12px;
    transition: transform 0.25s;
    vertical-align: middle;
}

.payment-logo:hover {
    transform: scale(1.12);
}

@media (max-width: 600px) {
    .footer-section {
        font-size: 16px;
    }
    .contact-line {
        font-size: 16px;
    }
    .payment-logo {
        height: 38px;
    }
}

</style>
