<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'discount'])
                       ->latest()
                       ->paginate(20);

        return response()->json([
            'status'  => true,
            'message' => 'Orders retrieved successfully',
            'data'    => $orders
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'discount_id'  => 'nullable|exists:discounts,id',
            'total_amount' => 'required|numeric|min:0',
            'table_number' => 'nullable|string|max:20',
            'user_id'      => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $order = Order::create($request->all());

        return response()->json([
            'status'  => true,
            'message' => 'Order created successfully',
            'data'    => $order
        ], 201);
    }


    public function show($id)
    {
        $order = Order::with(['user', 'discount', 'items'])->find($id);

        if (!$order) {
            return response()->json([
                'status'  => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Order retrieved successfully',
            'data'    => $order
        ]);
    }


    public function update(Request $request, $id)
    {
        $order = Order::find($id,['*']);

        if (!$order) {
            return response()->json([
                'status'  => false,
                'message' => 'Order not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'discount_id'  => 'nullable|exists:discounts,id',
            'total_amount' => 'numeric|min:0',
            'table_number' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $order->update($request->all());

        return response()->json([
            'status'  => true,
            'message' => 'Order updated successfully',
            'data'    => $order->fresh()
        ]);
    }


    public function destroy($id)
    {
        $order = Order::find($id, ['*']);

        if (!$order) {
            return response()->json([
                'status'  => false,
                'message' => 'Order not found'
            ], 404);
        }

        $order->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Order deleted successfully'
        ], 200);
    }
}