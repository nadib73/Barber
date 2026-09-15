<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - BarberBook</title>

    <!-- Tailwind & Alpine -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex selection:bg-amber-500 selection:text-slate-950">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-950 border-r border-slate-800 hidden md:flex flex-col">
        <div class="h-16 flex items-center px-6 border-b border-slate-800">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-500 flex items-center justify-center text-slate-950 font-black text-lg">✂️</div>
                <span class="font-bold text-white tracking-tight">Barber<span class="text-amber-500">Book</span></span>
            </a>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                📊 Dashboard
            </a>
            <a href="{{ route('admin.bookings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.bookings.*') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                📅 Jadwal Booking
            </a>
            <a href="{{ route('admin.kapsters.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.kapsters.*') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                💈 Data Kapster
            </a>
            <a href="{{ route('admin.services.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.services.*') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                ✂️ Data Layanan
            </a>
        </nav>
        <div class="p-4 border-t border-slate-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-rose-400 hover:bg-rose-500/10 transition">
                    🚪 Logout Admin
                </button>
            </form>
        </div>
    </aside>

    <!-- Mobile Header (Hidden on Desktop) -->
    <div class="md:hidden fixed top-0 w-full h-16 bg-slate-950 border-b border-slate-800 flex items-center justify-between px-4 z-40">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-amber-500 flex items-center justify-center text-slate-950 font-black text-lg">✂️</div>
            <span class="font-bold text-white tracking-tight">Barber<span class="text-amber-500">Book</span></span>
        </a>
        <button id="mobile-menu-btn" class="text-slate-400 hover:text-white p-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
    </div>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu" class="fixed inset-0 bg-slate-950/95 z-50 hidden flex-col border-b border-slate-800 pb-4">
        <div class="h-16 flex items-center justify-between px-4 border-b border-slate-800 mb-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-500 flex items-center justify-center text-slate-950 font-black text-lg">✂️</div>
                <span class="font-bold text-white tracking-tight">Admin Menu</span>
            </div>
            <button id="mobile-menu-close" class="text-slate-400 hover:text-white p-2">
                ✕
            </button>
        </div>
        <nav class="flex-1 px-4 space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 rounded-xl text-sm font-bold bg-slate-900 border border-slate-800 text-slate-200">📊 Dashboard</a>
            <a href="{{ route('admin.bookings.index') }}" class="block px-4 py-3 rounded-xl text-sm font-bold bg-slate-900 border border-slate-800 text-slate-200">📅 Jadwal Booking</a>
            <a href="{{ route('admin.kapsters.index') }}" class="block px-4 py-3 rounded-xl text-sm font-bold bg-slate-900 border border-slate-800 text-slate-200">💈 Data Kapster</a>
            <a href="{{ route('admin.services.index') }}" class="block px-4 py-3 rounded-xl text-sm font-bold bg-slate-900 border border-slate-800 text-slate-200">✂️ Data Layanan</a>
            <form method="POST" action="{{ route('logout') }}" class="pt-4">
                @csrf
                <button type="submit" class="w-full px-4 py-3 rounded-xl text-sm font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">🚪 Logout</button>
            </form>
        </nav>
    </div>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col md:pl-0 pt-16 md:pt-0 overflow-hidden">
        
        <!-- Top Nav (Desktop) -->
        <header class="h-16 bg-slate-900/50 backdrop-blur border-b border-slate-800 hidden md:flex items-center justify-between px-8 sticky top-0 z-30">
            <h1 class="text-lg font-bold text-white">@yield('page_title', 'Admin Panel')</h1>
            <div class="flex items-center gap-4">
                <a href="{{ route('booking.index') }}" target="_blank" class="text-xs font-semibold px-3 py-1.5 rounded-full bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition flex items-center gap-1.5">
                    🌐 Buka Web Pelanggan
                </a>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-xs font-bold">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    <span class="text-sm font-medium text-slate-300">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-xl flex items-center gap-2">
                    <span>✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm rounded-xl">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', () => {
            document.getElementById('mobile-menu').classList.remove('hidden');
            document.getElementById('mobile-menu').classList.add('flex');
        });
        document.getElementById('mobile-menu-close')?.addEventListener('click', () => {
            document.getElementById('mobile-menu').classList.add('hidden');
            document.getElementById('mobile-menu').classList.remove('flex');
        });
    </script>
</body>
</html>
