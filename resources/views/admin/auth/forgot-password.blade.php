<!DOCTYPE html>
<html lang="en"> <!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>AdminLTE 4 | Lockscreen</title><!--begin::Primary Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="title" content="AdminLTE 4 | Lockscreen">
    <meta name="author" content="ColorlibHQ">
    <meta name="description" content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS.">
    <meta name="keywords" content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard"><!--end::Primary Meta Tags--><!--begin::Fonts-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous"><!--end::Fonts--><!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css" integrity="sha256-dSokZseQNT08wYEWiz5iLI8QPlKxG+TswNRD8k35cpg=" crossorigin="anonymous"><!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css" integrity="sha256-Qsx5lrStHZyR9REqhUF8iQt73X06c8LGIUPzpOhwRrI=" crossorigin="anonymous"><!--end::Third Party Plugin(Bootstrap Icons)--><!--begin::Required Plugin(AdminLTE)-->
<style>
    :root {
        --primary: #f6fab1;
        --primary-dark: #bfcf5b;
        --secondary: #ec4899;
        --light: #ffffff;
        --light-gray: #f8fafc;
        --medium-gray: #e2e8f0;
        --dark-gray: #64748b;
        --text: #334155;
        --success: #10b981;
        --error: #ef4444;
    }

    body.lockscreen {
        background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
        font-family: 'Source Sans 3', sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .lockscreen-wrapper {
        background: var(--light);
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        width: 360px;
    }

    .lockscreen-logo a {
        color: var(--primary-dark);
        font-weight: 700;
        font-size: 1.5rem;
        text-decoration: none;
        display: block;
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .lockscreen-item {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        border-radius: 8px;
        gap:30px;
        border: 1px solid var(--medium-gray);
        padding: 0.5rem;
        background-color: var(--light-gray);
    }

    .lockscreen-image img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 2px solid var(--primary-dark);
        object-fit: cover;
    }

    .lockscreen-credentials .input-group {
        flex: 1;
        gap: 0.5rem;
        display: flex;
    }

    .lockscreen-credentials .form-control {
        border: 2px solid var(--primary-dark);
        border-radius: 5px;
        padding: 0.5rem 0.75rem;
        font-size: 1rem;
        outline: none;
        transition: all 0.3s ease;
    }

    .lockscreen-credentials .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 5px var(--primary);
    }

    .lockscreen-credentials .btn {
        background-color: var(--primary-dark);
        color: var(--light);
        border-radius: 5px;
        padding: 0.5rem 0.75rem;
        border: none;
        transition: background 0.3s ease;
    }

    .lockscreen-credentials .btn:hover {
        background-color: var(--primary);
        color: var(--dark-gray);
    }

    .help-block {
        text-align: center;
        font-size: 0.9rem;
        color: var(--text);
    }

    .help-block .error {
        color: var(--error);
    }

    .help-block .success {
        color: var(--success);
    }

    .otp-input-group {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }

    .otp-input {
        width: 20px;
        height: 20px;
        font-size: 20px;
        text-align: center;
        border: 2px solid var(--primary-dark);
        border-radius: 5px;
        transition: all 0.2s ease;
        background-color: var(--light-gray);
    }

    .otp-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 5px var(--primary);
    }

    .text-center a {
        color: var(--primary-dark);
        text-decoration: none;
        font-weight: 500;
    }

    .text-center a:hover {
        color: var(--primary);
        text-decoration: underline;
    }

    .lockscreen-footer {
        text-align: center;
        font-size: 0.8rem;
        color: var(--dark-gray);
        margin-top: 1.5rem;
    }
</style>

</head> <!--end::Head--> <!--begin::Body-->

