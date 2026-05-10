<?php

namespace App\Http\Controllers;

use App\Models\PaymentStatus;
use Illuminate\Http\Request;

class PaymentStatusController extends Controller
{
    public function index()
    {
        $statuses = PaymentStatus::all();
        return response()->json([
            'status' => true,
            'message' => 'Payment statuses retrieved successfully',
            'data' => $statuses
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'status_name' => 'required|string|max:100|unique:payment_statuses,status_name'
        ]);

        $status = PaymentStatus::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Payment status created successfully',
            'data' => $status
        ], 201);
    }

    public function show($id)
    {
        $status = PaymentStatus::find($id, ['*']    );
        if (!$status) {
            return response()->json([
                'status' => false,
                'message' => 'Payment status not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $status
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $status = PaymentStatus::find($id,['*']);
        if (!$status) {
            return response()->json([
                'status' => false,
                'message' => 'Payment status not found'
            ], 404);
        }

        $request->validate([
            'status_name' => 'required|string|max:100|unique:payment_statuses,status_name,' . $id
        ]);

        $status->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Payment status updated successfully',
            'data' => $status
        ], 200);
    }

    public function destroy($id)
    {
        $status = PaymentStatus::find($id, ['*']);
        if (!$status) {
            return response()->json([
                'status' => false,
                'message' => 'Payment status not found'
            ], 404);
        }

        $status->delete();

        return response()->json([
            'status' => true,
            'message' => 'Payment status deleted successfully'
        ], 200);
    }
}