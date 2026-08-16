<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Telegram Authenticating... | SPI LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-white min-h-screen flex items-center justify-center p-4 font-sans select-none">
    <div class="max-w-md w-full bg-slate-900/90 border border-slate-800 rounded-3xl p-8 text-center space-y-4 shadow-2xl">
        <div class="w-14 h-14 rounded-2xl bg-sky-500/20 text-sky-400 flex items-center justify-center mx-auto ring-4 ring-sky-500/20 animate-pulse">
            <svg class="w-7 h-7 fill-current" viewBox="0 0 24 24">
                <path d="M12 0C5.37 0 0 5.37 0 12s5.37 12 12 12 12-5.37 12-12S18.63 0 12 0zm5.56 8.16l-1.92 9.07c-.14.65-.53.81-1.07.51l-2.97-2.19-1.43 1.38c-.16.16-.29.29-.6.29l.21-3.03 5.51-4.98c.24-.21-.05-.33-.37-.12l-6.81 4.29-2.94-.92c-.64-.2-.65-.64.13-.95l11.49-4.43c.53-.2 1 .13.77 1.07z"/>
            </svg>
        </div>
        <h2 class="text-lg font-bold text-white">កំពុងផ្ទៀងផ្ទាត់គណនី Telegram...</h2>
        <p class="text-xs text-slate-400" id="status-text">សូមរង់ចាំមួយភ្លែត ប្រព័ន្ធកំពុងតភ្ជាប់គណនីរបស់អ្នក</p>
        <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
            <div class="bg-sky-500 h-full w-2/3 animate-[pulse_1s_infinite]"></div>
        </div>
    </div>

    <script>
    (function() {
        try {
            function safeBase64Decode(str) {
                try {
                    let b64 = str.replace(/-/g, '+').replace(/_/g, '/');
                    while (b64.length % 4 !== 0) b64 += '=';
                    const binString = atob(b64);
                    const bytes = Uint8Array.from(binString, (m) => m.charCodeAt(0));
                    return new TextDecoder().decode(bytes);
                } catch (e) {
                    return atob(str);
                }
            }

            let tgUser = null;
            
            // 1. Try parsing hash fragment (#tgAuthResult=...)
            if (window.location.hash && window.location.hash.includes('tgAuthResult=')) {
                const hashStr = window.location.hash.substring(1);
                const params = new URLSearchParams(hashStr);
                const tgAuthResult = params.get('tgAuthResult');
                if (tgAuthResult) {
                    const decoded = safeBase64Decode(tgAuthResult);
                    tgUser = JSON.parse(decoded);
                }
            }

            // 2. Try parsing query parameters (?id=...)
            if (!tgUser) {
                const searchParams = new URLSearchParams(window.location.search);
                if (searchParams.get('id')) {
                    tgUser = {};
                    searchParams.forEach((val, key) => { tgUser[key] = val; });
                }
            }

            if (!tgUser || !tgUser.id) {
                window.location.replace('/login' + (window.location.search || '?error=cancelled'));
                return;
            }

            // Direct Standard Form POST Submission for 100% Reliable Session Cookie & Redirect
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/auth/telegram';

            for (const [key, value] of Object.entries(tgUser)) {
                if (value !== null && value !== undefined) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = typeof value === 'object' ? JSON.stringify(value) : String(value);
                    form.appendChild(input);
                }
            }

            document.body.appendChild(form);
            form.submit();
        } catch (e) {
            console.error('Telegram Callback Error:', e);
            window.location.replace('/login?error=unauthorized');
        }
    })();
    </script>
</body>
</html>
