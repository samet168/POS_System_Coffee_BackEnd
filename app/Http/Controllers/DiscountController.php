<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::all();

        return response()->json([
            'status' => true,
            'data' => $discounts
        ]);
    }

    public function list(Request $request)
    {
        $discounts = Discount::query()

            ->when($request->name, function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->name . '%');
            })

            ->when($request->type, function ($query) use ($request) {
                $query->where('type', $request->type);
            })

            ->latest()
            ->paginate(20);

        return response()->json([
            'status'  => true,
            'message' => 'Discounts retrieved successfully',
            'data'    => $discounts
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'type'       => 'required|in:percentage,fixed',
            'value'      => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date'
        ]);

        $discount = Discount::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Discount created successfully',
            'data' => $discount
        ], 201);
    }

    public function show($id)
    {
        $discount = Discount::find($id,['*']);
        if (!$discount) return response()->json(['status' => false, 'message' => 'Discount not found'], 404);
        return response()->json(['status' => true, 'data' => $discount], 200);
    }

    public function update(Request $request, $id)
    {
        $discount = Discount::find($id,['*']);
        if (!$discount) return response()->json(['status' => false, 'message' => 'Discount not found'], 404);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0'
        ]);

        $discount->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Discount updated successfully',
            'data' => $discount
        ], 200);
    }

    public function destroy($id)
    {
        $discount = Discount::find($id,['*']);
        if (!$discount) return response()->json(['status' => false, 'message' => 'Discount not found'], 404);

        $discount->delete();
        return response()->json(['status' => true, 'message' => 'Discount deleted successfully'], 200);
    }
}