<body class="lockscreen bg-body-secondary">
    <div class="lockscreen-wrapper">
        <div class="lockscreen-logo"> <a href="#"><b>Admin - Forgot Password</b></a> </div>
        <div id="forgot-password-container" class="">
            <div class="" id="sendOtpSection">
                <div class="lockscreen-name">Email</div>
                <div class="lockscreen-item" >
                    <div class="lockscreen-image"> <img src="{{asset('assets/admin/images/admin.jpg')}}" alt="User Image"> </div>
                    <form class="lockscreen-credentials">
                        <div class="input-group"> <input type="email" class="form-control shadow-none" id="email" name="email" placeholder="Email">
                            <div class="input-group-text border-0 bg-transparent px-1">
                                <button type="button" class="btn shadow-none d-flex align-items-center" id="sendOtpBtn">
                                    <i class="bi bi-box-arrow-right text-body-secondary" id="sendOtpIcon"></i>
                                    <span id="sendOtpLoader" class="spinner-border spinner-border-sm text-body-secondary ms-2" role="status" style="display: none;"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="help-block text-center">
                    Enter your email to send otp.
                </div>

            </div>

            <div class="hidden" id="verifyOtpSection">
                <div class="lockscreen-name">Verify OTP</div>
                <div class="lockscreen-item " >
                    <div class="lockscreen-image"> <img src="{{asset('assets/admin/images/AdminLTElogo.png')}}" alt="User Image"> </div>
                    <form class="lockscreen-credentials">
                        <div class="input-group"> 
                            <!-- <input type="text" class="form-control shadow-none" id="otp" name="otp" placeholder="OTP"> -->
                            <input type="text" class="otp-input form-control shadow-none" maxlength="1" id="otp1">
                            <input type="text" class="otp-input form-control shadow-none" maxlength="1" id="otp2">
                            <input type="text" class="otp-input form-control shadow-none" maxlength="1" id="otp3">
                            <input type="text" class="otp-input form-control shadow-none" maxlength="1" id="otp4">
                            <div class="input-group-text border-0 bg-transparent px-1"> <button type="button" class="btn shadow-none" id="verifyOtpBtn"> <i class="bi bi-box-arrow-right text-body-secondary"></i> </button> </div>
                        </div>
                    </form>
                </div>
                
                <div class="help-block text-center">
                    Enter OTP received on your email.
                    <div id="responseMessage"></div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4"> <a href="{{route('admin.login')}}" class="text-decoration-none">Or sign in here</a> </div>
        <div class="lockscreen-footer text-center">
            Copyright © {{date('Y')}} &nbsp;
            <br>
            All rights reserved
        </div>
    </div> <!--begin::Third Party Plugin(OverlayScrollbars)-->
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>
        const forgotPasswordContainer = document.getElementById('forgot-password-container');

        function setSendOtpLoading(isLoading){
            const $btn = $('#sendOtpBtn');
            if(isLoading){
                $('#sendOtpIcon').hide();
                $('#sendOtpLoader').show();
                $btn.prop('disabled', true).addClass('disabled');
            } else {
                $('#sendOtpLoader').hide();
                $('#sendOtpIcon').show();
                $btn.prop('disabled', false).removeClass('disabled');
            }
        }

        $('#sendOtpBtn').click(function () {
            const email = $('#email').val();
            if (!email) {
                $('#responseMessage').html('<p class="error">Please enter your email.</p>');
                return;
            }

            // show loader and disable button
            setSendOtpLoading(true);
            $('#responseMessage').html(''); // clear previous messages

            $.ajax({
                url: "{{route('admin.send.otp')}}",
                type: 'POST',
                data: { email: email },
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                },
                success: function (response) {
                    // hide loader and enable button
                    setSendOtpLoading(false);

                    if (response.success) {
                        $('#responseMessage').html('<p class="success">' + response.message + '</p>');
                        $('#sendOtpSection').addClass('hidden');
                        $('#verifyOtpSection').removeClass('hidden');
                    } else {
                        $('#responseMessage').html('<p class="error">' + response.message + '</p>');
                    }
                },
                error: function (xhr) {
                    // hide loader and enable button
                    setSendOtpLoading(false);

                    let msg = 'An error occurred. Please try again.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    $('#responseMessage').html('<p class="error">Error: ' + msg + '</p>');
                }
            });
        });

        $('.otp-input').on('input', function () {
            let $this = $(this);
            let val = $this.val();

            // Move to the next input if the current one is filled
            if (val.length === 1) {
                $this.next('.otp-input').focus();
            }

            // Handle backspace
            if (val.length === 0) {
                $this.prev('.otp-input').focus();
            }
        });

        // Verify OTP button click
        $('#verifyOtpBtn').click(function () {
            const email = $('#email').val();

            const otp = 
                $('#otp1').val() + 
                $('#otp2').val() + 
                $('#otp3').val() + 
                $('#otp4').val();

            if (otp.length !== 4) {
                $('#responseMessage').html('<p class="error">Please enter a valid 4-digit OTP.</p>');
                return;
            }

            $.ajax({
                url: "{{route('admin.verify.otp')}}",
                type: 'POST',
                data: { email: email, otp: otp },
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                },
                success: function (response) {
                    if (response.success) {
                        // verifyOtpForm.style.display = 'none';
                        $('#responseMessage').html('<p class="success">' + response.message + '</p>');
                        forgotPasswordContainer.innerHTML = response.reset_password_html;
                    } else {
                        $('#responseMessage').html('<p class="error">' + response.message + '</p>');
                    }
                },
                error: function (xhr) {
                    $('#responseMessage').html('<p class="error">Error: ' + xhr.responseJSON.message + '</p>');
                }
            });
        });

        $(document).on('submit', '#resetPasswordForm', function (e) {
            e.preventDefault();

            const form = $(this);
            const actionUrl = form.attr('action');
            const formData = form.serialize();

            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                },
                success: function (response) {
                    if (response.success) {
                        alert('Password reset successfully!');
                        window.location.href = "{{route('admin.login')}}";
                    } else {
                        alert(response.message || 'Failed to reset password.');
                    }
                },
                error: function (xhr) {
                    console.error('Error:', xhr.responseText);
                    alert(xhr.responseJSON.message);
                },
            });
        });

    </script>
    
</body><!--end::Body-->

</html>