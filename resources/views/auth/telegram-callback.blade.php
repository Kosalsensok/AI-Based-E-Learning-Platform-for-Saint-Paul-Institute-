<!DOCTYPE html>
<html lang="km" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>កំពុងរៀបចំ Dashboard របស់អ្នក... | SPI LMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Kantumruy Pro', 'Plus Jakarta Sans', sans-serif;
        }
        @keyframes pulseGlow {
            0%, 100% { transform: scale(1); opacity: 0.3; }
            50% { transform: scale(1.15); opacity: 0.6; }
        }
        .glow-effect {
            animation: pulseGlow 3s ease-in-out infinite;
        }
    </style>
    <script>
    (function() {
        try {
            function safeBase64Decode(str) {
                try {
                    var b64 = str.replace(/-/g, '+').replace(/_/g, '/');
                    while (b64.length % 4 !== 0) b64 += '=';
                    var binString = atob(b64);
                    var bytes = Uint8Array.from(binString, function(m) { return m.charCodeAt(0); });
                    return new TextDecoder().decode(bytes);
                } catch (e) {
                    return atob(str);
                }
            }

            var tgUser = null;
            if (window.location.hash && window.location.hash.includes('tgAuthResult=')) {
                var hashStr = window.location.hash.substring(1);
                var params = new URLSearchParams(hashStr);
                var tgAuthResult = params.get('tgAuthResult');
                if (tgAuthResult) {
                    var decoded = safeBase64Decode(tgAuthResult);
                    tgUser = JSON.parse(decoded);
                }
            }

            if (!tgUser) {
                var searchParams = new URLSearchParams(window.location.search);
                if (searchParams.get('id')) {
                    tgUser = {};
                    searchParams.forEach(function(val, key) { tgUser[key] = val; });
                }
            }

            if (!tgUser || !tgUser.id) {
                window.location.replace('/login' + (window.location.search || '?error=cancelled'));
                return;
            }

            // Direct Standard Form POST Submission for 100% Reliable Session Cookie & Redirect
            function submitForm() {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '/auth/telegram';

                var csrfMeta = document.querySelector('meta[name="csrf-token"]');
                if (csrfMeta) {
                    var csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfMeta.getAttribute('content');
                    form.appendChild(csrfInput);
                }

                for (var key in tgUser) {
                    if (tgUser.hasOwnProperty(key) && tgUser[key] !== null && tgUser[key] !== undefined) {
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = typeof tgUser[key] === 'object' ? JSON.stringify(tgUser[key]) : String(tgUser[key]);
                        form.appendChild(input);
                    }
                }

                (document.body || document.documentElement).appendChild(form);

                // Smooth luxury transition (450ms)
                setTimeout(function() {
                    form.submit();
                }, 450);
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', submitForm);
            } else {
                submitForm();
            }
        } catch (e) {
            window.location.replace('/login?error=unauthorized');
        }
    })();
    </script>
</head>
<body class="bg-[#070D1E] text-slate-100 min-h-screen flex items-center justify-center p-4 font-sans select-none overflow-hidden relative">
    <!-- Ambient Backdrop Glows -->
    <div class="absolute w-[450px] h-[450px] rounded-full bg-blue-600/15 blur-[120px] pointer-events-none glow-effect"></div>
    <div class="absolute w-[350px] h-[350px] rounded-full bg-sky-500/10 blur-[100px] pointer-events-none -bottom-10 -right-10"></div>

    <!-- Luxury Transition Card -->
    <div class="relative z-10 w-full max-w-sm p-8 rounded-3xl bg-slate-900/70 border border-slate-800/80 backdrop-blur-xl shadow-2xl shadow-black/50 text-center flex flex-col items-center gap-5">
        <!-- Logo with Glow -->
        <div class="relative flex items-center justify-center">
            <div class="absolute w-16 h-16 rounded-full bg-sky-500/20 blur-md"></div>
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-sky-400 p-0.5 shadow-lg shadow-sky-500/20 flex items-center justify-center relative z-10">
                <div class="w-full h-full bg-[#0B132B] rounded-[14px] flex items-center justify-center">
                    <svg class="w-7 h-7 text-sky-400 fill-current" viewBox="0 0 24 24">
                        <path d="M12 2L1 7l11 5 9-4.09V17h2V7L12 2zm0 13l-7-3.18V17l7 3.18L19 17v-5.18L12 15z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Spinner & Status Text -->
        <div class="flex flex-col items-center gap-2">
            <h3 class="text-base font-bold text-white tracking-wide">កំពុងរៀបចំ Dashboard របស់អ្នក...</h3>
            <p class="text-xs text-slate-400 font-medium">សូមរង់ចាំមួយភ្លែត ប្រព័ន្ធកំពុងតភ្ជាប់គណនី</p>
        </div>

        <!-- Animated Progress Line -->
        <div class="w-full bg-slate-800/80 rounded-full h-1.5 overflow-hidden p-0.5">
            <div class="bg-gradient-to-r from-blue-500 via-sky-400 to-blue-500 h-full rounded-full w-2/3 animate-[pulse_1.5s_ease-in-out_infinite]"></div>
        </div>
    </div>
</body>
</html>
