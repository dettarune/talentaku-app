<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- SweetAlert CSS for custom alerts -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.24/dist/sweetalert2.min.css" rel="stylesheet" />
    <style>
        /* Custom styles for the login container */
        body {
            background-color: #f0f2f5;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            margin: 100px auto;
            padding: 40px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .login-logo {
            width: 120px;
            margin-bottom: 20px;
        }

        /* Progress bar container at the top */
        .progress-bar-container {
            height: 5px;
            background-color: #f3f3f3;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .progress-bar {
            height: 100%;
            background-color: #007bff;
            width: 0%;
            border-radius: 5px;
            transition: width 0.4s ease-in-out;
        }
    </style>
</head>
<body>

<!-- Progress bar container, moved to the top of the page -->
<div class="progress-bar-container d-none" id="progressBar">
    <div class="progress-bar" id="progress"></div>
</div>

<div class="login-container">
    <div class="text-center mb-4">
        <img src="{{ asset('logo.png') }}" alt="Logo Perusahaan" class="login-logo">
    </div>
    <h2 class="text-center mb-4">Login</h2>
    <form id="loginForm">
        @csrf

        <!-- Username input -->
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required placeholder="Enter your username">
        </div>

        <!-- Password input -->
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
        </div>
        <input type="hidden" name="flagCreateSession" value="Y">

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 JS for custom alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.24/dist/sweetalert2.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        $('#loginForm').on('submit', function (e) {
            e.preventDefault();

            // Show the progress bar
            $('#progressBar').removeClass('d-none');
            $('#progress').css('width', '0%'); // Reset progress bar

            // Simulate smooth progress bar animation
            let progress = 0;
            const interval = setInterval(function () {
                if (progress >= 100) {
                    clearInterval(interval);
                } else {
                    progress += 5; // Increase progress by 5 each interval
                    $('#progress').css('width', progress + '%');
                }
            }, 100); // Interval is 100ms for a smooth experience

            var id = $("#username").val();
            var password = $("#password").val();

            // AJAX request to login API
            $.ajax({
                type  : "GET",
                url   : "{{ url('token') }}",
                data  : "",
                xhrFields: { withCredentials: true }, // Menambahkan pengiriman cookie
                success : function(data) {
                    if(data["STATUS"] == "SUCCESS") {
                        var token = data["PAYLOAD"];
                        $.ajax({
                            type  : "POST",
                            url   : "{{ url('backend/auth/login/') }}",
                            data  : {
                                "U_NAME" : id,
                                "U_PASSWORD" : password,
                                "_token": token,
                                flagCreateSession: true,
                                'admin': true
                            },
                            xhrFields: { withCredentials: true },
                            success : function(data) {
                                if(data["STATUS"] == "SUCCESS") {
                                    clearInterval(interval);
                                    $('#progress').css('width', '100%');
                                    window.setTimeout(function() {
                                        window.location = "{{ url('/backend/dashboard') }}";
                                    }, 1000);
                                } else {
                                    clearInterval(interval);
                                    $('#progress').css('width', '100%');
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Login Failed',
                                        text: data["MESSAGE"],
                                    });
                                }
                            },
                            error : function(xhr, status, error) {
                                clearInterval(interval);
                                $('#progress').css('width', '100%');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Something went wrong, please try again.',
                                });
                            }
                        });
                    } else {
                        clearInterval(interval);
                        $('#progress').css('width', '100%');
                        Swal.fire({
                            icon: 'error',
                            title: 'Token Error',
                            text: data["MESSAGE"],
                        });
                    }
                },
                error : function(xhr, status, error) {
                    clearInterval(interval);
                    $('#progress').css('width', '100%');
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'Unable to reach the server.',
                    });
                }
            });

        });
    });
</script>

</body>
</html>
