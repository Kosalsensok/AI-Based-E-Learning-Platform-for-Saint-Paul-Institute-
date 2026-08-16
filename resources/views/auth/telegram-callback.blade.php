<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connecting Telegram... | SPI LMS</title>
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
            window.addEventListener('DOMContentLoaded', function() {
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

                document.body.appendChild(form);
                form.submit();
            });
        } catch (e) {
            window.location.replace('/login?error=unauthorized');
        }
    })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-white min-h-screen flex items-center justify-center p-4 font-sans select-none">
    <div class="flex flex-col items-center justify-center gap-3">
        <div class="w-10 h-10 border-3 border-sky-400 border-t-transparent rounded-full animate-spin"></div>
        <span class="text-xs text-slate-300 font-medium tracking-wide">Connecting SPI LMS...</span>
    </div>
</body>
</html>
