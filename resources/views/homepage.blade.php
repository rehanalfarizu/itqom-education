<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>itqom-education</title>

    <!-- Tailwind CSS akan di-compile melalui Vite -->
    @if(app()->environment('production'))
        <!-- Production: gunakan built assets -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <!-- Development: gunakan Tailwind CDN -->
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
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />
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

