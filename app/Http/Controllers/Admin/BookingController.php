<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Kapster;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $kapsterId = $request->get('kapster_id');
        $status = $request->get('status');

        $query = Booking::with(['kapster', 'service', 'customer'])->latest();

        if ($date) {
            $query->where('booking_date', $date);
        }

        if ($kapsterId) {
            $query->where('kapster_id', $kapsterId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $bookings = $query->paginate(15)->withQueryString();
        $kapsters = Kapster::all();
        $services = Service::all();

        return view('admin.bookings.index', compact('bookings', 'kapsters', 'services', 'date', 'kapsterId', 'status'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20',
            'service_id' => 'required|exists:services,id',
            'kapster_id' => 'required|exists:kapsters,id',
            'booking_date' => 'required|date',
            'booking_time' => 'required|date_format:H:i',
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string|max:255',
        ]);

        Booking::create([
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'service_id' => $validated['service_id'],
            'kapster_id' => $validated['kapster_id'],
            'booking_date' => $validated['booking_date'],
            'booking_time' => $validated['booking_time'] . ':00',
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('admin.bookings.index')->with('success', 'Booking manual berhasil ditambahkan.');
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $booking = Booking::findOrFail($id);
        $booking->update(['status' => $validated['status']]);

        return back()->with('success', 'Status booking berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return back()->with('success', 'Booking berhasil dihapus.');
    }
}
