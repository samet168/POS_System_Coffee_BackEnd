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
    public function list(Request $request)
    {
        $orderItems = OrderItem::with(['order', 'item', 'size'])

            // search by order_id
            ->when($request->order_id, function ($query) use ($request) {
                $query->where('order_id', $request->order_id);
            })

            // search by item_id
            ->when($request->item_id, function ($query) use ($request) {
                $query->where('item_id', $request->item_id);
            })

            // search by item name
            ->when($request->item_name, function ($query) use ($request) {
                $query->whereHas('item', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->item_name . '%');
                });
            })

            //  search by size_id
            ->when($request->size_id, function ($query) use ($request) {
                $query->where('size_id', $request->size_id);
            })

            ->latest()
            ->paginate(10);

        return response()->json([
            'status'  => true,
            'message' => 'Order items retrieved successfully',
            'data'    => $orderItems
        ]);
    }
    // public function list(Request $request)
    // {
    //     $orderItems = OrderItem::with([
    //             'order',
    //             'item.category',
    //             'size'
    //         ])

    //         // filter order
    //         ->when($request->order_id, function ($query) use ($request) {
    //             $query->where('order_id', $request->order_id);
    //         })

    //         // filter item
    //         ->when($request->item_id, function ($query) use ($request) {
    //             $query->where('item_id', $request->item_id);
    //         })

    //         // filter size
    //         ->when($request->size_id, function ($query) use ($request) {
    //             $query->where('size_id', $request->size_id);
    //         })

    //         // search item name (IMPORTANT FIX)
    //         ->when($request->item_name, function ($query) use ($request) {
    //             $query->whereHas('item', function ($q) use ($request) {
    //                 $q->where('name', 'like', '%' . $request->item_name . '%');
    //             });
    //         })

    //         ->latest()
    //         ->paginate(10);

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Order items retrieved successfully',
    //         'data' => $orderItems
    //     ]);
    // }
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'order_id'     => "nullable",
    //         'item_id'      => 'required|exists:items,id',
    //         'size_id'      => 'nullable|exists:sizes,id',
    //         'ice_level'    => 'nullable|in:low,medium,high',
    //         'sugar_level'  => 'nullable|in:0%,25%,50%,75%,100%',
    //         'quantity'     => 'required|integer|min:1',
    //         'unit_price'   => 'required|numeric|min:0',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Validation failed',
    //             'errors'  => $validator->errors()
    //         ], 422);
    //     }

    //     $orderItem = OrderItem::create($request->all());

    //     return response()->json([
    //         'status'  => true,
    //         'message' => 'Order item created successfully',
    //         'data'    => $orderItem->load(['item', 'size'])
    //     ], 201);
    // }
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'order_id'     => 'nullable|exists:orders,id',
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

    // បើ order_id មិនបានផ្ញើមក យើងបង្កើត Order ថ្មីស្វ័យប្រវត្តិ
    $orderId = $request->order_id;

    if (!$orderId) {
        $order = \App\Models\Order::create([
            'user_id'       => auth()->id() ?? 1, 
            'table_number'  => 'T1',                     
            'status'        => 'pending',
            'total_amount'  => 0,
        ]);
        $orderId = $order->id;
    }

    $orderItem = OrderItem::create([
        'order_id'     => $orderId,
        'item_id'      => $request->item_id,
        'size_id'      => $request->size_id,
        'ice_level'    => $request->ice_level,
        'sugar_level'  => $request->sugar_level,
        'quantity'     => $request->quantity,
        'unit_price'   => $request->unit_price,
    ]);

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
// public function update(Request $request, $id)
// {
//     $orderItem = OrderItem::find($id);
//     if (!$orderItem) return response()->json(['message' => 'Not found'], 404);

//     $validator = Validator::make($request->all(), [
//         'quantity' => 'required|integer|min:1',
//         'unit_price' => 'required|numeric'
//     ]);

//     if ($validator->fails()) return response()->json(['errors' => $validator->errors()], 422);

//     // គណនាឡើងវិញ
//     $data = $request->all();
//     $data['sub_total'] = $data['quantity'] * $data['unit_price'];

//     $orderItem->update($data);

//     return response()->json([
//         'status' => true,
//         'data' => $orderItem->fresh()->load(['item', 'size'])
//     ]);
// }


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