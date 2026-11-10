<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Login</title>
    <link rel="icon" href="{{ asset('assets/img/markup.png') }}" type="image/png">

    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<style>
    :root {
        --primary: #E2E689;
        --primary-dark: #bfcf5b;
        --secondary: #ec4899;
        --light: #ffffff;
        --light-gray: #f8fafc;
        --medium-gray: #e2e8f0;
        --dark-gray: #64748b;
        --text: #334155;
        --success: #10b981;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #f0f4ff 0%, #E2E689 100%);
        /* min-height: 100vh; */
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    body::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 90%;
        height: 90%;
        background: radial-gradient(circle, #f0f4ff, #E2E689);
        z-index: -1;
        animation: rotate 30s linear infinite;
    }

    @keyframes rotate {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .login-container {
        width: 100%;
        max-width: 420px;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(15px);
        border-radius: 20px;
        padding: 50px 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        position: relative;
        overflow: hidden;
        border: 1px solid #E2E689;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .login-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
    }

    .login-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 6px;
        background: linear-gradient(90deg, #E2E689, #bfcf5b);
    }

    .logo-container {
        text-align: center;
        margin-bottom: 35px;
    }

    .logo {
        width: 90px;
        height: 90px;
        margin: 0 auto 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #E2E689, #bfcf5b);
        border-radius: 50%;
        color: white;
        font-size: 36px;
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
    }

    .logo i {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.15); }
        100% { transform: scale(1); }
    }

    h2 {
        color: var(--text);
        font-weight: 700;
        font-size: 26px;
        margin-bottom: 5px;
        text-align: center;
    }

    .subtitle {
        color: var(--dark-gray);
        font-size: 14px;
        text-align: center;
        margin-bottom: 35px;
    }

    .form-group {
        margin-bottom: 22px;
        position: relative;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--text);
        font-weight: 500;
        font-size: 14px;
    }

    .input-field {
        position: relative;
    }

    .input-field input {
        width: 100%;
        padding: 14px 20px 14px 45px;
        border: 1px solid var(--medium-gray);
        border-radius: 12px;
        font-size: 14px;
        color: var(--text);
        background-color: var(--light-gray);
        transition: all 0.3s ease;
    }

    .input-field input::placeholder {
        color: var(--dark-gray);
    }

    .input-field input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(226, 230, 137, 0.15);
        outline: none;
        background-color: white;
    }

    .input-field i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--dark-gray);
        font-size: 16px;
        transition: color 0.3s ease;
    }

    .options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
        font-size: 13px;
    }

    .remember-me {
        display: flex;
        align-items: center;
    }

    .remember-me input {
        margin-right: 8px;
        accent-color: var(--primary);
    }

    .remember-me label {
        color: var(--text);
        margin-bottom: 0;
        cursor: pointer;
    }

    .forgot-password a {
        color: var(--primary);
        text-decoration: none;
        transition: color 0.3s ease;
        font-weight: 500;
    }

    .forgot-password a:hover {
        color: var(--primary-dark);
        text-decoration: underline;
    }

    .login-btn {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, #E2E689, #bfcf5b);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
    }

    .login-btn:hover {
        background: linear-gradient(135deg, #bfcf5b, #E2E689);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(79, 70, 229, 0.4);
    }

    .login-btn:active {
        transform: translateY(0);
    }

    .footer {
        text-align: center;
        margin-top: 30px;
        font-size: 12px;
        color: var(--dark-gray);
    }

    .footer a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
    }

    .footer a:hover {
        text-decoration: underline;
    }

    .alert-danger {
        background-color: rgba(254, 226, 226, 0.7);
        color: #dc2626;
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
        border-left: 4px solid #dc2626;
    }

    .alert-danger ul {
        margin-bottom: 0;
        padding-left: 20px;
    }

    .floating {
        position: absolute;
        border-radius: 50%;
        z-index: -1;
        background: linear-gradient(135deg, rgba(226, 230, 137, 0.1), rgba(191, 207, 91, 0.1));
        opacity: 0.5;
    }

    .floating:nth-child(1) {
        width: 180px;
        height: 180px;
        top: -70px;
        right: -60px;
        animation: float 8s ease-in-out infinite;
    }

    .floating:nth-child(2) {
        width: 120px;
        height: 120px;
        bottom: 30px;
        left: -40px;
        animation: float 10s ease-in-out infinite 2s;
    }

    @keyframes float {
        0% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-25px) rotate(10deg); }
        100% { transform: translateY(0) rotate(0deg); }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .login-container {
            max-width: 90%;
            padding: 40px 30px;
        }

        h2 {
            font-size: 24px;
        }

        .subtitle {
            font-size: 13px;
            margin-bottom: 25px;
        }

        .login-btn {
            padding: 14px;
            font-size: 15px;
        }
    }

    @media (max-width: 480px) {
        .login-container {
            padding: 30px 20px;
            border-radius: 15px;
        }

        h2 {
            font-size: 22px;
        }

        .subtitle {
            font-size: 12px;
            margin-bottom: 20px;
        }

        .options {
            flex-direction: column;
            gap: 12px;
            align-items: flex-start;
        }

        .forgot-password {
            align-self: flex-end;
        }

        .input-field input {
            padding: 12px 18px 12px 40px;
            font-size: 13px;
        }

        .login-btn {
            font-size: 14px;
            padding: 12px;
        }
    }

    @media (max-width: 360px) {
        .login-container {
            padding: 25px 15px;
        }

        .logo {
            width: 70px;
            height: 70px;
            font-size: 28px;
        }

        h2 {
            font-size: 20px;
        }

        .subtitle {
            font-size: 11px;
        }
    }
