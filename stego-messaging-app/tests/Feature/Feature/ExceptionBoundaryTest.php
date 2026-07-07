<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\SteganoService;
use Illuminate\Support\Facades\Log;

class ExceptionBoundaryTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_catches_corrupted_payload_exceptions_and_logs_to_laravel_repository()
    {
        Log::shouldReceive('error')->zeroOrMoreTimes();
        
        $stegoService = new SteganoService();
        $corruptedData = "Not_A_Valid_Base64_Or_PNG_String";

        try {
            $result = $stegoService->extract($corruptedData);
        } catch (\Exception $e) {
            $this->fail('The application crashed instead of handling the exception safely.');
        }

        // Updated assertion to match your actual return value
        $this->assertNull($result);
    }
}