<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BarberBook') }} - Login</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col justify-center items-center px-4 py-8 selection:bg-amber-500 selection:text-slate-950">
    <div class="w-full max-w-md space-y-6">
        
        <!-- Brand Header -->
        <div class="text-center space-y-2">
            <a href="/" class="inline-flex items-center gap-2">
                <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center text-slate-950 font-black text-2xl shadow-lg shadow-amber-500/20">
                    ✂️
                </div>
                <span class="font-black text-2xl text-white tracking-tight">Barber<span class="text-amber-500">Book</span></span>
            </a>
            <p class="text-xs text-slate-400">Portal Masuk Khusus Admin & Manajemen</p>
        </div>

        <!-- Card Container -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 md:p-8 shadow-2xl relative">
            {{ $slot }}
        </div>

        <!-- Back to Customer Web Link -->
        <div class="text-center">
            <a href="{{ route('booking.index') }}" class="text-xs text-slate-500 hover:text-amber-400 transition inline-flex items-center gap-1.5">
                &larr; Kembali ke Halaman Booking Pelanggan
            </a>
        </div>
    </div>
</body>
</html>
