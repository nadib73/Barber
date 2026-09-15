<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kapster;
use App\Models\KapsterSchedule;
use Illuminate\Http\Request;

class KapsterController extends Controller
{
    public function index()
    {
        $kapsters = Kapster::with('schedules')->get();
        return view('admin.kapsters.index', compact('kapsters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'photo_url' => 'nullable|url',
        ]);

        $kapster = Kapster::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'photo_url' => $validated['photo_url'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
            'is_active' => true,
        ]);

        // Create default schedules (Senin - Sabtu: 10:00 - 21:00)
        for ($day = 1; $day <= 6; $day++) {
            KapsterSchedule::create([
                'kapster_id' => $kapster->id,
                'day_of_week' => $day,
                'start_time' => '10:00:00',
                'end_time' => '21:00:00',
            ]);
        }

        return back()->with('success', 'Kapster baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $kapster = Kapster::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'photo_url' => 'nullable|url',
            'is_active' => 'nullable|boolean',
        ]);

        $kapster->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'photo_url' => $validated['photo_url'],
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'Data kapster diperbarui.');
    }

    public function updateSchedule(Request $request, $id)
    {
        $kapster = Kapster::findOrFail($id);
        $schedules = $request->input('schedules', []); // array of [day => [active, start_time, end_time]]

        KapsterSchedule::where('kapster_id', $kapster->id)->delete();

        foreach ($schedules as $day => $data) {
            if (!empty($data['active'])) {
                KapsterSchedule::create([
                    'kapster_id' => $kapster->id,
                    'day_of_week' => $day,
                    'start_time' => $data['start_time'] ?? '10:00:00',
                    'end_time' => $data['end_time'] ?? '21:00:00',
                ]);
            }
        }

        return back()->with('success', 'Jadwal kerja kapster berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kapster = Kapster::findOrFail($id);
        $kapster->delete();

        return back()->with('success', 'Kapster berhasil dihapus.');
    }
}
