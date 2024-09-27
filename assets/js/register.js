document.addEventListener('DOMContentLoaded', function () {
    const registerForm = document.getElementById('registerForm');

    if (registerForm) {
        registerForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const email = document.getElementById('registerEmail').value;
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('registerConfirmPassword').value;
            if (email === '' || password === '' || confirmPassword === '') {
                showMessage('Please fill in all fields.', 'error');
                return;
            }

            if (password !== confirmPassword) {
                showMessage('Passwords do not match.', 'error');
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open('POST', todolistAjax.ajaxurl, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            const data = `action=todolist_register_user&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}&confirm_password=${encodeURIComponent(confirmPassword)}&nonce=${encodeURIComponent(todolistAjax.nonce)}`;

            xhr.onload = function () {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);

                    if (response.success) {
                        showMessage('Registration successful! Redirecting you to the login page...', 'success');
                        
                        setTimeout(function () {
                            window.location.href = 'index.php/login';
                        }, 2000);
                    } else {
                        showMessage(response.data.message, 'error');
                    }
                } else {
                    showMessage('An error occurred. Please try again later.', 'error');
                }
            };

            xhr.onerror = function () {
                console.error('AJAX Error:', xhr.statusText);
                showMessage('Oops! Something went wrong. Please try again later.', 'error');
            };

            xhr.send(data);
        });
    }

    function showMessage(message, type) {
        const messageElement = document.getElementById('registerMessage');
        if (messageElement) {
            messageElement.innerText = message;
            messageElement.className = `message ${type}`; 
            messageElement.style.display = 'block'; 
        }
    }
});
