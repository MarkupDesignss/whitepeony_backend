<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | Whitepeony Tea</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f7f8ef 0%, #eef1d4 100%);
            color: #2f2f2f;
            line-height: 1.8;
            padding: 40px 20px;
        }
        .container {
            max-width: 950px;
            margin: auto;
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
        }
        .hero {
            background: linear-gradient(135deg, #aeb254 0%, #8f9442 100%);
            padding: 70px 50px;
            text-align: center;
            color: #fff;
            position: relative;
        }
        .hero::after {
            content: "";
            position: absolute;
            bottom: -40px;
            left: 0;
            width: 100%;
            height: 80px;
            background: #fff;
            border-radius: 50% 50% 0 0;
        }
        .hero h1 {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 14px;
        }
        .content {
            padding: 70px 50px 50px;
        }
        .section {
            background: #fafbf2;
            border-left: 6px solid #aeb254;
            padding: 28px;
            border-radius: 18px;
            margin-bottom: 28px;
        }
        h2 {
            color: #7f8437;
            font-size: 24px;
            margin-bottom: 14px;
        }
        .footer {
            background: #aeb254;
            color: #fff;
            text-align: center;
            padding: 30px;
            font-size: 14px;
            margin-top: 30px;
        }
        a {
            color: #7f8437;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero">
            <h1>{{ $title }}</h1>
        </div>
        <div class="content">
            @if($image)
                <img src="{{ $image }}" class="img-fluid rounded mb-4" style="max-width:100%; margin-bottom:20px;">
            @endif
            {!! $content !!}
        </div>
        <div class="footer">
            © 2026 Whitepeony Tea — Luxury • Ecology • Modernity
        </div>
    </div>
</body>
</html>