<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ItemSizePrice;

class OrderItemController extends Controller
{
    // ================= LIST =================
    public function index()
{
    try {
        $orders = OrderItem::with(['item'])->get();

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
public function list(Request $request)
{
    try {

        $query = OrderItem::query();

        if ($request->filled('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->filled('size_id')) {
            $query->where('size_id', $request->size_id);
        }

        $perPage = $request->get('per_page', 10);

        $orderItems = $query->orderBy('_id', 'desc')->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Order items retrieved successfully',
            'data' => $orderItems
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'status' => false,
            'message' => $e->getMessage(),
            'line' => $e->getLine()
        ], 500);
    }
}

    // ================= STORE =================
    public function store(Request $request)
    {
        try {

            // VALIDATION
            $validator = Validator::make($request->all(), [
                'order_id'     => 'nullable|exists:orders,id',
                'item_id'      => 'required|exists:items,id',
                'size_id'      => 'required|exists:sizes,id',
                'ice_level'    => 'nullable|in:low,medium,high',
                'sugar_level'  => 'nullable|in:0%,25%,50%,75%,100%',
                'quantity'     => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // ================= GET PRICE FROM SIZEPRICE =================
            $sizePrice = ItemSizePrice::where('item_id', $request->item_id)
                ->where('size_id', $request->size_id)
                ->first();

            if (!$sizePrice) {
                return response()->json([
                    'status' => false,
                    'message' => 'Price not found for this item & size'
                ], 404);
            }

            // ================= CREATE ORDER IF NOT EXIST =================
            $orderId = $request->order_id;

            if (!$orderId) {
                $order = Order::create([
                    'user_id' => auth()->id() ?? 1,
                    'table_number' => 'T1',
                    'status' => 'pending',
                    'total_amount' => 0,
                ]);

                $orderId = $order->id;
            }

            // ================= CREATE ORDER ITEM =================
            $orderItem = OrderItem::create([
                'order_id'    => $orderId,
                'item_id'     => $request->item_id,
                'size_id'     => $request->size_id,
                'ice_level'   => $request->ice_level,
                'sugar_level' => $request->sugar_level,
                'quantity'    => $request->quantity,
                'unit_price'  => $sizePrice->price,
            ]);

            // ================= CALCULATE TOTAL (MONGODB SAFE) =================
            $total = OrderItem::where('order_id', $orderId)
                ->get()
                ->sum(function ($item) {
                    return $item->quantity * $item->unit_price;
                });

            Order::where('id', $orderId)->update([
                'total_amount' => $total
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Order item created successfully',
                'data' => $orderItem->load(['order', 'item', 'size'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    // ================= DELETE =================
    public function destroy($id)
    {
        try {

            $orderItem = OrderItem::find($id);

            if (!$orderItem) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order item not found'
                ], 404);
            }

            $orderId = $orderItem->order_id;

            $orderItem->delete();

            // UPDATE TOTAL (MONGODB SAFE)
            $total = OrderItem::where('order_id', $orderId)
                ->get()
                ->sum(function ($item) {
                    return $item->quantity * $item->unit_price;
                });

            Order::where('id', $orderId)->update([
                'total_amount' => $total
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}