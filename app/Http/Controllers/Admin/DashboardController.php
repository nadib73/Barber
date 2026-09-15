<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Kapster;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->format('Y-m-d');

        $todayBookings = Booking::with(['kapster', 'service'])
            ->where('booking_date', $today)
            ->orderBy('booking_time', 'asc')
            ->get();

        $stats = [
            'today_count' => $todayBookings->count(),
            'today_revenue' => $todayBookings->whereIn('status', ['confirmed', 'completed'])->sum(function ($b) {
                return $b->service ? $b->service->price : 0;
            }),
            'pending_count' => Booking::where('status', 'pending')->count(),
            'total_kapsters' => Kapster::where('is_active', true)->count(),
            'total_services' => Service::count(),
            'total_customers' => User::where('role', 'customer')->count(),
        ];

        return view('admin.dashboard', compact('todayBookings', 'stats'));
    }
}
