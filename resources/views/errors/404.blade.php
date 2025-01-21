<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #111;
            color: #fff;
            font-family: 'Arial', sans-serif;
            height: 100vh;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            perspective: 1000px;
        }

        .container {
            position: relative;
            width: 100%;
            height: 100vh;
            transform-style: preserve-3d;
        }

        .parallax-layer {
            position: absolute;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            transform-style: preserve-3d;
        }

        .bg-stars {
            position: absolute;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, #222 1px, transparent 1px);
            background-size: 50px 50px;
            transform: translateZ(-500px) scale(2.5);
        }

        .error-number {
            font-size: 15rem;
            font-weight: bold;
            color: #333;
            text-shadow: 2px 2px 10px rgba(255, 255, 255, 0.1);
            transform: translateZ(-200px) scale(1.5);
        }

        .astronaut {
            position: absolute;
            font-size: 8rem;
            transform: translateZ(0) scale(1);
            animation: float 6s ease-in-out infinite;
        }

        .text-content {
            text-align: center;
            transform: translateZ(100px) scale(0.9);
        }

        h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #fff;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        p {
            font-size: 1.2rem;
            color: #aaa;
            margin-bottom: 2rem;
        }

        .btn-home {
            padding: 1rem 2rem;
            background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
            border: none;
            border-radius: 30px;
            color: white;
            font-size: 1.1rem;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            text-decoration: none;
        }

        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-30px) rotate(5deg);
            }
        }

        .meteors {
            position: absolute;
            width: 100%;
            height: 100%;
            transform: translateZ(-300px) scale(2);
            pointer-events: none;
        }

        .meteor {
            position: absolute;
            width: 2px;
            height: 2px;
            background: #fff;
            opacity: 0;
            transform-origin: center;
            animation: meteor 3s linear infinite;
        }

        @keyframes meteor {
            0% {
                transform: translate(120vw, -50vh) rotate(45deg) scale(0);
                opacity: 1;
            }
            100% {
                transform: translate(-20vw, 50vh) rotate(45deg) scale(3);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="parallax-layer bg-stars"></div>
    <div class="parallax-layer meteors" id="meteors"></div>
    <div class="parallax-layer">
        <div class="error-number">404</div>
    </div>
    <div class="parallax-layer">
        <div class="astronaut">👨‍🚀</div>
    </div>
    <div class="parallax-layer">
        <div class="text-content">
            <h1>Houston, We Have a Problem!</h1>
            <p>The page you're looking for seems to have drifted into deep space.</p>
            <a href="/" class="btn-home">Return to Home</a>
        </div>
    </div>
</div>

<script>
    // Parallax effect
    document.addEventListener('mousemove', (e) => {
        const layers = document.querySelectorAll('.parallax-layer');
        const centerX = window.innerWidth / 2;
        const centerY = window.innerHeight / 2;
        const mouseX = e.clientX - centerX;
        const mouseY = e.clientY - centerY;

        layers.forEach((layer, i) => {
            const speed = (i + 1) * 0.01;
            const x = (mouseX * speed);
            const y = (mouseY * speed);
            layer.style.transform = `translate3d(${x}px, ${y}px, ${layer.style.zIndex || 0}px)`;
        });
    });

    // Create meteors
    function createMeteors() {
        const meteorsContainer = document.getElementById('meteors');
        const meteor = document.createElement('div');
        meteor.className = 'meteor';

        // Random position
        meteor.style.top = Math.random() * 100 + 'vh';
        meteor.style.left = Math.random() * 100 + 'vw';

        meteorsContainer.appendChild(meteor);

        // Remove meteor after animation
        setTimeout(() => {
            meteor.remove();
        }, 3000);
    }

    // Create meteors periodically
    setInterval(createMeteors, 300);
</script>
</body>
</html>
