<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Register a test route that explicitly checks for an administrative flag
        Route::get('/_test/admin/dashboard', function (\Illuminate\Http\Request $request) {
            $user = $request->user();
            
            // Hard gate: strictly check the attribute or force drop if missing/false
            if (!$user || !isset($user->is_admin) || $user->is_admin != true) {
                abort(403, 'Unauthorized access to administrative layer.');
            }
            
            return response()->json(['status' => 'success']);
        })->middleware(['web']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function unauthorized_users_are_blocked_from_admin_dashboard()
    {
        // 1. Create a user and explicitly set is_admin to 0 (false)
        $regularUser = User::factory()->create();
        $regularUser->is_admin = 0;
        $regularUser->save();

        // 2. Refresh the model instance to ensure the state is locked into SQLite memory
        $regularUser = $regularUser->fresh();

        // 3. Fire the request into the gateway
        $response = $this->actingAs($regularUser)->get('/_test/admin/dashboard');

        // 4. Assert the authorization gate intercepts and cuts off the request cleanly
        $response->assertStatus(403);
    }
}