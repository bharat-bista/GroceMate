<?php

namespace App\Http\Controllers;

use App\Services\AdminChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminChatbotController extends Controller
{
    public function __construct(
        protected AdminChatbotService $chatbotService
    ) {
    }

    /**
     * Handle a single admin chatbot message.
     *
     * The sticky widget sends every prompt here. This controller stays very
     * small on purpose: validation happens here, while the business logic lives
     * inside the dedicated service class.
     */
    public function message(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        return response()->json(
            $this->chatbotService->answer(
                message: $data['message'],
                user: $request->user(),
            )
        );
    }
}
