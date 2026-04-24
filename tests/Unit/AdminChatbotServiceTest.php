<?php

namespace Tests\Unit;

use App\Services\AdminChatbotService;
use Tests\TestCase;

class AdminChatbotServiceTest extends TestCase
{
    /**
     * The built-in calculator should answer deterministically without needing
     * database access or the Python Vanna sidecar.
     */
    public function test_it_handles_calculator_questions_locally(): void
    {
        $service = new AdminChatbotService();

        $response = $service->answer('Calculate 2450 + 18%');

        $this->assertSame('calculator', $response['source']);
        $this->assertStringContainsString('2,891', $response['answer']);
    }

    /**
     * Greeting prompts should produce a friendly local response.
     */
    public function test_it_handles_greeting_prompts_locally(): void
    {
        $service = new AdminChatbotService();

        $response = $service->answer('hello');

        $this->assertSame('laravel-rule', $response['source']);
        $this->assertStringContainsString('Ask me about low stock', $response['answer']);
    }
}
