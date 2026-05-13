<?php

namespace App\Http\Controllers;

use App\Models\ItemSizePrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItemSizePriceController extends Controller
{
    
    public function index(Request $request)
    {
        $itemId = $request->query('item_id');

        $query = ItemSizePrice::with(['item', 'size']);

        if ($itemId) {
            $query->where('item_id', $itemId);
        }

        $prices = $query->latest()->paginate(20);

        return response()->json([
            'status'  => true,
            'message' => 'Item size prices retrieved successfully',
            'data'    => $prices
        ]);
    }

   public function list(Request $request)
    {
        $itemSizePrices = ItemSizePrice::with(['item', 'size'])

            // search by item name
            ->when($request->item_name, function ($query) use ($request) {
                $query->whereHas('item', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->item_name . '%');
                });
            })

            // search by size name
            ->when($request->size_name, function ($query) use ($request) {
                $query->whereHas('size', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->size_name . '%');
                });
            })

            // filter by item_id
            ->when($request->item_id, function ($query) use ($request) {
                $query->where('item_id', $request->item_id);
            })

            // filter by size_id
            ->when($request->size_id, function ($query) use ($request) {
                $query->where('size_id', $request->size_id);
            })

            ->latest()
            ->paginate(20);

        return response()->json([
            'status'  => true,
            'message' => 'Item size prices retrieved successfully',
            'data'    => $itemSizePrices
        ]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id'  => 'required|exists:items,id',
            'size_id'  => 'required|exists:sizes,id',
            'price'    => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        // ការពារកុំឱ្យមានទិន្នន័យស្ទួន (item + size)
        $exists = ItemSizePrice::where('item_id', $request->item_id)
                               ->where('size_id', $request->size_id)
                               ->exists();

        if ($exists) {
            return response()->json([
                'status'  => false,
                'message' => 'This item size price already exists'
            ], 422);
        }

        $itemSizePrice = ItemSizePrice::create($request->all());

        return response()->json([
            'status'  => true,
            'message' => 'Item size price created successfully',
            'data'    => $itemSizePrice->load(['item', 'size'])
        ], 201);
    }


    public function show($id)
    {
        $itemSizePrice = ItemSizePrice::with(['item', 'size'])->find($id);

        if (!$itemSizePrice) {
            return response()->json([
                'status'  => false,
                'message' => 'Item size price not found'
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Item size price retrieved successfully',
            'data'    => $itemSizePrice
        ]);
    }


    public function update(Request $request, $id)
    {
        $itemSizePrice = ItemSizePrice::find($id, ['*']);

        if (!$itemSizePrice) {
            return response()->json([
                'status'  => false,
                'message' => 'Item size price not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'price' => 'numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $itemSizePrice->update($request->all());

        return response()->json([
            'status'  => true,
            'message' => 'Item size price updated successfully',
            'data'    => $itemSizePrice->fresh()->load(['item', 'size'])
        ]);
    }

    // លុបតាម ID
    public function destroy($id)
    {
        $itemSizePrice = ItemSizePrice::find($id, ['*']);

        if (!$itemSizePrice) {
            return response()->json([
                'status'  => false,
                'message' => 'Item size price not found'
            ], 404);
        }

        $itemSizePrice->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Item size price deleted successfully'
        ], 200);
    }
}