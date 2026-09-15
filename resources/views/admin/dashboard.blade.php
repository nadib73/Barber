@extends('layouts.admin')

@section('page_title', 'Dashboard Overview')

@section('content')
<div class="space-y-6">

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-slate-950 border border-slate-800 p-5 rounded-2xl space-y-1">
            <span class="text-xs font-semibold text-slate-400">Booking Hari Ini</span>
            <div class="text-2xl font-black text-white">{{ $stats['today_count'] }}</div>
            <span class="text-[10px] text-amber-400 font-medium">📅 {{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}</span>
        </div>

        <div class="bg-slate-950 border border-slate-800 p-5 rounded-2xl space-y-1">
            <span class="text-xs font-semibold text-slate-400">Estimasi Omset Hari Ini</span>
            <div class="text-2xl font-black text-amber-400">Rp {{ number_format($stats['today_revenue'], 0, ',', '.') }}</div>
            <span class="text-[10px] text-emerald-400 font-medium">💰 Total dari booking aktif</span>
        </div>

        <div class="bg-slate-950 border border-slate-800 p-5 rounded-2xl space-y-1">
            <span class="text-xs font-semibold text-slate-400">Kapster Aktif</span>
            <div class="text-2xl font-black text-white">{{ $stats['total_kapsters'] }}</div>
            <span class="text-[10px] text-slate-500 font-medium">💈 Siap melayani</span>
        </div>

        <div class="bg-slate-950 border border-slate-800 p-5 rounded-2xl space-y-1">
            <span class="text-xs font-semibold text-slate-400">Layanan Aktif</span>
            <div class="text-2xl font-black text-white">{{ $stats['total_services'] }}</div>
            <span class="text-[10px] text-slate-500 font-medium">✂️ Pilihan menu potong</span>
        </div>
    </div>

    <!-- Today's Schedule Table -->
    <div class="bg-slate-950 border border-slate-800 rounded-2xl p-6 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-white">Jadwal Potong Rambut Hari Ini</h2>
                <p class="text-xs text-slate-400">Daftar antrean dan reservasi pelanggan hari ini</p>
            </div>
            <a href="{{ route('admin.bookings.index') }}" class="text-xs font-bold text-amber-400 hover:underline">
                Lihat Semua Booking &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900 text-slate-400 uppercase font-semibold text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Jam</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Kapster</th>
                        <th class="py-3 px-4">Layanan</th>
                        <th class="py-3 px-4">Harga</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-900">
                    @forelse($todayBookings as $b)
                        <tr class="hover:bg-slate-900/50 transition">
                            <td class="py-3.5 px-4 font-bold text-amber-400">
                                {{ \Carbon\Carbon::parse($b->booking_time)->format('H:i') }} WIB
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-white">{{ $b->customer_name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $b->customer_phone }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-200">
                                ✂️ {{ $b->kapster ? $b->kapster->name : 'Siapa Saja' }}
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-200">
                                {{ $b->service ? $b->service->name : '-' }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-200">
                                Rp {{ number_format($b->service ? $b->service->price : 0, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($b->status === 'confirmed')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Dikonfirmasi</span>
                                @elseif($b->status === 'completed')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-500/20 text-blue-400 border border-blue-500/30">Selesai</span>
                                @elseif($b->status === 'cancelled')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">Batal</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">Menunggu</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <form action="{{ route('admin.bookings.update-status', $b->id) }}" method="POST" class="inline-flex gap-1">
                                    @csrf
                                    @method('PATCH')
                                    @if($b->status !== 'completed')
                                        <button type="submit" name="status" value="completed" title="Tandai Selesai" class="px-2 py-1 rounded bg-blue-500/20 text-blue-400 hover:bg-blue-500 hover:text-white font-bold text-[10px] transition">
                                            ✓ Selesai
                                        </button>
                                    @endif
                                    @if($b->status !== 'cancelled')
                                        <button type="submit" name="status" value="cancelled" title="Batalkan" class="px-2 py-1 rounded bg-rose-500/20 text-rose-400 hover:bg-rose-500 hover:text-white font-bold text-[10px] transition">
                                            ✕ Batal
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500 text-xs">
                                Belum ada booking terdaftar untuk hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
