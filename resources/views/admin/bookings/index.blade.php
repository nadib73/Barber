@extends('layouts.admin')

@section('page_title', 'Kelola Jadwal & Booking')

@section('content')
<div x-data="{ showWalkInModal: false }" class="space-y-6">

    <!-- Filter & Action Bar -->
    <div class="bg-slate-950 border border-slate-800 p-4 rounded-2xl flex flex-col md:flex-row gap-4 items-center justify-between">
        
        <!-- Filter Form -->
        <form action="{{ route('admin.bookings.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <div>
                <input 
                    type="date" 
                    name="date" 
                    value="{{ $date }}" 
                    class="bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500"
                >
            </div>

            <div>
                <select name="kapster_id" class="bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                    <option value="">Semua Kapster</option>
                    @foreach($kapsters as $kap)
                        <option value="{{ $kap->id }}" {{ $kapsterId == $kap->id ? 'selected' : '' }}>{{ $kap->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="status" class="bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ $status == 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                    <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs transition">
                Filter
            </button>
            <a href="{{ route('admin.bookings.index') }}" class="text-xs text-slate-400 hover:underline">Reset</a>
        </form>

        <!-- Walk-in Booking Button -->
        <button 
            type="button" 
            @click="showWalkInModal = true" 
            class="w-full md:w-auto px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-amber-500/10"
        >
            <span>+</span> Tambah Walk-in (Manual)
        </button>
    </div>

    <!-- Bookings Table -->
    <div class="bg-slate-950 border border-slate-800 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900 text-slate-400 uppercase font-semibold text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">ID & Tanggal</th>
                        <th class="py-3 px-4">Jam</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Layanan</th>
                        <th class="py-3 px-4">Kapster</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-900">
                    @forelse($bookings as $b)
                        <tr class="hover:bg-slate-900/50 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-mono text-amber-400 font-bold">#BOOK-{{ $b->id }}</div>
                                <div class="text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($b->booking_date)->translatedFormat('d M Y') }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-white">
                                {{ \Carbon\Carbon::parse($b->booking_time)->format('H:i') }} WIB
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-white">{{ $b->customer_name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $b->customer_phone }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-slate-200">{{ $b->service ? $b->service->name : '-' }}</div>
                                <div class="text-[11px] text-amber-400">Rp {{ number_format($b->service ? $b->service->price : 0, 0, ',', '.') }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-300">
                                {{ $b->kapster ? $b->kapster->name : 'Siapa Saja' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <form action="{{ route('admin.bookings.update-status', $b->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select 
                                        name="status" 
                                        onchange="this.form.submit()" 
                                        class="bg-slate-900 border border-slate-800 rounded-lg px-2 py-1 text-[11px] font-bold focus:outline-none"
                                        :class="{
                                            'text-emerald-400': '{{ $b->status }}' === 'confirmed',
                                            'text-blue-400': '{{ $b->status }}' === 'completed',
                                            'text-rose-400': '{{ $b->status }}' === 'cancelled',
                                            'text-amber-400': '{{ $b->status }}' === 'pending'
                                        }"
                                    >
                                        <option value="pending" {{ $b->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="confirmed" {{ $b->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                        <option value="completed" {{ $b->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $b->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <form action="{{ route('admin.bookings.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Hapus booking ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded text-slate-500 hover:text-rose-400 hover:bg-slate-900 transition">
                                        🗑️
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-500 text-xs">
                                Tidak ada data booking yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
            <div class="p-4 border-t border-slate-900">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>

    <!-- Walk-In Booking Modal -->
    <div 
        x-show="showWalkInModal" 
        x-transition.opacity
        class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4"
    >
        <div 
            @click.away="showWalkInModal = false" 
            class="bg-slate-900 border border-slate-800 w-full max-w-md rounded-2xl p-6 space-y-4 shadow-2xl"
        >
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="font-bold text-white text-base">Tambah Walk-In Customer</h3>
                <button type="button" @click="showWalkInModal = false" class="text-slate-400 hover:text-white">✕</button>
            </div>

            <form action="{{ route('admin.bookings.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Nama Customer *</label>
                    <input type="text" name="customer_name" required placeholder="Nama customer..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-white focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Nomor WhatsApp *</label>
                    <input type="tel" name="customer_phone" required placeholder="081234567890" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-white focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Layanan *</label>
                    <select name="service_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-white focus:outline-none focus:border-amber-500">
                        @foreach($services as $srv)
                            <option value="{{ $srv->id }}">{{ $srv->name }} (Rp {{ number_format($srv->price, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Kapster *</label>
                    <select name="kapster_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-white focus:outline-none focus:border-amber-500">
                        @foreach($kapsters as $kap)
                            <option value="{{ $kap->id }}">{{ $kap->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Tanggal *</label>
                        <input type="date" name="booking_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Jam *</label>
                        <input type="time" name="booking_time" value="{{ date('H:i') }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-500">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Status Awal</label>
                    <select name="status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-500">
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed (Langsung Selesai)</option>
                    </select>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" @click="showWalkInModal = false" class="w-1/3 py-2.5 bg-slate-800 text-slate-300 rounded-xl font-bold">Batal</button>
                    <button type="submit" class="w-2/3 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl font-bold">Simpan Booking</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
