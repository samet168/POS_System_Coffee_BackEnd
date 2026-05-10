<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with(['category', 'status'])->get();
        return response()->json([
            'status' => true,
            'message' => 'Items retrieved successfully',
            'data' => $items
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_category_id' => 'required|exists:item_categories,_id',
            'item_status_id'   => 'required|exists:item_statuses,_id',
            'name'             => 'required|string|max:255',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', 
        ]);

        $item = new Item();
        $item->item_category_id = $request->item_category_id;
        $item->item_status_id   = $request->item_status_id;
        $item->name             = $request->name;

        
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $fileName);
            $item->image = url('images/' . $fileName);  
        }

        $item->save();

        return response()->json([
            'status'  => true,
            'message' => 'Item created successfully',
            'data'    => $item
        ], 201);
    }

    public function show($id)
    {
        $item = Item::with(['category', 'status', 'prices.size'])->find($id);
        if (!$item) {
            return response()->json(['status' => false, 'message' => 'Item not found'], 404);
        }
        return response()->json(['status' => true, 'data' => $item], 200);
    }

    public function update(Request $request, $id)
    {
        $item = Item::find($id,['*']);
        if (!$item) {
            return response()->json(['status' => false, 'message' => 'Item not found'], 404);
        }

        $request->validate([
            'item_category_id' => 'required|exists:item_categories,_id',
            'item_status_id'   => 'required|exists:item_statuses,_id',
            'name'             => 'required|string|max:255',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $item->item_category_id = $request->item_category_id;
        $item->item_status_id   = $request->item_status_id;
        $item->name             = $request->name;

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $fileName);
            $item->image = url('images/' . $fileName);
        }

        $item->save();

        return response()->json([
            'status'  => true,
            'message' => 'Item updated successfully',
            'data'    => $item
        ], 200);
    }

    public function destroy($id)
    {
        $item = Item::find($id,['*']);
        if (!$item) {
            return response()->json(['status' => false, 'message' => 'Item not found'], 404);
        }

        $item->delete();
        return response()->json(['status' => true, 'message' => 'Item deleted successfully'], 200);
    }
}