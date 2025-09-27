<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        \App\Models\User::factory()->admin()->count(2)->create();
        $organizers = \App\Models\User::factory()->organizer()->count(3)->create();
        \App\Models\User::factory()->count(10)->create();

        $events = \App\Models\Event::factory()->count(5)->sequence(fn($i) => ['created_by' => $organizers->random()->id])->create();
        foreach($events as $event) {
            \App\Models\Ticket::factory()->count(3)->create(['event_id'=>$event->id]);
        }

        $bookings = \App\Models\Booking::factory()->count(20)->create();

        // 5️⃣ Payments
        foreach($bookings as $booking) {
            \App\Models\Payment::factory()->create([
                'booking_id' => $booking->id,
                'amount' => $booking->ticket->price * $booking->quantity,
                'status' => ['success', 'failed', 'refunded'][rand(0,2)],
            ]);
        }
    }
}
