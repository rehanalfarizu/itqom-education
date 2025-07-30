<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vue Homepage</title>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.1/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />
    @vite(['resources/js/app.js']) {{-- Hubungkan dengan Vite --}}
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
    },
  }
</script>

</head>
<body>
    <div id="app">
        <!-- Vue Router akan menangani rendering komponen -->
    </div>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

<script>
  AOS.init({
    duration: 1000,
    once: true,
  });
</script>
</body>
</html>
