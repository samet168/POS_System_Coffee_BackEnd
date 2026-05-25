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
        try {
            $query = ItemSizePrice::query();

    
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('item', function ($q) use ($search) {
                    $q->whereRaw([
                        'name' => ['$regex' => $search, '$options' => 'i']
                    ]);
                });
            }


            if ($request->filled('item_id')) {
                $query->where('item_id', $request->item_id);
            }

    
            if ($request->filled('size_id')) {
                $query->where('size_id', $request->size_id);
            }

    
            if ($request->filled('min_price')) {
                $query->where('price', '>=', (float)$request->min_price);
            }
            if ($request->filled('max_price')) {
                $query->where('price', '<=', (float)$request->max_price);
            }


            $sortBy = $request->get('sort_by', '_id');
            $sortDir = $request->get('sort_dir', 'desc');

            $allowed = ['_id', 'price', 'created_at', 'updated_at'];
            if (in_array($sortBy, $allowed)) {
                $query->orderBy($sortBy, $sortDir);
            } else {
                $query->orderBy('_id', 'desc');
            }

            // ====================== PAGINATION ======================
            $perPage = (int) $request->get('per_page', 10);
            $perPage = max(1, min(100, $perPage));

            $itemSizePrices = $query->paginate($perPage);

            $itemSizePrices->load(['item', 'size']);

            return response()->json([
                'status'       => true,
                'message'      => 'Item size prices retrieved successfully',
                'total'        => $itemSizePrices->total(),
                'current_page' => $itemSizePrices->currentPage(),
                'per_page'     => $itemSizePrices->perPage(),
                'last_page'    => $itemSizePrices->lastPage(),
                'data'         => $itemSizePrices->items()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Error occurred',
                'error'   => $e->getMessage()
            ], 500);
        }
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