<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with('category')
                     ->latest()
                     ->paginate(20);

        return response()->json([
            'status'  => true,
            'message' => 'Items retrieved successfully',
            'data'    => $items
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_category_id' => 'required|exists:item_categories,id',
            'name'             => 'required|string|max:255',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'status'           => 'nullable|in:In Stock,Out of Stock',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $request->only(['item_category_id', 'name', 'status']);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . rand(100000, 999999) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('images/items');

            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $fileName);
            $data['image'] = url('images/items/' . $fileName);
        }

        $item = Item::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Item created successfully',
            'data'    => $item->load('category')
        ], 201);
    }

    public function show($id)
    {
        $item = Item::with('category')->find($id);

        if (!$item) {
            return response()->json([
                'status'  => false,
                'message' => 'Item not found'
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Item retrieved successfully',
            'data'    => $item
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = Item::find($id, ['*']);

        if (!$item) {
            return response()->json([
                'status'  => false,
                'message' => 'Item not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'item_category_id' => 'nullable|exists:item_categories,id',
            'name'             => 'string|max:255',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'status'           => 'nullable|in:In Stock,Out of Stock',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $request->only(['item_category_id', 'name', 'status']);


        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . rand(100000, 999999) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('images/items');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $fileName);
            $data['image'] = url('images/items/' . $fileName);


            if ($item->image) {
                $oldImagePath = public_path(str_replace(url('/'), '', $item->image));
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
        }

        $item->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'Item updated successfully',
            'data'    => $item->fresh()->load('category')
        ]);
    }

    public function destroy($id)
    {
        $item = Item::find($id, ['*']);

        if (!$item) {
            return response()->json([
                'status'  => false,
                'message' => 'Item not found'
            ], 404);
        }

        if ($item->image) {
            $oldImagePath = public_path(str_replace(url('/'), '', $item->image));
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        $item->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Item deleted successfully'
        ], 200);
    }
}