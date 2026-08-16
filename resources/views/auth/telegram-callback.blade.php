<!DOCTYPE html>
<html lang="km" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>កំពុងរៀបចំ Dashboard របស់អ្នក... | SPI LMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes indeterminate {
            0% {
                transform: translateX(-100%) scaleX(0.2);
            }
            50% {
                transform: translateX(0%) scaleX(0.6);
            }
            100% {
                transform: translateX(100%) scaleX(0.2);
            }
        }

        .animate-indeterminate {
            animation: indeterminate 1.4s infinite cubic-bezier(0.65, 0.815, 0.735, 0.395);
            transform-origin: 0% 50%;
        }

        .font-khmer {
            font-family: 'Kantumruy Pro', 'Plus Jakarta Sans', sans-serif;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.96) translateY(4px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.35s ease-out forwards;
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
                // If user declined or no hash in popup window: notify window.opener and close
                if (window.opener && !window.opener.closed) {
                    try {
                        window.opener.postMessage({ event: 'auth_result', result: false }, '*');
                        window.close();
                        return;
                    } catch(e) {}
                }
                window.location.replace('/login?status=declined');
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
<body class="bg-slate-950/60 backdrop-blur-xl text-slate-100 min-h-screen flex items-center justify-center p-4 font-khmer select-none overflow-hidden relative">
    <div id="loading-screen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xl transition-opacity duration-300">
      <!-- Loading Card -->
      <div class="relative w-full max-w-sm mx-4 p-8 rounded-3xl bg-slate-900/80 border border-slate-700/50 shadow-2xl shadow-blue-500/10 flex flex-col items-center text-center animate-fade-in font-khmer">
        
        <!-- Ambient Glow ខាងក្រោយ Logo -->
        <div class="absolute -top-10 w-24 h-24 bg-blue-500/20 rounded-full blur-2xl pointer-events-none"></div>

        <!-- E-LMS Logo ជាមួយ Pulse Effect -->
        <div class="relative mb-5">
          <div class="w-20 h-20 rounded-full p-1 bg-gradient-to-tr from-blue-600 via-sky-400 to-indigo-500 shadow-lg shadow-sky-500/30 animate-pulse">
            <img 
              src="/images/logo.png" 
              alt="E-LMS Logo" 
              class="w-full h-full object-cover rounded-full bg-slate-900"
              onerror="this.src='/logo.png'"
            />
          </div>
        </div>

        <!-- អក្សរខ្មែរច្បាស់ៗ -->
        <h3 class="text-lg font-bold text-white tracking-wide mb-1.5">
          កំពុងរៀបចំ Dashboard របស់អ្នក...
        </h3>
        <p class="text-xs text-slate-400 font-normal leading-relaxed mb-6">
          សូមរង់ចាំមួយភ្លែត ប្រព័ន្ធកំពុងផ្ទៀងផ្ទាត់គណនី
        </p>

        <!-- Modern Animated Progress Bar -->
        <div class="w-full bg-slate-800/80 rounded-full h-1.5 overflow-hidden p-0.5 border border-slate-700/40">
          <div class="bg-gradient-to-r from-blue-500 via-sky-400 to-teal-300 h-full rounded-full animate-indeterminate"></div>
        </div>

      </div>
    </div>
</body>
</html>
