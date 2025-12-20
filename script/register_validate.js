document.getElementById('registerForm').addEventListener('submit', function(event) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const errorDiv = document.getElementById('error');

    errorDiv.textContent = '';

    if(password !== confirmPassword) {
        errorDiv.textContent = 'Passwords do not match!';
        event.preventDefault();
        return;
    }

    const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/;
    if(!passwordPattern.test(password)) {
        errorDiv.textContent = 'Password must be at least 6 characters long and include at least one number and one letter.';
        event.preventDefault();
        return;
    }
});