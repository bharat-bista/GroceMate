<?php

namespace Tests\Unit;

use App\Models\POS\Customer;
use App\Models\Supplier;
use App\Services\AdminChatbotService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminChatbotServiceTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_it_answers_customer_due_questions_locally(): void
    {
        Customer::query()->create([
            'name' => 'Acme Retail',
            'total_due' => 1250.50,
            'opening_due' => 0,
        ]);

        $service = new AdminChatbotService();
        $response = $service->answer('show customer due');

        $this->assertSame('laravel-rule', $response['source']);
        $this->assertStringContainsString('Total customer receivable', $response['answer']);
        $this->assertStringContainsString('Acme Retail', $response['answer']);
    }

    public function test_it_answers_supplier_due_questions_locally(): void
    {
        Supplier::query()->create([
            'name' => 'Fresh Wholesale',
            'total_due' => 2300,
            'opening_due' => 0,
        ]);

        $service = new AdminChatbotService();
        $response = $service->answer('show supplier due');

        $this->assertSame('laravel-rule', $response['source']);
        $this->assertStringContainsString('Total supplier payable', $response['answer']);
        $this->assertStringContainsString('Fresh Wholesale', $response['answer']);
    }
}
