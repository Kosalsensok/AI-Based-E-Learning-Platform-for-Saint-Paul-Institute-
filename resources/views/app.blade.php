<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1e40af">

    <!-- Favicon Links for Google Search, Mobile, and Desktop Browsers -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="48x48" href="/favicon-48x48.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="144x144" href="/favicon-144x144.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/favicon-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/favicon-512x512.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="apple-touch-icon-precomposed" sizes="180x180" href="/apple-touch-icon-precomposed.png">
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Primary Meta Tags & SEO Snippet -->
    <title inertia>SPI AI-ELMS | ប្រព័ន្ធគ្រប់គ្រងការសិក្សា Saint Paul Institute</title>
    <meta name="title" content="SPI AI-ELMS | ប្រព័ន្ធគ្រប់គ្រងការសិក្សា Saint Paul Institute">
    <meta name="description" content="ប្រព័ន្ធគ្រប់គ្រងការសិក្សាឆ្លាតវៃ SPI AI-ELMS សម្រាប់និស្សិត និងសាស្ត្រាចារ្យ នៃវិទ្យាស្ថានសន្តប៉ូល។ ចូលប្រើប្រាស់ដើម្បីពិនិត្យកាលវិភាគ ពិន្ទុ និងមេរៀន។">
    <meta name="keywords" content="SPI AI-ELMS, Saint Paul Institute, ELMS, SPI, វិទ្យាស្ថានសន្តប៉ូល, ប្រព័ន្ធគ្រប់គ្រងការសិក្សា, spilms, e-learning">
    <meta name="author" content="Saint Paul Institute">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Facebook / Telegram Previews -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://spilms.tech/">
    <meta property="og:title" content="SPI AI-ELMS | ប្រព័ន្ធគ្រប់គ្រងការសិក្សា Saint Paul Institute">
    <meta property="og:description" content="ចូលប្រើប្រាស់ប្រព័ន្ធគ្រប់គ្រងការសិក្សា SPI AI-ELMS ដោយសុវត្ថិភាព និងរហ័ស។">
    <meta property="og:image" content="https://spilms.tech/images/og-cover.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="SPI AI-ELMS">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://spilms.tech/">
    <meta name="twitter:title" content="SPI AI-ELMS | ប្រព័ន្ធគ្រប់គ្រងការសិក្សា Saint Paul Institute">
    <meta name="twitter:description" content="ចូលប្រើប្រាស់ប្រព័ន្ធគ្រប់គ្រងការសិក្សា SPI AI-ELMS ដោយសុវត្ថិភាព និងរហ័ស។">
    <meta name="twitter:image" content="https://spilms.tech/images/og-cover.png">

    <!-- Structured Data (Schema.org) for Google Search & Knowledge Graph -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "EducationalOrganization",
      "name": "SPI AI-ELMS",
      "alternateName": "Saint Paul Institute E-Learning Management System",
      "url": "https://spilms.tech",
      "logo": "https://spilms.tech/images/logo.png",
      "image": "https://spilms.tech/images/og-cover.png",
      "description": "ប្រព័ន្ធគ្រប់គ្រងការសិក្សាឆ្លាតវៃនៃវិទ្យាស្ថានសន្តប៉ូល (Saint Paul Institute)",
      "sameAs": [
        "https://spi.edu.kh"
      ]
    }
    </script>

    <!-- Google Fonts Preconnect & Stylesheets (Kantumruy Pro, Inter, Battambang) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Inter:wght@300;400;500;600;700;800&family=Kantumruy+Pro:ital,wght@0,300..700;1,300..700&display=swap" rel="stylesheet">

    <script>
        (function() {
            try {
                var storedTheme = localStorage.getItem('theme');
                if (storedTheme === 'dark' || (!storedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {}
        })();
    </script>

    @routes
    <!-- Google Identity Services SDK -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>

    <!-- Clerk Authentication SDK -->
    @if(config('services.clerk.publishable_key') || env('VITE_CLERK_PUBLISHABLE_KEY'))
    <script async crossorigin="anonymous" data-clerk-publishable-key="{{ config('services.clerk.publishable_key') ?? env('VITE_CLERK_PUBLISHABLE_KEY') }}" src="https://cdn.jsdelivr.net/npm/@clerk/clerk-js@5/dist/clerk.browser.js" type="text/javascript"></script>
    @endif
    @vite('resources/js/app.ts')
    @inertiaHead
</head>
<body class="font-sans antialiased bg-slate-900 text-slate-100 min-h-screen">
    @inertia
</body>
</html>
