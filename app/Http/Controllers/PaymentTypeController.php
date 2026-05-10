<?php

namespace App\Http\Controllers;

use App\Models\PaymentType;
use Illuminate\Http\Request;

class PaymentTypeController extends Controller
{
    public function index()
    {
        $types = PaymentType::all();
        return response()->json([
            'status' => true,
            'message' => 'Payment types retrieved successfully',
            'data' => $types
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type_name' => 'required|string|max:100|unique:payment_types,type_name'
        ]);

        $type = PaymentType::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Payment type created successfully',
            'data' => $type
        ], 201);
    }

    public function show($id)
    {
        $type = PaymentType::find($id, ['*']);
        if (!$type) {
            return response()->json([
                'status' => false,
                'message' => 'Payment type not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $type
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $type = PaymentType::find($id, ['*'] );
        if (!$type) {
            return response()->json([
                'status' => false,
                'message' => 'Payment type not found'
            ], 404);
        }

        $request->validate([
            'type_name' => 'required|string|max:100|unique:payment_types,type_name,' . $id
        ]);

        $type->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Payment type updated successfully',
            'data' => $type
        ], 200);
    }

    public function destroy($id)
    {
        $type = PaymentType::find($id,['*']);
        if (!$type) {
            return response()->json([
                'status' => false,
                'message' => 'Payment type not found'
            ], 404);
        }

        $type->delete();

        return response()->json([
            'status' => true,
            'message' => 'Payment type deleted successfully'
        ], 200);
    }
}