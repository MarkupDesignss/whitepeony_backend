<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment Successful</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
    body {
        margin: 0;
        padding: 0;
        background: linear-gradient(135deg, #42e695, #3bb2b8);
        font-family: 'Poppins', sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        animation: fadePage 1s ease-in-out;
    }
    @keyframes fadePage { from{opacity:0;transform:scale(0.98);} to{opacity:1;transform:scale(1);} }

    .card {
        background: #fff;
        padding: 40px;
        border-radius: 25px;
        width: 90%;
        max-width: 550px;
        text-align: center;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        animation: slideUp 1s ease-out;
    }
    @keyframes slideUp { from{transform:translateY(40px);opacity:0;} to{transform:translateY(0);opacity:1;} }

    .checkmark {
        width: 120px;
        height: 120px;
        background: #e8fff1;
        border-radius: 50%;
        margin: 0 auto 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        animation: pop 0.6s ease-out;
    }
    @keyframes pop {0%{transform:scale(0);}100%{transform:scale(1);}}

    .checkmark img { width: 70px; animation: bounce 1s ease-in-out; }
    @keyframes bounce {0%{transform:scale(0.5);opacity:0;}60%{transform:scale(1.2);opacity:1;}100%{transform:scale(1);}}

    h2 { font-size: 28px; color: #28a745; font-weight: 700; margin-bottom: 10px; }
    p { color:#555;font-size:15px; }

    .details { text-align:left; margin-top:25px; animation: fadeIn 1s ease-in-out; }
    @keyframes fadeIn {from{opacity:0;}to{opacity:1;}}

    .details p { font-size: 16px; margin: 8px 0; }

    .btn {
        display: inline-block;
        margin-top: 25px;
        background: #0069d9;
        color: #fff;
        padding: 12px 35px;
        border-radius: 50px;
        text-decoration: none;
        font-size: 16px;
        font-weight: 600;
        transition: 0.3s;
        box-shadow: 0 8px 20px rgba(0, 105, 217, 0.3);
    }
    .btn:hover { background:#0052aa; box-shadow:0 10px 25px rgba(0, 105, 217, 0.5); transform:translateY(-3px);}

</style>
</head>
<body>

<div class="card">

    <div class="checkmark">
        <img src="https://cdn-icons-png.flaticon.com/512/845/845646.png" alt="success">
    </div>

    <h2>Payment Successful!</h2>
    <p>Thank you! Your event registration payment has been confirmed.</p>

    <div class="details">
        <p><strong>🧾 Registration ID:</strong> {{ $registration_id }}</p>
        <p><strong>📄 PayPal Order ID:</strong> {{ $paypal_id ?? $tracking }}</p>
        <p><strong>💰 Amount Paid:</strong> ₹{{ number_format($amount, 2) }}</p>
        <p><strong>💳 Payment Method:</strong> {{ $payment_method }}</p>
    </div>

    <a href="/" class="btn">Back to Home</a>

</div>

</body>
</html>
