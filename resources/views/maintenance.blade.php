<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode</title>
    <style>
        body {
            text-align: center;
            padding: 50px;
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #f4f4f9;
        }
        h1 {
            font-size: 48px;
            margin-bottom: 20px;
            color: #444;
        }
        p {
            font-size: 18px;
            color: #666;
            margin-bottom: 40px;
        }
        .maintenance-image {
            max-width: 300px;
            margin: 20px auto;
        }
        .maintenance-image img {
            width: 100%;
            height: auto;
            animation: bounce 2s infinite;
            mix-blend-mode: multiply;
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
    </style>
</head>
<body>
<h1>🎧 We'll be back soon!</h1>
<p>
    Sorry for the inconvenience, but our music site is undergoing maintenance. We're working hard to bring it back better than ever! 🎶
</p>
<div class="maintenance-image">
    <img src="{{asset('public/images/maintenance.png')}}" alt="Headphones Under Maintenance">
</div>
<p>Thank you for your patience and support! 🙏</p>
</body>
</html>
