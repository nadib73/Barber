@extends('layouts.barber')

@section('title', 'Riwayat Booking - BarberBook')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-white tracking-tight">Riwayat Booking</h1>
            <p class="text-xs text-slate-400">Cek status dan riwayat potong rambut Anda</p>
        </div>
        <a href="{{ route('booking.index') }}" class="text-xs font-bold px-3 py-1.5 rounded-lg bg-amber-500 text-slate-950 hover:bg-amber-400 transition">
            + Booking Baru
        </a>
    </div>

    <!-- Search by Phone (if not logged in) -->
    @guest
        <form action="{{ route('booking.history') }}" method="GET" class="p-4 bg-slate-900 border border-slate-800 rounded-xl space-y-3">
            <label class="block text-xs font-semibold text-slate-300">Cari Berdasarkan Nomor WhatsApp:</label>
            <div class="flex gap-2">
                <input 
                    type="tel" 
                    name="phone" 
                    value="{{ request('phone') }}" 
                    required 
                    placeholder="Contoh: 081234567890" 
                    class="flex-1 bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500"
                >
                <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-lg transition">
                    Cari
                </button>
            </div>
        </form>
    @endguest

    <!-- Bookings List -->
    <div class="space-y-3">
        @forelse($bookings as $item)
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 space-y-3">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-[10px] text-slate-500 uppercase font-mono">#BOOK-{{ $item->id }}</span>
                        <h3 class="font-bold text-sm text-white">{{ $item->service->name }}</h3>
                        <p class="text-xs text-amber-400 font-medium">✂️ {{ $item->kapster ? $item->kapster->name : 'Siapa Saja' }}</p>
                    </div>

                    <div>
                        @if($item->status === 'confirmed')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                Dikonfirmasi
                            </span>
                        @elseif($item->status === 'completed')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                Selesai
                            </span>
                        @elseif($item->status === 'cancelled')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                Dibatalkan
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                                Menunggu
                            </span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs text-slate-400 border-t border-slate-800/80 pt-2.5">
                    <div>
                        <span>📅 {{ \Carbon\Carbon::parse($item->booking_date)->translatedFormat('d M Y') }}</span>
                        <span class="ml-2">⏰ {{ \Carbon\Carbon::parse($item->booking_time)->format('H:i') }} WIB</span>
                    </div>
                    <span class="font-bold text-slate-200">Rp {{ number_format($item->service->price, 0, ',', '.') }}</span>
                </div>

                <!-- Rebook Button -->
                <div class="pt-1">
                    <a href="{{ route('booking.index') }}" class="block text-center py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-[11px] font-semibold text-slate-300 transition">
                        Booking Ulang Layanan Ini &rarr;
                    </a>
                </div>
            </div>
        @empty
            <div class="p-8 text-center bg-slate-900 border border-slate-800 rounded-xl space-y-2">
                <div class="text-3xl">📭</div>
                <h4 class="font-bold text-sm text-slate-300">Belum Ada Riwayat Booking</h4>
                <p class="text-xs text-slate-500">
                    @if(request('phone'))
                        Tidak ditemukan riwayat untuk nomor {{ request('phone') }}.
                    @else
                        Silakan masukkan nomor WhatsApp Anda di atas untuk melihat riwayat booking.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

</div>
@endsection