</style>

</head>

<body>
    <!-- Decorative floating elements -->
    <div class="floating"></div>
    <div class="floating"></div>

    <div class="login-container">
        <div class="logo-container">
            <div class="logo">
                <i class="fas fa-lock"></i>
            </div>
            <h2>Admin Portal</h2>
            <p class="subtitle">Sign in to access your dashboard</p>
        </div>

        <!-- Validation Errors -->
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{route('admin.login.post')}}" method="post">
            @csrf
            <div class="form-group">
                <label for="loginEmail">Email Address</label>
                <div class="input-field">
                    <i class="fas fa-envelope"></i>
                    <input id="loginEmail" type="email" name="email" placeholder="your@email.com" required value="{{ old('email') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="loginPassword">Password</label>
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input id="loginPassword" type="password" name="password" placeholder="••••••••" required>
                </div>
            </div>

            <div class="options">
                <div class="remember-me">
                    <input type="checkbox" name="remember_me" id="flexCheckDefault">
                    <label for="flexCheckDefault">Remember me</label>
                </div>
                 <div class="forgot-password">
                    <a href="{{route('admin.forgot-password')}}">Forgot password?</a>
                </div>
            </div>

            <button type="submit" class="login-btn">SIGN IN</button>
        </form>

        <div class="footer">
            <p>© 2026 Admin Portal. All rights reserved. <br> Powered by <a href="https://www.markupdesigns.com/" target="_blank">Markup Design</a></p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        @if (session()->has('message'))
            var type = "{{ session('alert-type', 'success') }}";
            var message = {!! json_encode(session('message')) !!};

            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-bottom-right",
                timeOut: 5000
            };

            if (type === 'success') {
                toastr.success(message);
            } else if (type === 'error') {
                toastr.error(message);
            } else if (type === 'info') {
                toastr.info(message);
            } else if (type === 'warning') {
                toastr.warning(message);
            }
        @endif

        // Input focus animation
        document.querySelectorAll('.input-field input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.querySelector('i').style.color = '#4f46e5';
            });

            input.addEventListener('blur', function() {
                this.parentElement.querySelector('i').style.color = '#64748b';
            });
        });
    </script>

</body>

</html>