<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#d97706">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">

    <title>@yield('title', 'BarberBook - Booking Barbershop Online')</title>

    <!-- Tailwind CSS & Alpine.js -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col justify-between selection:bg-amber-500 selection:text-slate-950">

    <!-- Header Navigation -->
    <header class="sticky top-0 z-40 bg-slate-900/90 backdrop-blur border-b border-slate-800">
        <div class="max-w-md mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('booking.index') }}" class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-lg bg-amber-500 flex items-center justify-center text-slate-950 font-black text-xl shadow-lg shadow-amber-500/20">
                    ✂️
                </div>
                <div>
                    <span class="font-bold text-lg text-white tracking-tight">Barber<span class="text-amber-500">Book</span></span>
                    <span class="block text-[10px] text-slate-400 font-medium tracking-widest uppercase">Premium Barber</span>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <a href="{{ route('booking.history') }}" class="text-xs font-semibold px-3 py-1.5 rounded-full bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition">
                    Riwayat
                </a>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold px-3 py-1.5 rounded-full bg-amber-500 text-slate-950 hover:bg-amber-400 transition">
                            Admin
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="text-xs font-medium text-slate-400 hover:text-amber-400">
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Container (Mobile-first centered max-w-md) -->
    <main class="w-full max-w-md mx-auto px-4 py-6 flex-1">
        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs rounded-xl flex items-center gap-2">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3 bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs rounded-xl">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- PWA Install Prompt Toast -->
    <div id="pwa-toast" class="hidden fixed bottom-4 left-4 right-4 max-w-md mx-auto z-50">
        <div class="bg-amber-500 text-slate-950 p-4 rounded-2xl shadow-2xl flex items-center justify-between gap-3 border border-amber-400">
            <div class="flex items-center gap-3">
                <div class="text-2xl">📱</div>
                <div>
                    <h4 class="font-bold text-sm leading-tight">Install BarberBook</h4>
                    <p class="text-xs opacity-90">Tambahkan ke Layar Utama HP Anda!</p>
                </div>
            </div>
            <button id="pwa-install-btn" class="bg-slate-950 text-amber-400 font-bold text-xs px-3 py-2 rounded-xl hover:bg-slate-900 transition">
                Install
            </button>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-900 border-t border-slate-800 text-center py-4 text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} BarberBook PWA. Booking Barbershop Cepat & Mudah.</p>
    </footer>

    <!-- Service Worker Registration & PWA Script -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('SW Registered'))
                    .catch(err => console.log('SW Registration failed', err));
            });
        }

        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            const toast = document.getElementById('pwa-toast');
            if (toast) toast.classList.remove('hidden');
        });

        document.getElementById('pwa-install-btn')?.addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                if (outcome === 'accepted') {
                    document.getElementById('pwa-toast')?.classList.add('hidden');
                }
                deferredPrompt = null;
            }
        });
    </script>
</body>
</html>
