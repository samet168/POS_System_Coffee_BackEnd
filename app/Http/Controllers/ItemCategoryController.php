<?php

namespace App\Http\Controllers;

use App\Models\ItemCategory;
use Illuminate\Http\Request;

class ItemCategoryController extends Controller
{
    public function index()
    {
        $categories = ItemCategory::all();
        return response()->json([
            'status' => true,
            'message' => 'Item categories retrieved successfully',
            'data' => $categories
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $category = ItemCategory::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Item category created successfully',
            'data' => $category
        ], 201);
    }

    public function show($id)
    {
        $category = ItemCategory::find($id,['*']);
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Item category not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $category
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $category = ItemCategory::find($id,['*']);
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Item category not found'
            ], 404);
        }

        $request->validate(['name' => 'required|string|max:255']);

        $category->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Item category updated successfully',
            'data' => $category
        ], 200);
    }

    public function destroy($id)
    {
        $category = ItemCategory::find($id,['*']);
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Item category not found'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'status' => true,
            'message' => 'Item category deleted successfully'
        ], 200);
    }
}