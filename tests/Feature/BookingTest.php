<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Kapster;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\BarberSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(BarberSeeder::class);
    }
    public function test_booking_page_can_be_rendered()
    {
        $response = $this->get('/booking');
        $response->assertStatus(200);
        $response->assertSee('BarberBook');
    }

    public function test_available_slots_endpoint()
    {
        $service = Service::first();
        $date = Carbon::tomorrow()->format('Y-m-d');

        $response = $this->getJson("/api/available-slots?date={$date}&service_id={$service->id}&kapster_id=any");
        $response->assertStatus(200);
        $response->assertJsonStructure(['slots']);
    }

    public function test_can_create_booking_successfully()
    {
        $service = Service::first();
        $kapster = Kapster::first();
        $date = Carbon::tomorrow()->format('Y-m-d');

        $response = $this->post('/booking', [
            'service_id' => $service->id,
            'kapster_id' => $kapster->id,
            'customer_name' => 'Budi Tester',
            'customer_phone' => '081234567899',
            'booking_date' => $date,
            'booking_time' => '11:00',
            'notes' => 'Potongan rapi',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'customer_name' => 'Budi Tester',
            'customer_phone' => '081234567899',
            'booking_date' => $date,
            'booking_time' => '11:00:00',
        ]);
    }

    public function test_admin_dashboard_requires_admin()
    {
        // Guest cannot access
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');

        // Normal customer cannot access
        $user = User::factory()->create(['role' => 'customer']);
        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(403);

        // Admin can access
        $admin = User::where('role', 'admin')->first();
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Overview');
    }
}
