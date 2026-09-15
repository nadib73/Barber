@extends('layouts.barber')

@section('title', 'Booking Barbershop - BarberBook')

@section('content')
<div x-data="bookingApp()" x-init="init()" class="space-y-6">

    <!-- Hero Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500/20 via-slate-900 to-slate-950 p-5 border border-amber-500/30">
        <div class="relative z-10">
            <span class="inline-block px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-400 text-[11px] font-bold tracking-wide uppercase mb-2 border border-amber-500/30">
                Self Booking Online
            </span>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Booking Potong Rambut</h1>
            <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                Pilih kapster & jam favoritmu tanpa antre. Konfirmasi & pengingat otomatis via WhatsApp!
            </p>
        </div>
        <div class="absolute -right-4 -bottom-6 text-7xl opacity-10 select-none">💈</div>
    </div>

    <!-- Booking Multi-step Form -->
    <form action="{{ route('booking.store') }}" method="POST" @submit="handleSubmit($event)">
        @csrf

        <!-- Hidden Inputs for Form Submission -->
        <input type="hidden" name="service_id" :value="selectedService?.id">
        <input type="hidden" name="kapster_id" :value="selectedKapster">
        <input type="hidden" name="booking_date" :value="selectedDate">
        <input type="hidden" name="booking_time" :value="selectedSlot">

        <!-- Progress Indicators -->
        <div class="flex items-center justify-between px-2 mb-6 text-xs font-semibold">
            <div class="flex items-center gap-1.5" :class="step >= 1 ? 'text-amber-400' : 'text-slate-600'">
                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs" :class="step >= 1 ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-slate-800 text-slate-500'">1</span>
                <span>Layanan</span>
            </div>
            <div class="h-0.5 flex-1 mx-2 bg-slate-800" :class="step >= 2 ? 'bg-amber-500/50' : ''"></div>
            <div class="flex items-center gap-1.5" :class="step >= 2 ? 'text-amber-400' : 'text-slate-600'">
                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs" :class="step >= 2 ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-slate-800 text-slate-500'">2</span>
                <span>Kapster</span>
            </div>
            <div class="h-0.5 flex-1 mx-2 bg-slate-800" :class="step >= 3 ? 'bg-amber-500/50' : ''"></div>
            <div class="flex items-center gap-1.5" :class="step >= 3 ? 'text-amber-400' : 'text-slate-600'">
                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs" :class="step >= 3 ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-slate-800 text-slate-500'">3</span>
                <span>Waktu</span>
            </div>
            <div class="h-0.5 flex-1 mx-2 bg-slate-800" :class="step >= 4 ? 'bg-amber-500/50' : ''"></div>
            <div class="flex items-center gap-1.5" :class="step >= 4 ? 'text-amber-400' : 'text-slate-600'">
                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs" :class="step >= 4 ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-slate-800 text-slate-500'">4</span>
                <span>Data</span>
            </div>
        </div>

        <!-- STEP 1: PILIH LAYANAN -->
        <div x-show="step === 1" x-transition.opacity class="space-y-4">
            <h2 class="text-sm font-bold text-slate-200 uppercase tracking-wider">1. Pilih Layanan</h2>

            <div class="space-y-2.5">
                @foreach($services as $service)
                    <div 
                        @click="selectService({{ json_encode($service) }})"
                        class="p-4 rounded-xl border transition cursor-pointer flex items-center justify-between group"
                        :class="selectedService?.id === {{ $service->id }} ? 'bg-amber-500/10 border-amber-500 shadow-lg shadow-amber-500/10' : 'bg-slate-900 border-slate-800 hover:border-slate-700'"
                    >
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-sm text-white group-hover:text-amber-400 transition">{{ $service->name }}</h3>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-800 text-slate-400">⏱️ {{ $service->duration_minutes }}m</span>
                            </div>
                            <p class="text-xs text-slate-400 line-clamp-1">{{ $service->description ?? 'Layanan profesional dengan kapster berpengalaman.' }}</p>
                        </div>
                        <div class="text-right pl-3">
                            <span class="text-sm font-extrabold text-amber-400">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <button 
                type="button" 
                @click="step = 2" 
                :disabled="!selectedService"
                class="w-full mt-4 py-3.5 rounded-xl font-bold text-sm bg-amber-500 text-slate-950 hover:bg-amber-400 transition disabled:opacity-40 disabled:cursor-not-allowed shadow-lg shadow-amber-500/20"
            >
                Lanjut ke Pilih Kapster &rarr;
            </button>
        </div>

        <!-- STEP 2: PILIH KAPSTER -->
        <div x-show="step === 2" x-transition.opacity class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-200 uppercase tracking-wider">2. Pilih Kapster</h2>
                <button type="button" @click="step = 1" class="text-xs text-amber-400 hover:underline">&larr; Ubah Layanan</button>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <!-- Option "Siapa Saja" -->
                <div 
                    @click="selectedKapster = 'any'; onKapsterChanged()"
                    class="p-4 rounded-xl border text-center cursor-pointer transition flex flex-col items-center justify-center gap-2"
                    :class="selectedKapster === 'any' ? 'bg-amber-500/10 border-amber-500 shadow-md shadow-amber-500/10' : 'bg-slate-900 border-slate-800 hover:border-slate-700'"
                >
                    <div class="w-14 h-14 rounded-full bg-slate-800 flex items-center justify-center text-2xl border border-slate-700">
                        ⚡
                    </div>
                    <div>
                        <div class="font-bold text-xs text-white">Siapa Saja</div>
                        <div class="text-[10px] text-slate-400">Jadwal Tercepat</div>
                    </div>
                </div>

                <!-- Kapster Cards -->
                @foreach($kapsters as $kapster)
                    <div 
                        @click="selectedKapster = '{{ $kapster->id }}'; onKapsterChanged()"
                        class="p-4 rounded-xl border text-center cursor-pointer transition flex flex-col items-center justify-center gap-2"
                        :class="selectedKapster === '{{ $kapster->id }}' ? 'bg-amber-500/10 border-amber-500 shadow-md shadow-amber-500/10' : 'bg-slate-900 border-slate-800 hover:border-slate-700'"
                    >
                        <img src="{{ $kapster->photo_url ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80' }}" alt="{{ $kapster->name }}" class="w-14 h-14 rounded-full object-cover border-2 border-slate-700">
                        <div>
                            <div class="font-bold text-xs text-white">{{ $kapster->name }}</div>
                            <div class="text-[10px] text-amber-400 font-medium">⭐ Master Barber</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex gap-2 mt-4">
                <button type="button" @click="step = 1" class="w-1/3 py-3 rounded-xl font-bold text-xs bg-slate-800 text-slate-300 hover:bg-slate-700 transition">
                    Kembali
                </button>
                <button 
                    type="button" 
                    @click="step = 3; fetchSlots()" 
                    :disabled="!selectedKapster"
                    class="w-2/3 py-3.5 rounded-xl font-bold text-sm bg-amber-500 text-slate-950 hover:bg-amber-400 transition disabled:opacity-40 disabled:cursor-not-allowed shadow-lg shadow-amber-500/20"
                >
                    Lanjut Pilih Jadwal &rarr;
                </button>
            </div>
        </div>

        <!-- STEP 3: PILIH TANGGAL & SLOT WAKTU -->
        <div x-show="step === 3" x-transition.opacity class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-200 uppercase tracking-wider">3. Tanggal & Jam</h2>
                <button type="button" @click="step = 2" class="text-xs text-amber-400 hover:underline">&larr; Ubah Kapster</button>
            </div>

            <!-- Date Selector -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Pilih Tanggal Booking:</label>
                <input 
                    type="date" 
                    x-model="selectedDate" 
                    @change="fetchSlots()" 
                    min="{{ date('Y-m-d') }}"
                    class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-amber-500 transition"
                >
            </div>

            <!-- Time Slots Section -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-semibold text-slate-300">Pilih Slot Jam Tersedia:</label>
                    <span x-show="loadingSlots" class="text-[11px] text-amber-400 animate-pulse">Memuat slot...</span>
                </div>

                <!-- Empty or Loading State -->
                <div x-show="!loadingSlots && availableSlots.length === 0" class="p-6 text-center bg-slate-900 rounded-xl border border-slate-800 text-slate-400 text-xs">
                    Tidak ada slot tersedia di tanggal ini atau barbershop tutup. Silakan pilih tanggal lain.
                </div>

                <!-- Slots Grid -->
                <div x-show="availableSlots.length > 0" class="grid grid-cols-4 gap-2">
                    <template x-for="slot in availableSlots" :key="slot.time">
                        <button
                            type="button"
                            @click="if(slot.available) selectedSlot = slot.time"
                            :disabled="!slot.available"
                            class="py-2.5 px-1 rounded-xl text-xs font-semibold transition flex flex-col items-center justify-center border"
                            :class="{
                                'bg-amber-500 text-slate-950 font-bold border-amber-500 shadow-md shadow-amber-500/20': selectedSlot === slot.time,
                                'bg-slate-900 text-slate-200 border-slate-800 hover:border-amber-500/50': slot.available && selectedSlot !== slot.time,
                                'bg-slate-900/30 text-slate-600 border-slate-800/40 cursor-not-allowed line-through': !slot.available
                            }"
                        >
                            <span x-text="slot.time"></span>
                        </button>
                    </template>
                </div>
            </div>

            <div class="flex gap-2 mt-4">
                <button type="button" @click="step = 2" class="w-1/3 py-3 rounded-xl font-bold text-xs bg-slate-800 text-slate-300 hover:bg-slate-700 transition">
                    Kembali
                </button>
                <button 
                    type="button" 
                    @click="step = 4" 
                    :disabled="!selectedDate || !selectedSlot"
                    class="w-2/3 py-3.5 rounded-xl font-bold text-sm bg-amber-500 text-slate-950 hover:bg-amber-400 transition disabled:opacity-40 disabled:cursor-not-allowed shadow-lg shadow-amber-500/20"
                >
                    Lanjut Isi Data Diri &rarr;
                </button>
            </div>
        </div>

        <!-- STEP 4: DATA DIRI & KONFIRMASI -->
        <div x-show="step === 4" x-transition.opacity class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-200 uppercase tracking-wider">4. Data Diri & Konfirmasi</h2>
                <button type="button" @click="step = 3" class="text-xs text-amber-400 hover:underline">&larr; Ubah Waktu</button>
            </div>

            <!-- Booking Summary Card -->
            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-400">Layanan:</span>
                    <span class="font-bold text-white" x-text="selectedService?.name"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Kapster:</span>
                    <span class="font-bold text-amber-400" x-text="getKapsterName()"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Jadwal:</span>
                    <span class="font-bold text-white" x-text="selectedDate + ' - Jam ' + selectedSlot + ' WIB'"></span>
                </div>
                <div class="flex justify-between pt-2 border-t border-slate-800 text-sm">
                    <span class="text-slate-300 font-bold">Total Harga:</span>
                    <span class="font-extrabold text-amber-400" x-text="'Rp ' + Number(selectedService?.price || 0).toLocaleString('id-ID')"></span>
                </div>
            </div>

            <!-- Customer Form Input -->
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Lengkap *</label>
                    <input 
                        type="text" 
                        name="customer_name" 
                        required 
                        value="{{ auth()->user()?->name ?? old('customer_name') }}"
                        placeholder="Contoh: Budi Gunawan" 
                        class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-amber-500 transition"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nomor WhatsApp Aktif *</label>
                    <input 
                        type="tel" 
                        name="customer_phone" 
                        required 
                        value="{{ auth()->user()?->phone ?? old('customer_phone') }}"
                        placeholder="Contoh: 081234567890" 
                        class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-amber-500 transition"
                    >
                    <p class="text-[11px] text-slate-400 mt-1">Konfirmasi & reminder jadwal akan otomatis dikirimkan ke nomor ini.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Catatan Tambahan (Opsional)</label>
                    <textarea 
                        name="notes" 
                        rows="2" 
                        placeholder="Contoh: Model undercut, bawa anak kecil, dll." 
                        class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-amber-500 transition"
                    >{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="flex gap-2 mt-4">
                <button type="button" @click="step = 3" class="w-1/3 py-3 rounded-xl font-bold text-xs bg-slate-800 text-slate-300 hover:bg-slate-700 transition">
                    Kembali
                </button>
                <button 
                    type="submit" 
                    class="w-2/3 py-3.5 rounded-xl font-bold text-sm bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 hover:from-amber-400 hover:to-amber-500 transition shadow-lg shadow-amber-500/25"
                >
                    Konfirmasi Booking Sekarang 🔥
                </button>
            </div>
        </div>

    </form>
</div>

<script>
function bookingApp() {
    return {
        step: 1,
        selectedService: null,
        selectedKapster: 'any',
        selectedDate: '{{ date('Y-m-d') }}',
        selectedSlot: '',
        availableSlots: [],
        loadingSlots: false,
        kapstersList: @json($kapsters),

        init() {
            // Default first service if exists
            const services = @json($services);
            if (services.length > 0) {
                this.selectedService = services[0];
            }
        },

        selectService(service) {
            this.selectedService = service;
        },

        onKapsterChanged() {
            this.selectedSlot = '';
            if (this.step === 3) {
                this.fetchSlots();
            }
        },

        getKapsterName() {
            if (this.selectedKapster === 'any') return 'Siapa Saja (Tersedia)';
            const k = this.kapstersList.find(x => x.id == this.selectedKapster);
            return k ? k.name : 'Siapa Saja';
        },

        async fetchSlots() {
            if (!this.selectedService || !this.selectedDate) return;
            this.loadingSlots = true;
            this.availableSlots = [];
            this.selectedSlot = '';

            try {
                const url = `/api/available-slots?date=${encodeURIComponent(this.selectedDate)}&service_id=${this.selectedService.id}&kapster_id=${encodeURIComponent(this.selectedKapster)}`;
                const res = await fetch(url);
                const data = await res.json();
                this.availableSlots = data.slots || [];
            } catch (err) {
                console.error('Error fetching slots', err);
            } finally {
                this.loadingSlots = false;
            }
        },

        handleSubmit(event) {
            if (!this.selectedService || !this.selectedDate || !this.selectedSlot) {
                event.preventDefault();
                alert('Silakan lengkapi pilihan layanan, tanggal, dan slot jam!');
            }
        }
    }
}
</script>
@endsection
