@extends('layouts.barber')

@section('title', 'Booking Berhasil! - BarberBook')

@section('content')
<div class="space-y-6 text-center py-4">

    <!-- Success Icon & Header -->
    <div class="space-y-2">
        <div class="w-16 h-16 bg-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center text-3xl mx-auto border border-emerald-500/30">
            ✓
        </div>
        <h1 class="text-2xl font-black text-white tracking-tight">Booking Berhasil!</h1>
        <p class="text-xs text-slate-400">
            Detail booking telah tersimpan dan konfirmasi dikirim ke WhatsApp Anda.
        </p>
    </div>

    <!-- Booking Ticket Card -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 text-left space-y-4 shadow-xl relative overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Kode Booking</span>
                <div class="text-base font-extrabold text-amber-400">#BOOK-{{ $booking->id }}</div>
            </div>
            <div class="px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                {{ strtoupper($booking->status) }}
            </div>
        </div>

        <div class="space-y-2.5 text-xs">
            <div class="flex justify-between">
                <span class="text-slate-400">Nama Pelanggan:</span>
                <span class="font-bold text-white">{{ $booking->customer_name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Nomor WhatsApp:</span>
                <span class="font-bold text-slate-200">{{ $booking->customer_phone }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Layanan:</span>
                <span class="font-bold text-white">{{ $booking->service->name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Kapster:</span>
                <span class="font-bold text-amber-400">{{ $booking->kapster ? $booking->kapster->name : 'Siapa Saja' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Tanggal:</span>
                <span class="font-bold text-white">{{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('l, d F Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Jam Booking:</span>
                <span class="font-bold text-amber-400">{{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }} WIB</span>
            </div>
            <div class="flex justify-between pt-3 border-t border-slate-800 text-sm">
                <span class="text-slate-300 font-bold">Total Pembayaran:</span>
                <span class="font-extrabold text-amber-400">Rp {{ number_format($booking->service->price, 0, ',', '.') }}</span>
            </div>
        </div>

        @if($booking->notes)
            <div class="pt-2 border-t border-slate-800/80 text-[11px] text-slate-400">
                <span class="font-semibold text-slate-300">Catatan:</span> {{ $booking->notes }}
            </div>
        @endif
    </div>

    <!-- WhatsApp Notice Box -->
    <div class="p-4 rounded-xl bg-emerald-950/40 border border-emerald-500/30 flex items-start gap-3 text-left">
        <div class="text-2xl">💬</div>
        <div class="text-xs">
            <h4 class="font-bold text-emerald-300 mb-0.5">Notifikasi WhatsApp Otomatis</h4>
            <p class="text-emerald-400/90 leading-relaxed">
                Kami telah mengirim konfirmasi via WhatsApp ke <strong>{{ $booking->customer_phone }}</strong> dan akan mengirimkan reminder H-1 sebelum waktu booking Anda.
            </p>
        </div>
    </div>

    <!-- PWA Call to action box -->
    <div class="p-4 rounded-xl bg-gradient-to-r from-amber-500/20 to-amber-600/10 border border-amber-500/30 text-left space-y-2">
        <div class="flex items-center gap-2">
            <span class="text-xl">📲</span>
            <h4 class="font-bold text-white text-xs">Simpan ke Layar Utama HP Anda!</h4>
        </div>
        <p class="text-[11px] text-slate-400 leading-relaxed">
            Booking berikutnya bisa langsung dari icon aplikasi di HP Anda tanpa membuka browser ulang.
        </p>
        <button 
            type="button" 
            onclick="triggerPwaPrompt()" 
            class="w-full py-2.5 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition"
        >
            + Tambahkan ke Layar Utama
        </button>
    </div>

    <!-- Action Buttons -->
    <div class="space-y-2 pt-2">
        <a href="{{ route('booking.index') }}" class="block w-full py-3.5 rounded-xl font-bold text-sm bg-slate-900 border border-slate-800 text-slate-200 hover:bg-slate-800 transition">
            Booking Layanan Baru &rarr;
        </a>
        <a href="{{ route('booking.history', ['phone' => $booking->customer_phone]) }}" class="block text-xs text-amber-400 hover:underline pt-1">
            Lihat Riwayat Booking Saya
        </a>
    </div>

</div>

<script>
function triggerPwaPrompt() {
    if (deferredPrompt) {
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then((choice) => {
            if (choice.outcome === 'accepted') {
                console.log('User accepted PWA prompt');
            }
            deferredPrompt = null;
        });
    } else {
        alert('Untuk menambah ke Layar Utama:\n• Chrome: Klik titik tiga ⋮ lalu pilih "Add to Home Screen" atau "Install App".\n• Safari iOS: Klik ikon Share (kotak panah ke atas) lalu pilih "Add to Home Screen".');
    }
}
</script>
@endsection
