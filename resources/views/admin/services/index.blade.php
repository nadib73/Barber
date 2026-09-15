@extends('layouts.admin')

@section('page_title', 'Kelola Menu & Layanan')

@section('content')
<div x-data="{ showAddModal: false, editService: null, showEditModal: false }" class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-white">Daftar Layanan Barbershop</h2>
            <p class="text-xs text-slate-400">Atur menu perawatan rambut, tarif harga, dan estimasi durasi pengerjaan</p>
        </div>
        <button 
            type="button" 
            @click="showAddModal = true" 
            class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition flex items-center gap-1.5 shadow-lg shadow-amber-500/10"
        >
            <span>+</span> Tambah Layanan
        </button>
    </div>

    <!-- Services Table -->
    <div class="bg-slate-950 border border-slate-800 rounded-2xl overflow-hidden">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-900 text-slate-400 uppercase font-semibold text-[10px] border-b border-slate-800">
                <tr>
                    <th class="py-3 px-4">Nama Layanan</th>
                    <th class="py-3 px-4">Harga (Rp)</th>
                    <th class="py-3 px-4">Durasi</th>
                    <th class="py-3 px-4">Deskripsi Singkat</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-900">
                @forelse($services as $s)
                    <tr class="hover:bg-slate-900/50 transition">
                        <td class="py-3.5 px-4 font-bold text-white">
                            {{ $s->name }}
                        </td>
                        <td class="py-3.5 px-4 font-extrabold text-amber-400">
                            Rp {{ number_format($s->price, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4 font-medium text-slate-300">
                            ⏱️ {{ $s->duration_minutes }} Menit
                        </td>
                        <td class="py-3.5 px-4 text-slate-400 max-w-xs truncate">
                            {{ $s->description ?? '-' }}
                        </td>
                        <td class="py-3.5 px-4 text-right space-x-1">
                            <button 
                                type="button" 
                                @click="editService = {{ json_encode($s) }}; showEditModal = true"
                                class="px-2.5 py-1 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-amber-400 font-bold text-[11px]"
                            >
                                Edit
                            </button>
                            <form action="{{ route('admin.services.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus layanan ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 rounded-lg text-slate-500 hover:text-rose-400">
                                    🗑️
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-500">
                            Belum ada layanan terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah Layanan -->
    <div x-show="showAddModal" x-transition.opacity class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.away="showAddModal = false" class="bg-slate-900 border border-slate-800 w-full max-w-md rounded-2xl p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="font-bold text-white text-base">Tambah Layanan Baru</h3>
                <button type="button" @click="showAddModal = false" class="text-slate-400 hover:text-white">✕</button>
            </div>

            <form action="{{ route('admin.services.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Nama Layanan *</label>
                    <input type="text" name="name" required placeholder="Contoh: Premium Fade Haircut" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-white focus:outline-none focus:border-amber-500">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Harga (Rp) *</label>
                        <input type="number" name="price" required min="0" step="1000" placeholder="50000" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-white focus:outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Durasi (Menit) *</label>
                        <input type="number" name="duration_minutes" required min="5" step="5" value="45" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-white focus:outline-none focus:border-amber-500">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Deskripsi Singkat</label>
                    <textarea name="description" rows="2" placeholder="Detail proses atau produk yang didapat..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-500"></textarea>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" @click="showAddModal = false" class="w-1/3 py-2.5 bg-slate-800 text-slate-300 rounded-xl font-bold">Batal</button>
                    <button type="submit" class="w-2/3 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl font-bold">Simpan Layanan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Layanan -->
    <div x-show="showEditModal" x-transition.opacity class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 w-full max-w-md rounded-2xl p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="font-bold text-white text-base">Edit Layanan</h3>
                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-white">✕</button>
            </div>

            <form :action="`/admin/services/${editService?.id}`" method="POST" class="space-y-3 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Nama Layanan *</label>
                    <input type="text" name="name" :value="editService?.name" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-white focus:outline-none focus:border-amber-500">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Harga (Rp) *</label>
                        <input type="number" name="price" :value="editService?.price" required min="0" step="1000" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-white focus:outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Durasi (Menit) *</label>
                        <input type="number" name="duration_minutes" :value="editService?.duration_minutes" required min="5" step="5" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-white focus:outline-none focus:border-amber-500">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Deskripsi Singkat</label>
                    <textarea name="description" rows="2" x-text="editService?.description" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-amber-500"></textarea>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" @click="showEditModal = false" class="w-1/3 py-2.5 bg-slate-800 text-slate-300 rounded-xl font-bold">Batal</button>
                    <button type="submit" class="w-2/3 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl font-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
