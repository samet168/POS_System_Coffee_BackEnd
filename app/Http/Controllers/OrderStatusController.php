<?php

namespace App\Http\Controllers;

use App\Models\OrderStatus;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'message' => 'Order statuses retrieved successfully',
            'data' => OrderStatus::all()
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate(['status_name' => 'required|string|max:100']);
        $status = OrderStatus::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Order status created successfully',
            'data' => $status
        ], 201);
    }

    public function show($id)
    {
        $status = OrderStatus::find($id,['*']);
        if (!$status) return response()->json(['status' => false, 'message' => 'Order status not found'], 404);
        return response()->json(['status' => true, 'data' => $status], 200);
    }

    public function update(Request $request, $id)
    {
        $status = OrderStatus::find($id,['*']);
        if (!$status) return response()->json(['status' => false, 'message' => 'Order status not found'], 404);

        $request->validate(['status_name' => 'required|string|max:100']);
        $status->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Order status updated successfully',
            'data' => $status
        ], 200);
    }

    public function destroy($id)
    {
        $status = OrderStatus::find($id, ['*']);
        if (!$status) return response()->json(['status' => false, 'message' => 'Order status not found'], 404);

        $status->delete();
        return response()->json(['status' => true, 'message' => 'Order status deleted successfully'], 200);
    }
}