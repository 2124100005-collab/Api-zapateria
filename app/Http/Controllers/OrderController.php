<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id'   => 'required|integer',
            'items.*.product_name' => 'required|string',
            'items.*.price'        => 'required|numeric',
            'items.*.quantity'     => 'required|integer|min:1',
        ]);

        $user = $request->user();

        DB::beginTransaction();
        try {
            $total = 0;
            foreach ($request->items as $item) {
                $stock = DB::table('product_stock')
                    ->where('product_id', $item['product_id'])
                    ->value('stock');

                if ($stock === null || $stock < $item['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuficiente para {$item['product_name']}",
                    ], 422);
                }

                $total += $item['price'] * $item['quantity'];
            }

            $order = Order::create([
                'user_id' => $user->id,
                'total'   => $total,
                'status'  => 'pending',
            ]);

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'price'        => $item['price'],
                    'quantity'     => $item['quantity'],
                    'subtotal'     => $item['price'] * $item['quantity'],
                ]);

                DB::table('product_stock')
                    ->where('product_id', $item['product_id'])
                    ->decrement('stock', $item['quantity']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data'    => $order->load('items'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el pedido',
            ], 500);
        }
    }

    public function index(Request $request, int $userId)
    {
        if ($request->user()->id !== $userId) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        $orders = Order::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $orders,
        ]);
    }

    public function show(Request $request, int $userId, int $orderId)
    {
        if ($request->user()->id !== $userId) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        $order = Order::where('id', $orderId)
            ->where('user_id', $userId)
            ->with('items')
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pedido no encontrado'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $order,
        ]);
    }

    public function cancel(Request $request, int $userId, int $orderId)
    {
        if ($request->user()->id !== $userId) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        $order = Order::where('id', $orderId)
            ->where('user_id', $userId)
            ->with('items')
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pedido no encontrado'], 404);
        }

        if ($order->status === 'cancelled') {
            return response()->json(['success' => false, 'message' => 'El pedido ya está cancelado'], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($order->items as $item) {
                DB::table('product_stock')
                    ->where('product_id', $item->product_id)
                    ->increment('stock', $item->quantity);
            }

            $order->update(['status' => 'cancelled']);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pedido cancelado correctamente',
                'data'    => $order,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar el pedido',
            ], 500);
        }
    }
}