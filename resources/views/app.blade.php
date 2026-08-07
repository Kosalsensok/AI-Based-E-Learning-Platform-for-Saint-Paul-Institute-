<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1e40af">
    <link rel="icon" type="image/x-icon" href="/favicon.ico?v=2">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/logo.png?v=2">
    <link rel="apple-touch-icon" href="/images/logo.png?v=2">
    <link rel="manifest" href="/manifest.webmanifest">
    <title inertia>E-LMS</title>

    <!-- Google Fonts Preconnect & Stylesheets (Kantumruy Pro, Inter, Battambang) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Inter:wght@300;400;500;600;700;800&family=Kantumruy+Pro:ital,wght@0,300..700;1,300..700&display=swap" rel="stylesheet">

    @routes
    @vite('resources/js/app.ts')
    @inertiaHead
</head>
<body class="font-sans antialiased">@inertia</body>
</html>
