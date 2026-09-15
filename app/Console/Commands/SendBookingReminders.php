<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendBookingReminders extends Command
{
    protected $signature = 'barber:send-reminders';
    protected $description = 'Kirim pesan pengingat booking (H-1) via WhatsApp ke pelanggan';

    public function handle(FonnteService $fonnteService)
    {
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        $bookings = Booking::with(['service', 'kapster'])
            ->where('booking_date', $tomorrow)
            ->where('status', 'confirmed')
            ->get();

        $this->info("Ditemukan {$bookings->count()} booking untuk besok ({$tomorrow}).");

        foreach ($bookings as $booking) {
            $formattedTime = Carbon::parse($booking->booking_time)->format('H:i');
            $kapsterName = $booking->kapster ? $booking->kapster->name : 'Staff Barbershop';
            $serviceName = $booking->service ? $booking->service->name : 'Layanan';

            $message = "Halo *{$booking->customer_name}*,\n\n" .
                "Pengingat jadwal potong rambut Anda *BESOK* di *BarberBook* ✂️\n\n" .
                "🗓️ Tanggal: Besok (" . Carbon::parse($booking->booking_date)->translatedFormat('l, d F Y') . ")\n" .
                "⏰ Jam: {$formattedTime} WIB\n" .
                "💈 Layanan: {$serviceName}\n" .
                "✂️ Kapster: {$kapsterName}\n\n" .
                "Mohon hadir tepat waktu ya. Sampai bertemu besok! 🔥";

            $fonnteService->sendMessage($booking->customer_phone, $message);
            $this->info("Reminder terkirim ke {$booking->customer_phone} ({$booking->customer_name}).");
        }

        $this->info("Semua reminder berhasil diproses.");
        return Command::SUCCESS;
    }
}
