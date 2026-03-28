<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaymentController extends Controller
{
    public function pay(Request $request, int $userId, int $orderId)
    {
        if ($request->user()->id !== $userId) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        $order = Order::where('id', $orderId)
            ->where('user_id', $userId)
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pedido no encontrado'], 404);
        }

        if ($order->payment_status === 'completed') {
            return response()->json(['success' => false, 'message' => 'El pedido ya fue pagado'], 422);
        }

        if ($order->status === 'cancelled') {
            return response()->json(['success' => false, 'message' => 'No puedes pagar un pedido cancelado'], 422);
        }

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            $response = $provider->createOrder([
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => 'order_' . $order->id,
                        'amount' => [
                            'currency_code' => 'MXN',
                            'value' => number_format($order->total, 2, '.', ''),
                        ],
                    ],
                ],
                'application_context' => [
                    'return_url' => env('APP_URL_CLIENT', 'http://localhost:8000') . '/pagos/success/' . $userId . '/' . $orderId,
                    'cancel_url' => env('APP_URL_CLIENT', 'http://localhost:8000') . '/pagos/cancel',
                ],
            ]);

            if (isset($response['id']) && $response['status'] === 'CREATED') {
                foreach ($response['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        return response()->json([
                            'success'      => true,
                            'approval_url' => $link['href'],
                            'paypal_id'    => $response['id'],
                        ]);
                    }
                }
            }

            return response()->json(['success' => false, 'message' => 'Error al crear el pago en PayPal'], 500);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function capture(Request $request, int $userId, int $orderId)
    {
        $request->validate([
            'paypal_order_id' => 'required|string',
        ]);

        if ($request->user()->id !== $userId) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        $order = Order::where('id', $orderId)
            ->where('user_id', $userId)
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pedido no encontrado'], 404);
        }

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            $response = $provider->capturePaymentOrder($request->paypal_order_id);

            if (isset($response['status']) && $response['status'] === 'COMPLETED') {
                $transactionId = $response['purchase_units'][0]['payments']['captures'][0]['id'];

                $order->update([
                    'transaction_id' => $transactionId,
                    'payment_status' => 'completed',
                    'paid_at'        => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Pago completado correctamente',
                    'data'    => $order,
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Error al capturar el pago'], 500);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}