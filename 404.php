<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Страница не найдена</title>
    <style>
        :root {
            --primary-color: #1A272D;
            --secondary-color: #2A3437;
            --accent-color: #FF9900;
            --text-color: #1A272D;
            --bg-color: #F5F5F5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Roboto', sans-serif;
            color: var(--text-color);
            padding: 20px;
        }

        .error-container {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(26, 39, 45, 0.1);
            max-width: 600px;
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .error-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--secondary-color), var(--accent-color));
        }

        .error-code {
            font-size: clamp(80px, 15vw, 150px);
            background: linear-gradient(45deg, var(--secondary-color), var(--accent-color));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 30px;
            text-shadow: 2px 2px 10px rgba(26, 39, 45, 0.1);
        }

        h2 {
            font-size: clamp(24px, 5vw, 32px);
            margin-bottom: 20px;
            color: var(--primary-color);
        }

        p {
            font-size: clamp(16px, 3vw, 18px);
            margin-bottom: 30px;
            color: var(--text-color);
            line-height: 1.6;
        }

        .home-button {
            display: inline-block;
            padding: 15px 40px;
            background: var(--accent-color);
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 500;
            font-size: 18px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .home-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 153, 0, 0.3);
        }

        @media (max-width: 768px) {
            .error-container {
                padding: 30px 20px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .error-container {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <h2>Страница не найдена</h2>
        <p>К сожалению, запрашиваемая страница не существует или была перемещена.</p>
        <a href="/" class="home-button">Вернуться на главную</a>
    </div>
</body>
</html>