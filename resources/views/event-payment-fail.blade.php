<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment Failed</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
    body {
        margin: 0;
        padding: 0;
        background: linear-gradient(135deg, #ff6b6b, #ff4757);
        font-family: "Poppins", sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .card {
        background: #fff;
        width: 90%;
        max-width: 550px;
        padding: 40px;
        border-radius: 20px;
        text-align: center;
        box-shadow: 0px 10px 30px rgba(0,0,0,0.2);
        animation: fadeIn 1s ease-in-out;
    }

    .fail-icon {
        width: 120px;
        height: 120px;
        background: #ff4d4d;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto 20px auto;
        animation: pop 0.7s ease-in-out;
    }

    .fail-icon span {
        font-size: 70px;
        color: white;
        font-weight: bold;
        animation: rotateX 1s ease-in-out;
    }

    h2 {
        color: #ff2e2e;
        font-size: 32px;
        margin-bottom: 10px;
        animation: slideDown 1s ease;
    }

    p { color: #555; font-size: 16px; line-height: 1.6; }

    .details {
        text-align: left;
        margin-top: 25px;
        padding: 15px;
        background: #ffe5e5;
        border-left: 4px solid #ff3b3b;
        border-radius: 10px;
        animation: fadeUp 1s ease;
    }

    .btn-home {
        display: inline-block;
        margin-top: 30px;
        padding: 12px 30px;
        border-radius: 12px;
        background: #ff3b3b;
        color: #fff;
        font-weight: 600;
        text-decoration: none;
        transition: 0.3s;
        animation: fadeUp 1.2s;
    }

    .btn-home:hover {
        background: #d72626;
        transform: translateY(-3px);
    }

    @keyframes pop { 0%{transform:scale(0);}100%{transform:scale(1);} }
    @keyframes rotateX {0%{transform:rotateX(180deg);opacity:0;}100%{transform:rotateX(0);opacity:1;}}
    @keyframes fadeIn {from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
    @keyframes fadeUp {from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
    @keyframes slideDown {from{opacity:0;transform:translateY(-20px);}to{opacity:1;transform:translateY(0);}}
</style>

</head>
<body>

<div class="card">

    <div class="fail-icon"><span>✖</span></div>

    <h2>Payment Failed</h2>
    <p>Oops! Something went wrong. Your registration payment could not be processed.</p>

    <div class="details">

        @if (isset($registration_id))
        <p><strong>🧾 Registration ID:</strong> {{ $registration_id }}</p>
        @endif

        @if(isset($reason))
        <p><strong>⚠ Reason:</strong> {{ $reason }}</p>
        @endif

        @if(isset($payment_method))
        <p><strong>💳 Payment Method:</strong> {{ $payment_method }}</p>
        @endif

        <p>Please try again or contact support if the issue persists.</p>
    </div>

    <a href="/" class="btn-home">Back to Home</a>

</div>

</body>
</html>
