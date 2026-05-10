<?php

namespace App\Http\Controllers;

use App\Models\ItemSizePrice;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
public function index()
{
    $orders = Order::all();

    return response()->json([
        'status' => true,
        'message' => 'Orders retrieved successfully',
        'data' => $orders
    ], 200);
}

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'order_status_id' => 'required|exists:order_statuses,_id',
    //         'discount_id'     => 'nullable|exists:discounts,_id',
    //         'table_number'    => 'nullable|string',
    //         'user_id'         => 'required|exists:users,_id',
    //         'items'           => 'required|array|min:1',
    //         'items.*.item_id' => 'required|exists:items,_id',
    //         'items.*.size_id' => 'nullable|exists:sizes,_id',
    //         'items.*.quantity'=> 'required|integer|min:1',
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         $order = Order::create([
    //             'order_status_id' => $request->order_status_id,
    //             'discount_id'     => $request->discount_id,
    //             'user_id'         => $request->user_id,
    //             'table_number'    => $request->table_number,
    //             'total_amount'    => 0,
    //         ]);

    //         $total = 0;

    //         foreach ($request->items as $item) {
    //             $priceRecord = ItemSizePrice::where('item_id', $item['item_id'])
    //                             ->where('size_id', $item['size_id'])->first();

    //             $unitPrice = $priceRecord ? $priceRecord->price : 0;
    //             $subTotal  = $unitPrice * $item['quantity'];

    //             OrderItem::create([
    //                 'order_id'   => $order->id,
    //                 'item_id'    => $item['item_id'],
    //                 'size_id'    => $item['size_id'],
    //                 'quantity'   => $item['quantity'],
    //                 'unit_price' => $unitPrice,
    //                 'sub_total'  => $subTotal,
    //             ]);

    //             $total += $subTotal;
    //         }

    //         $order->update(['total_amount' => $total]);
    //         DB::commit();

    //         $order->load(['orderItems.item', 'orderItems.size']);

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Order created successfully',
    //             'data' => $order
    //         ], 201);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Failed to create order',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function store(Request $request)
{
    try {

        $order = Order::create([
            'order_status_id' => $request->order_status_id,
            'discount_id'     => $request->discount_id,
            'user_id'         => $request->user_id,
            'table_number'    => $request->table_number,
            'total_amount'    => 0,
        ]);

        $total = 0;

        foreach ($request->items as $item) {

            $priceRecord = ItemSizePrice::where('item_id', $item['item_id'])
                ->where('size_id', $item['size_id'])
                ->first();

            $unitPrice = $priceRecord ? $priceRecord->price : 0;
            $subTotal  = $unitPrice * $item['quantity'];

            OrderItem::create([
                'order_id'   => $order->id,
                'item_id'    => $item['item_id'],
                'size_id'    => $item['size_id'],
                'quantity'   => $item['quantity'],
                'unit_price' => $unitPrice,
                'sub_total'  => $subTotal,
            ]);

            $total += $subTotal;
        }

        $order->update(['total_amount' => $total]);

        $order->load(['items']);

        return response()->json([
            'status' => true,
            'message' => 'Order created successfully',
            'data' => $order
        ], 201);

    } catch (\Exception $e) {

        return response()->json([
            'status' => false,
            'message' => 'Failed to create order',
            'error' => $e->getMessage()
        ], 500);
    }
}
    public function show($id)
    {
        $order = Order::with(['user', 'status', 'discount', 'orderItems.item', 'orderItems.size'])->find($id);
        if (!$order) return response()->json(['status' => false, 'message' => 'Order not found'], 404);
        return response()->json(['status' => true, 'data' => $order], 200);
    }

    public function update(Request $request, $id)
    {
        $order = Order::find($id,['*']);
        if (!$order) return response()->json(['status' => false, 'message' => 'Order not found'], 404);

        $order->update($request->only(['order_status_id', 'discount_id', 'table_number']));

        return response()->json([
            'status' => true,
            'message' => 'Order updated successfully',
            'data' => $order
        ], 200);
    }

    public function destroy($id)
    {
        $order = Order::find($id,['*']);
        if (!$order) return response()->json(['status' => false, 'message' => 'Order not found'], 404);

        $order->delete();
        return response()->json(['status' => true, 'message' => 'Order deleted successfully'], 200);
    }
}