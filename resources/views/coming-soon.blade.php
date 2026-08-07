<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sắp Ra Mắt - AIM Agency</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #00ff88;
            --secondary: #0a0a0a;
            --text-main: #ffffff;
            --text-muted: #888888;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--secondary);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        /* Animated Background Gradients */
        .bg-glow {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(0,255,136,0.15) 0%, rgba(10,10,10,0) 70%);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
            animation: pulse 4s ease-in-out infinite alternate;
        }

        @keyframes pulse {
            0% { transform: translate(-50%, -50%) scale(1); opacity: 0.5; }
            100% { transform: translate(-50%, -50%) scale(1.2); opacity: 1; }
        }

        .content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 2rem;
            max-width: 800px;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 900;
            letter-spacing: 4px;
            color: var(--primary);
            margin-bottom: 3rem;
            text-transform: uppercase;
        }

        h1 {
            font-size: 5rem;
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #a5a5a5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -2px;
        }

        p {
            font-size: 1.2rem;
            font-weight: 300;
            color: var(--text-muted);
            margin-bottom: 3rem;
            line-height: 1.6;
        }

        .notify-btn {
            background: transparent;
            color: var(--text-main);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 15px 40px;
            font-size: 1rem;
            font-weight: 500;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .notify-btn:hover {
            background: var(--text-main);
            color: var(--secondary);
            border-color: var(--text-main);
            box-shadow: 0 0 20px rgba(255,255,255,0.2);
        }

        /* Glitch effect on title hover */
        h1:hover {
            animation: glitch 0.3s linear infinite;
        }

        @keyframes glitch {
            0% { transform: translate(0) }
            20% { transform: translate(-2px, 2px) }
            40% { transform: translate(-2px, -2px) }
            60% { transform: translate(2px, 2px) }
            80% { transform: translate(2px, -2px) }
            100% { transform: translate(0) }
        }

        .admin-link {
            position: absolute;
            bottom: 30px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
            z-index: 2;
        }

        .admin-link:hover {
            color: var(--primary);
        }

        @media (max-width: 768px) {
            h1 { font-size: 3.5rem; }
            p { font-size: 1rem; }
        }
    </style>
</head>
<body>
    <div class="bg-glow"></div>
    
    <div class="content">
        <div class="logo-text">AIM Agency</div>
        <h1>WE ARE<br>CRAFTING<br>SOMETHING EPIC</h1>
        <p>Hệ thống đang được nâng cấp và phát triển. Hãy quay lại sau nhé!<br>Một trải nghiệm tuyệt vời đang chờ đón bạn.</p>
        
        <button class="notify-btn" onclick="alert('Cảm ơn bạn! Chúng tôi sẽ sớm trở lại.')">Stay Tuned</button>
    </div>

    <!-- Hidden admin link to keep it easy for you to login -->
    <a href="/login" class="admin-link">Staff Login →</a>
</body>
</html>
