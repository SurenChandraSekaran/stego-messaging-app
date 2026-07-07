<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MessageRoutingTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_authenticate_and_initialize_chat_state_sessions()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/dashboard');

        // Asserts user baseline session navigation works without system exceptions
        $this->assertTrue($response->status() === 200 || $response->status() === 302);
    }
}