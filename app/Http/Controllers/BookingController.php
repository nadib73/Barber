<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Kapster;
use App\Models\KapsterSchedule;
use App\Models\Service;
use App\Models\User;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    protected FonnteService $fonnteService;

    public function __construct(FonnteService $fonnteService)
    {
        $this->fonnteService = $fonnteService;
    }

    /**
     * Booking page view
     */
    public function index()
    {
        $services = Service::all();
        $kapsters = Kapster::where('is_active', true)->get();

        return view('booking.index', compact('services', 'kapsters'));
    }

    /**
     * Check available time slots via AJAX
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'service_id' => 'required|exists:services,id',
            'kapster_id' => 'nullable',
        ]);

        $date = Carbon::parse($request->date);
        $dayOfWeek = $date->dayOfWeek; // 0 = Sunday ... 6 = Saturday
        $service = Service::findOrFail($request->service_id);
        $kapsterId = $request->kapster_id === 'any' ? null : $request->kapster_id;

        $slots = [];
        $startTime = Carbon::createFromTimeString('10:00:00');
        $endTime = Carbon::createFromTimeString('21:00:00');

        // Check active kapsters
        $kapstersQuery = Kapster::where('is_active', true);
        if ($kapsterId) {
            $kapstersQuery->where('id', $kapsterId);
        }
        $kapsters = $kapstersQuery->get();

        if ($kapsters->isEmpty()) {
            return response()->json(['slots' => []]);
        }

        $currentTime = $startTime->copy();
        $today = Carbon::today();
        $isToday = $date->isToday();
        $now = Carbon::now();

        while ($currentTime->lt($endTime)) {
            $slotTimeString = $currentTime->format('H:i');
            $slotFullTime = $date->copy()->setTimeFromTimeString($slotTimeString);

            // Skip past times if booking for today
            if ($isToday && $slotFullTime->lt($now->addMinutes(15))) {
                $currentTime->addMinutes(30);
                continue;
            }

            $isAvailable = false;

            foreach ($kapsters as $kapster) {
                // Check if kapster works on this day
                $schedule = KapsterSchedule::where('kapster_id', $kapster->id)
                    ->where('day_of_week', $dayOfWeek)
                    ->first();

                if (!$schedule) {
                    continue;
                }

                $schedStart = Carbon::createFromTimeString($schedule->start_time);
                $schedEnd = Carbon::createFromTimeString($schedule->end_time);

                if ($currentTime->lt($schedStart) || $currentTime->gte($schedEnd)) {
                    continue;
                }

                // Check conflict with existing non-cancelled bookings
                $hasConflict = Booking::where('kapster_id', $kapster->id)
                    ->where('booking_date', $request->date)
                    ->where('booking_time', $slotTimeString . ':00')
                    ->where('status', '!=', 'cancelled')
                    ->exists();

                if (!$hasConflict) {
                    $isAvailable = true;
                    break;
                }
            }

            $slots[] = [
                'time' => $slotTimeString,
                'available' => $isAvailable,
            ];

            $currentTime->addMinutes(30);
        }

        return response()->json(['slots' => $slots]);
    }

    /**
     * Store new booking
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'kapster_id' => 'nullable',
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:255',
        ]);

        $date = Carbon::parse($validated['booking_date']);
        $dayOfWeek = $date->dayOfWeek;
        $slotTimeString = $validated['booking_time'] . ':00';
        $requestedKapsterId = ($validated['kapster_id'] === 'any' || empty($validated['kapster_id'])) ? null : $validated['kapster_id'];

        // Assign kapster
        $assignedKapster = null;

        if ($requestedKapsterId) {
            $kapster = Kapster::where('id', $requestedKapsterId)->where('is_active', true)->firstOrFail();
            // Check conflict
            $conflict = Booking::where('kapster_id', $kapster->id)
                ->where('booking_date', $validated['booking_date'])
                ->where('booking_time', $slotTimeString)
                ->where('status', '!=', 'cancelled')
                ->exists();

            if ($conflict) {
                return back()->withInput()->withErrors(['booking_time' => 'Slot waktu tersebut baru saja dipesan orang lain. Silakan pilih waktu lain.']);
            }
            $assignedKapster = $kapster;
        } else {
            // Find any available active kapster
            $activeKapsters = Kapster::where('is_active', true)->get();
            foreach ($activeKapsters as $kapster) {
                $sched = KapsterSchedule::where('kapster_id', $kapster->id)
                    ->where('day_of_week', $dayOfWeek)
                    ->first();

                if (!$sched) continue;

                $conflict = Booking::where('kapster_id', $kapster->id)
                    ->where('booking_date', $validated['booking_date'])
                    ->where('booking_time', $slotTimeString)
                    ->where('status', '!=', 'cancelled')
                    ->exists();

                if (!$conflict) {
                    $assignedKapster = $kapster;
                    break;
                }
            }

            if (!$assignedKapster) {
                return back()->withInput()->withErrors(['booking_time' => 'Maaf, semua kapster penuh pada waktu ini. Silakan pilih waktu lain.']);
            }
        }

        // Customer ID if logged in or find user by phone
        $customerId = Auth::id();
        if (!$customerId) {
            $user = User::where('phone', $validated['customer_phone'])->first();
            if ($user) {
                $customerId = $user->id;
            }
        }

        $booking = Booking::create([
            'customer_id' => $customerId,
            'kapster_id' => $assignedKapster->id,
            'service_id' => $validated['service_id'],
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'booking_date' => $validated['booking_date'],
            'booking_time' => $slotTimeString,
            'status' => 'confirmed',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Send WhatsApp Notification via Fonnte
        $service = Service::find($validated['service_id']);
        $formattedDate = Carbon::parse($booking->booking_date)->translatedFormat('l, d F Y');
        $formattedTime = Carbon::parse($booking->booking_time)->format('H:i');

        $message = "Halo *{$booking->customer_name}*,\n\n" .
            "Booking barbershop Anda *BERHASIL* Disimpan! ✂️\n\n" .
            "📋 *Detail Booking:*\n" .
            "• Kode Booking: #BOOK-{$booking->id}\n" .
            "• Layanan: {$service->name}\n" .
            "• Kapster: {$assignedKapster->name}\n" .
            "• Tanggal: {$formattedDate}\n" .
            "• Jam: {$formattedTime} WIB\n" .
            "• Total: Rp " . number_format($service->price, 0, ',', '.') . "\n\n" .
            "Mohon datang 5-10 menit sebelum waktu booking.\n" .
            "Terima kasih telah memilih *BarberBook*! 🔥";

        $this->fonnteService->sendMessage($booking->customer_phone, $message);

        return redirect()->route('booking.success', $booking->id);
    }

    /**
     * Booking Success View
     */
    public function success($id)
    {
        $booking = Booking::with(['service', 'kapster'])->findOrFail($id);
        return view('booking.success', compact('booking'));
    }

    /**
     * Customer Booking History
     */
    public function history(Request $request)
    {
        $phone = $request->get('phone');
        $bookings = collect();

        if (Auth::check()) {
            $bookings = Booking::where('customer_id', Auth::id())
                ->with(['service', 'kapster'])
                ->latest()
                ->get();
        } elseif ($phone) {
            $bookings = Booking::where('customer_phone', $phone)
                ->with(['service', 'kapster'])
                ->latest()
                ->get();
        }

        return view('booking.history', compact('bookings', 'phone'));
    }
}
