<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Message;
use Carbon\Carbon;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    public function testMessageCapsuleCreation()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/api/messages', [
            'message' => 'A future message',
            'scheduled_opening_time' => now()->addDays(1)->toDateTimeString(),
            'is_opened' => false,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('messages', [
            'message' => 'A future message',
        ]);
    }

    public function testUpdateMessageCapsule()
    {
        $user = User::factory()->create();

        $messageCapsule = Message::factory()->create([
            'user_id' => $user->id,
            'scheduled_opening_time' => now()->subDay(), // Set to past to allow update
        ]);

        $response = $this->actingAs($user)->putJson('/api/messages/'.$messageCapsule->id, [
            'message' => 'Updated message',
            'scheduled_opening_time' => now()->addDays(1)->toDateTimeString(),
            'is_opened' => false,
        ]);

        // This time, expecting the update to succeed because the capsule is available for update
        $response->assertOk();
        $this->assertDatabaseHas('messages', [
            'id' => $messageCapsule->id,
            'message' => 'Updated message',
        ]);
    }

    public function testOpenMessageCapsule()
    {
        // Create a user and a message capsule with a past scheduled opening time
        $user = User::factory()->create();
        $messageCapsule = Message::factory()->create([
            'user_id' => $user->id,
            'message' => 'The future is now',
            'scheduled_opening_time' => Carbon::now()->subDays(1), // Ensure the capsule can be opened
            'is_opened' => false,
        ]);

        // Simulate the user opening the message capsule
        $response = $this->actingAs($user)->getJson("/api/messages/{$messageCapsule->id}/edit");

        // Assert the request was successful and the capsule is marked as opened
        $response->assertOk();
        $this->assertDatabaseHas('messages', [
            'id' => $messageCapsule->id,
            'is_opened' => true,
        ]);
    }

}
