<?php

namespace App\Http\Controllers;

use App\Models\ItemSizePrice;
use Illuminate\Http\Request;

class ItemSizePriceController extends Controller
{
    public function index()
    {
        $prices = ItemSizePrice::with(['item', 'size'])->get();
        return response()->json([
            'status' => true,
            'message' => 'Item size prices retrieved successfully',
            'data' => $prices
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,_id',
            'size_id' => 'required|exists:sizes,_id',
            'price'   => 'required|numeric|min:0'
        ]);

        $price = ItemSizePrice::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Item size price created successfully',
            'data' => $price
        ], 201);
    }

    public function show($id)
    {
        $price = ItemSizePrice::with(['item', 'size'])->find($id);
        if (!$price) return response()->json(['status' => false, 'message' => 'Price record not found'], 404);
        return response()->json(['
            status' => true, 
            'data' => $price], 200);
    }

    public function update(Request $request, int $id)
    {
        $price = ItemSizePrice::find($id,['*']);
        if (!$price) return response()->json(['
                status' => false, 
                'message' => 'Price record not found'], 404);

        $request->validate(['price' => 'required|numeric|min:0']);
        $price->update($request->only('price'));

        return response()->json([
            'status' => true,
            'message' => 'Price updated successfully',
            'data' => $price
        ], 200);
    }

    public function destroy($id)
    {
        $price = ItemSizePrice::find($id,['*']);
        if (!$price) return response()->json(['status' => false, 'message' => 'Price record not found'], 404);

        $price->delete();
        return response()->json(['status' => true, 'message' => 'Price record deleted successfully'], 200);
    }
}