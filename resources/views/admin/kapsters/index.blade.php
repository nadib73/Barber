@extends('layouts.admin')

@section('page_title', 'Kelola Kapster & Shift Kerja')

@section('content')
<div x-data="{ showAddModal: false, selectedKapster: null, showScheduleModal: false }" class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-white">Daftar Kapster (Tukang Cukur)</h2>
            <p class="text-xs text-slate-400">Kelola informasi pemotong rambut dan pengaturan jadwal kerjanya</p>
        </div>
        <button 
            type="button" 
            @click="showAddModal = true" 
            class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition flex items-center gap-1.5 shadow-lg shadow-amber-500/10"
        >
            <span>+</span> Tambah Kapster
        </button>
    </div>

    <!-- Kapsters Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @forelse($kapsters as $k)
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-5 space-y-4 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <img src="{{ $k->photo_url ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80' }}" alt="{{ $k->name }}" class="w-12 h-12 rounded-full object-cover border-2 border-slate-800">
                            <div>
                                <h3 class="font-bold text-sm text-white">{{ $k->name }}</h3>
                                <p class="text-xs text-slate-400">{{ $k->phone ?? 'Tanpa Nomor' }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $k->is_active ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-500' }}">
                            {{ $k->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800/80 text-xs space-y-1">
                        <span class="text-[10px] uppercase font-bold text-slate-400">Jadwal Shift:</span>
                        <div class="text-slate-300 text-[11px]">
                            @php
                                $days = [0 => 'Min', 1 => 'Sen', 2 => 'Sel', 3 => 'Rab', 4 => 'Kam', 5 => 'Jum', 6 => 'Sab'];
                                $activeDays = $k->schedules->pluck('day_of_week')->map(fn($d) => $days[$d] ?? $d)->implode(', ');
                            @endphp
                            {{ $activeDays ?: 'Belum diatur jadwalnya' }}
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 pt-2 border-t border-slate-900">
                    <button 
                        type="button" 
                        @click="selectedKapster = {{ json_encode($k) }}; showScheduleModal = true"
                        class="flex-1 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-xs font-semibold text-amber-400 transition text-center"
                    >
                        ⚙️ Atur Jadwal
                    </button>
                    <form action="{{ route('admin.kapsters.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Hapus kapster ini?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 rounded-xl bg-slate-900 hover:bg-rose-500/20 text-slate-500 hover:text-rose-400 border border-slate-800 transition">
                            🗑️
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-3 p-10 text-center bg-slate-950 border border-slate-800 rounded-2xl text-slate-500 text-xs">
                Belum ada data kapster. Klik tombol di atas untuk menambah kapster.
            </div>
        @endforelse
    </div>

    <!-- Modal Tambah Kapster -->
    <div x-show="showAddModal" x-transition.opacity class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.away="showAddModal = false" class="bg-slate-900 border border-slate-800 w-full max-w-md rounded-2xl p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="font-bold text-white text-base">Tambah Kapster Baru</h3>
                <button type="button" @click="showAddModal = false" class="text-slate-400 hover:text-white">✕</button>
            </div>

            <form action="{{ route('admin.kapsters.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Nama Lengkap *</label>
                    <input type="text" name="name" required placeholder="Nama kapster..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-white focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Nomor Kontak / WA</label>
                    <input type="tel" name="phone" placeholder="08123456789..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-white focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">URL Foto (Opsional)</label>
                    <input type="url" name="photo_url" placeholder="https://..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-white focus:outline-none focus:border-amber-500">
                </div>

                <div class="flex gap-2 pt-3">
                    <button type="button" @click="showAddModal = false" class="w-1/3 py-2.5 bg-slate-800 text-slate-300 rounded-xl font-bold">Batal</button>
                    <button type="submit" class="w-2/3 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl font-bold">Simpan Kapster</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Atur Jadwal Shift Kerja -->
    <div x-show="showScheduleModal" x-transition.opacity class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.away="showScheduleModal = false" class="bg-slate-900 border border-slate-800 w-full max-w-lg rounded-2xl p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="font-bold text-white text-base">Atur Jadwal: <span class="text-amber-400" x-text="selectedKapster?.name"></span></h3>
                <button type="button" @click="showScheduleModal = false" class="text-slate-400 hover:text-white">✕</button>
            </div>

            <form :action="`/admin/kapsters/${selectedKapster?.id}/schedule`" method="POST" class="space-y-3 text-xs">
                @csrf
                <div class="space-y-2 max-h-[60vh] overflow-y-auto pr-1">
                    @php
                        $dayLabels = [
                            1 => 'Senin',
                            2 => 'Selasa',
                            3 => 'Rabu',
                            4 => 'Kamis',
                            5 => 'Jumat',
                            6 => 'Sabtu',
                            0 => 'Minggu',
                        ];
                    @endphp

                    @foreach($dayLabels as $dayIndex => $label)
                        <div class="p-3 bg-slate-950 rounded-xl border border-slate-800 flex items-center justify-between gap-3">
                            <label class="flex items-center gap-2 cursor-pointer w-24">
                                <input type="checkbox" name="schedules[{{ $dayIndex }}][active]" value="1" checked class="rounded bg-slate-900 border-slate-800 text-amber-500 focus:ring-0">
                                <span class="font-bold text-white">{{ $label }}</span>
                            </label>

                            <div class="flex items-center gap-2">
                                <input type="time" name="schedules[{{ $dayIndex }}][start_time]" value="10:00" class="bg-slate-900 border border-slate-800 rounded-lg px-2 py-1 text-white">
                                <span class="text-slate-500">-</span>
                                <input type="time" name="schedules[{{ $dayIndex }}][end_time]" value="21:00" class="bg-slate-900 border border-slate-800 rounded-lg px-2 py-1 text-white">
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" @click="showScheduleModal = false" class="w-1/3 py-2.5 bg-slate-800 text-slate-300 rounded-xl font-bold">Batal</button>
                    <button type="submit" class="w-2/3 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl font-bold">Simpan Perubahan Jadwal</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
