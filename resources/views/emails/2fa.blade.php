<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OTP Verification</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #f0f4ff 0%, #fdf2f8 100%);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .otp-card {
      max-width: 400px;
      width: 100%;
      border-radius: 20px;
      background: #ffffff;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
      padding: 35px;
    }
    .otp-input {
      letter-spacing: 10px;
      text-align: center;
      font-size: 24px;
      font-weight: bold;
      border-radius: 12px;
      padding: 15px;
      border: 2px solid #d6d3f0;
      background: #fafafa;
    }
    .btn-custom {
      border-radius: 30px;
      font-weight: bold;
      padding: 12px;
      background: linear-gradient(135deg, #ec4899, #4f46e5);
      border: none;
      color: #fff;
      transition: 0.3s;
    }
    .btn-custom:hover {
      opacity: 0.95;
      transform: translateY(-2px);
    }
    .resend-link {
      text-decoration: none;
      font-size: 14px;
    }
    .text-gradient {
      background: linear-gradient(135deg, #ec4899, #4f46e5);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-weight: bold;
    }
  </style>
</head>
<body>
  @if (session('info'))
    <div class="alert alert-success position-absolute top-0 start-50 translate-middle-x mt-3 w-50 text-center">
      {{ session('info') }}
    </div>
  @endif
  <div class="otp-card text-center">
    <div class="mb-3">
      <img src="https://img.icons8.com/ios-filled/100/ec4899/lock.png" alt="Lock" width="60">
    </div>
    <h4 class="fw-bold text-gradient">Two-Factor Authentication</h4>
    <p class="text-muted">Enter the OTP sent to your email or phone</p>

    <!-- OTP Verification Form -->
    <form method="POST" action="{{ route('admin.2fa.verify') }}">
      @csrf
      <div class="mb-3">
        <input type="text" name="otp" maxlength="6" class="form-control otp-input" placeholder="••••••" required>
      </div>
      <button type="submit" class="btn btn-custom w-100">Verify OTP</button>
    </form>

    <!-- Resend OTP Form -->
    <form method="POST" action="{{ route('admin.2fa.resend') }}" class="mt-3">
      @csrf
      <button type="submit" class="btn btn-link resend-link text-secondary">
        Didn’t receive the code? <span class="fw-semibold text-gradient">Resend OTP</span>
      </button>
    </form>
  </div>
</body>
</html>
