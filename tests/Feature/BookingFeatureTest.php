<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_can_be_booked()
    {
        $user = User::factory()->create(['role' => 'customer']);
        $event = Event::factory()->create(['created_by' => $user->id]);
        $ticket = Ticket::factory()->create(['event_id' => $event->id, 'quantity' => 10]);
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->postJson("/api/tickets/{$ticket->id}/bookings", [
            'quantity' => 2
        ], ['Authorization' => "Bearer $token"]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'status',
                    'message',
                    'data' => ['id','user_id','ticket_id','quantity','status']
                ]);
    }

    public function test_booking_can_be_cancelled()
    {
        $user = User::factory()->create(['role' => 'customer']);
        $ticket = Ticket::factory()->create(['quantity'=>5]);
        $booking = Booking::factory()->create([
            'user_id'=>$user->id,
            'ticket_id'=>$ticket->id,
            'quantity'=>2,
            'status'=>'pending'
        ]);
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->putJson("/api/bookings/{$booking->id}/cancel", [], [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Booking cancelled successfully'
                ]);
    }
}
