<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Kapster;
use App\Models\KapsterSchedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BarberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@barberbook.test'],
            [
                'name' => 'Admin Barber',
                'phone' => '081234567890',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Services
        $services = [
            [
                'name' => 'Classic Haircut',
                'price' => 50000,
                'duration_minutes' => 45,
                'description' => 'Potong rambut rapi berkelas, termasuk cuci rambut dan styling pomade.',
            ],
            [
                'name' => 'Beard Trim & Shave',
                'price' => 35000,
                'duration_minutes' => 30,
                'description' => 'Rapikan jenggot dan kumis dengan handuk hangat dan pisau cukur steril.',
            ],
            [
                'name' => 'Hair Spa & Scalp Treatment',
                'price' => 45000,
                'duration_minutes' => 30,
                'description' => 'Pijat relaksasi kepala, stimulasi pertumbuhan rambut dan penyegar kulit kepala.',
            ],
            [
                'name' => 'The Gentleman Package',
                'price' => 110000,
                'duration_minutes' => 60,
                'description' => 'Paket komplit: Haircut + Beard Grooming + Hair Spa + Styling.',
            ],
            [
                'name' => 'Hair Coloring / Highlight',
                'price' => 130000,
                'duration_minutes' => 60,
                'description' => 'Pewarnaan rambut trendi dengan produk berkualitas tanpa merusak rambut.',
            ],
        ];

        foreach ($services as $srv) {
            Service::firstOrCreate(['name' => $srv['name']], $srv);
        }

        // Kapsters
        $kapsters = [
            [
                'name' => 'Budi Santoso',
                'phone' => '081234567891',
                'photo_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'Andi Pratama',
                'phone' => '081234567892',
                'photo_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'Rizky Ramadhan',
                'phone' => '081234567893',
                'photo_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
        ];

        foreach ($kapsters as $kapData) {
            $kapster = Kapster::firstOrCreate(['name' => $kapData['name']], $kapData);

            // Schedule: Senin (1) s/d Sabtu (6), 10:00 - 21:00
            for ($day = 1; $day <= 6; $day++) {
                KapsterSchedule::firstOrCreate(
                    [
                        'kapster_id' => $kapster->id,
                        'day_of_week' => $day,
                    ],
                    [
                        'start_time' => '10:00:00',
                        'end_time' => '21:00:00',
                    ]
                );
            }
        }
    }
}
