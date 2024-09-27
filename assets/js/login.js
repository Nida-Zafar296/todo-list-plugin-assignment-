jQuery(document).ready(function($) {
    $('#loginForm').submit(function(event) {
        event.preventDefault(); 

        var email = $('#loginEmail').val();
        var password = $('#loginPassword').val();

        $.ajax({
            url: todolistAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'todolist_login_user',
                email: email,
                password: password,
                nonce: todolistAjax.nonce 
            },
            success: function(response) {
                if (response.success) {
                    $('#loginMessage').text('Login successful! Redirecting you to your to-do list...');
                    setTimeout(function() {
                        window.location.href = 'index.php/to-do-list/';
                    }, 2000); 
                } else {
                    $('#loginMessage').text('Invalid credentials. Please check your email and password and try again.');
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', error);
                $('#loginMessage').text('Oops! Something went wrong. Please try again later.');
            }
        });
    });
});




