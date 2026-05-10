<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'message' => 'Sizes retrieved successfully',
            'data' => Size::all()
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'size_name' => 'required|string',
            'size_code' => 'required|string|unique:sizes,size_code'
        ]);

        $size = Size::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Size created successfully',
            'data' => $size
        ], 201);
    }

    public function show($id)
    {
        $size = Size::find($id,['*']);
        if (!$size) {
            return response()->json(['status' => false, 'message' => 'Size not found'], 404);
        }
        return response()->json(['status' => true, 'data' => $size], 200);
    }

    public function update(Request $request, $id)
    {
        $size = Size::find($id, ['*']);
        if (!$size) {
            return response()->json(['status' => false, 'message' => 'Size not found'], 404);
        }

        $request->validate(['size_name' => 'required|string']);
        $size->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Size updated successfully',
            'data' => $size
        ], 200);
    }

    public function destroy($id)
    {
        $size = Size::find($id, ['*']);
        if (!$size) return response()->json(['status' => false, 'message' => 'Size not found'], 404);

        $size->delete();
        return response()->json(['status' => true, 'message' => 'Size deleted successfully'], 200);
    }
}