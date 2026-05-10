<?php

namespace App\Http\Controllers;

use App\Models\ItemStatus;
use Illuminate\Http\Request;

class ItemStatusController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'message' => 'Item statuses retrieved successfully',
            'data' => ItemStatus::all()
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate(['status_name' => 'required|string|max:100']);
        $status = ItemStatus::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Item status created successfully',
            'data' => $status
        ], 201);
    }

    public function show($id)
    {
        $status = ItemStatus::find($id,['*']);
        if (!$status) return response()->json(['status' => false, 'message' => 'Item status not found'], 404);
        return response()->json(['status' => true, 'data' => $status], 200);
    }

    public function update(Request $request, $id)
    {
        $status = ItemStatus::find($id,['*']);
        if (!$status) return response()->json(['status' => false, 'message' => 'Item status not found'], 404);

        $request->validate(['status_name' => 'required|string|max:100']);
        $status->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Item status updated successfully',
            'data' => $status
        ], 200);
    }

    public function destroy($id)
    {
        $status = ItemStatus::find($id,['*']);
        if (!$status) return response()->json(['status' => false, 'message' => 'Item status not found'], 404);

        $status->delete();
        return response()->json(['status' => true, 'message' => 'Item status deleted successfully'], 200);
    }
}