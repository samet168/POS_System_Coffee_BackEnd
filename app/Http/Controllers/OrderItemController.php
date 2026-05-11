<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderItemController extends Controller
{
   
    public function index(Request $request)
    {
        $orderId = $request->query('order_id');

        $query = OrderItem::with(['item', 'size']);

        if ($orderId) {
            $query->where('order_id', $orderId);
        }

        $orderItems = $query->latest()->paginate(20);

        return response()->json([
            'status'  => true,
            'message' => 'Order items retrieved successfully',
            'data'    => $orderItems
        ]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id'     => 'required|exists:orders,id',
            'item_id'      => 'required|exists:items,id',
            'size_id'      => 'nullable|exists:sizes,id',
            'ice_level'    => 'nullable|in:low,medium,high',
            'sugar_level'  => 'nullable|in:0%,25%,50%,75%,100%',
            'quantity'     => 'required|integer|min:1',
            'unit_price'   => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $orderItem = OrderItem::create($request->all());

        return response()->json([
            'status'  => true,
            'message' => 'Order item created successfully',
            'data'    => $orderItem->load(['item', 'size'])
        ], 201);
    }


    public function show($id)
    {
        $orderItem = OrderItem::with(['item', 'size'])->find($id);

        if (!$orderItem) {
            return response()->json([
                'status'  => false,
                'message' => 'Order item not found'
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Order item retrieved successfully',
            'data'    => $orderItem
        ]);
    }


    public function update(Request $request, $id)
    {
        $orderItem = OrderItem::find($id,['*']);

        if (!$orderItem) {
            return response()->json([
                'status'  => false,
                'message' => 'Order item not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'size_id'     => 'nullable|exists:sizes,id',
            'ice_level'   => 'nullable|in:low,medium,high',
            'sugar_level' => 'nullable|in:0%,25%,50%,75%,100%',
            'quantity'    => 'integer|min:1',
            'unit_price'  => 'numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $orderItem->update($request->all());

        return response()->json([
            'status'  => true,
            'message' => 'Order item updated successfully',
            'data'    => $orderItem->fresh()->load(['item', 'size'])
        ]);
    }


    public function destroy($id)
    {
        $orderItem = OrderItem::find($id, ['*']);

        if (!$orderItem) {
            return response()->json([
                'status'  => false,
                'message' => 'Order item not found'
            ], 404);
        }

        $orderItem->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Order item deleted successfully'
        ], 200);
    }
}