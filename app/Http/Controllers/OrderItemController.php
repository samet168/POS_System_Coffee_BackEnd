<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    public function index()
    {
        $items = OrderItem::with(['order', 'item', 'size'])->get();
        return response()->json([
            'status' => true,
            'message' => 'Order items retrieved successfully',
            'data' => $items
        ], 200);
    }

    public function show($id)
    {
        $item = OrderItem::with(['item', 'size'])->find($id);
        if (!$item) return response()->json(['status' => false, 'message' => 'Order item not found'], 404);
        return response()->json(['status' => true, 'data' => $item], 200);
    }

    public function destroy($id)
    {
        $item = OrderItem::find($id,['*']);
        if (!$item) return response()->json(['status' => false, 'message' => 'Order item not found'], 404);

        $item->delete();
        return response()->json(['status' => true, 'message' => 'Order item deleted successfully'], 200);
    }
}