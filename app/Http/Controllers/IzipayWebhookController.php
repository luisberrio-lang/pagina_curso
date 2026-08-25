<?php

namespace App\Http\Controllers;

use App\Payments\WebhookRejected;
use App\Services\IzipayWebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class IzipayWebhookController extends Controller
{
    public function __invoke(Request $request, IzipayWebhookService $webhooks): JsonResponse
    {
        if (strlen($request->getContent()) > 65536) {
            return response()->json(['accepted' => false], 413);
        }

        try {
            $result = $webhooks->process((array) $request->json()->all(), $request->header('transactionId'));

            return response()->json(['accepted' => true, 'result' => $result]);
        } catch (WebhookRejected $exception) {
            Log::warning('Notificación Izipay rechazada.', ['reason' => $exception->getMessage()]);

            return response()->json(['accepted' => false], $exception->status);
        } catch (Throwable $exception) {
            Log::error('Error interno procesando notificación Izipay.', ['exception' => $exception::class]);

            return response()->json(['accepted' => false], 500);
        }
    }
}
