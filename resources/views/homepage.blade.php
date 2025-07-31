<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vue Homepage</title>

    <!-- Hanya gunakan satu Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primaryDark: '#564AB1',
                        primaryLight: '#B0ABDB',
                    },
                },
                screens: {
                    'sm': '640px',
                    'md': '768px',
                    'lg': '1024px',
                    'xl': '1280px',
                    '2xl': '1536px',
                }
            },
        }
    </script>

    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />
    @vite(['resources/js/app.js'])
</head>
<body>
    <div id="app"></div>

    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true,
        });
    </script>
</body>
</html>

