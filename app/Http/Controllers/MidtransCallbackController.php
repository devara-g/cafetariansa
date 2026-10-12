<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class MidtransCallbackController extends Controller
{
    public function __construct(private MidtransService $midtransService) {}

    /**
     * Handle incoming webhook notification from Midtrans.
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $payload = $request->all();
            Log::info('Midtrans Webhook Received: ', ['order_id' => $payload['order_id'] ?? null]);

            $result = $this->midtransService->handleNotification($payload);

            return response()->json([
                'success' => true,
                'message' => 'Notification handled successfully.',
                'status' => $result['status'],
                'order_number' => $result['order']->order_number,
            ]);
        } catch (InvalidArgumentException $e) {
            Log::warning('Midtrans Webhook Invalid Signature: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        } catch (\Throwable $e) {
            Log::error('Midtrans Webhook Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to process notification.',
            ], 500);
        }
    }
}
