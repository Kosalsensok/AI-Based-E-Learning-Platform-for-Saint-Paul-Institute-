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
    (async function() {
        try {
            let tgUser = null;
            
            // 1. Try parsing hash fragment (#tgAuthResult=...)
            if (window.location.hash && window.location.hash.includes('tgAuthResult=')) {
                const hashStr = window.location.hash.substring(1);
                const params = new URLSearchParams(hashStr);
                const tgAuthResult = params.get('tgAuthResult');
                if (tgAuthResult) {
                    let base64 = tgAuthResult.replace(/-/g, '+').replace(/_/g, '/');
                    while (base64.length % 4 !== 0) base64 += '=';
                    const decoded = atob(base64);
                    tgUser = JSON.parse(decoded);
                }
            }

            // 2. Try parsing query parameters (?id=...)
            if (!tgUser) {
                const searchParams = new URLSearchParams(window.location.search);
                if (searchParams.get('id')) {
                    tgUser = Object.fromEntries(searchParams.entries());
                }
            }

            if (!tgUser || !tgUser.id) {
                document.getElementById('status-text').innerText = 'រកមិនឃើញទិន្នន័យពី Telegram... កំពុងត្រឡប់ទៅ Login';
                setTimeout(() => { window.location.href = '/login'; }, 1500);
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const res = await fetch('/auth/telegram', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(tgUser)
            });

            const data = await res.json().catch(() => ({}));
            if (res.ok && data.success) {
                window.location.href = data.redirect || '/student/dashboard';
            } else {
                alert(data.message || 'ការផ្ទៀងផ្ទាត់ Telegram មិនត្រឹមត្រូវ');
                window.location.href = '/login';
            }
        } catch (e) {
            console.error(e);
            window.location.href = '/login';
        }
    })();
    </script>
</body>
</html>
