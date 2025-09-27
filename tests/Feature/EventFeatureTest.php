<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_event_can_be_created_by_organizer()
    {
        $user = User::factory()->create(['role' => 'organizer']);
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->postJson('/api/events', [
            'title' => 'Test Event',
            'description' => 'Event description',
            'date' => now()->addDays(5)->toDateString(),
            'location' => 'Test City'
        ], ['Authorization' => "Bearer $token"]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['status','message','data' => ['id','title','description','date','location']]);
    }
}
