<?php

namespace App\Http\Controllers;

use App\Models\SugarLevel;
use Illuminate\Http\Request;

class SugarLevelController extends Controller
{
    public function index()
    {
        $data = SugarLevel::all();
        return response()->json(['status' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'level_name' => 'required|string|unique:sugar_levels,level_name'
        ]);

        $sugar = SugarLevel::create(['level_name' => $request->level_name]);

        return response()->json([
            'status' => true,
            'message' => 'Sugar level created successfully',
            'data' => $sugar
        ], 201);
    }

    public function show($id)
    {
        $sugar = SugarLevel::find($id, ['*']);
        if (!$sugar) return response()->json(['status' => false, 'message' => 'Not found'], 404);
        return response()->json(['status' => true, 'data' => $sugar]);
    }

    public function update(Request $request, $id)
    {
        $sugar = SugarLevel::find($id, ['*']);
        if (!$sugar) return response()->json(['status' => false, 'message' => 'Not found'], 404);

        $sugar->update($request->only(['level_name']));
        return response()->json(['status' => true, 'message' => 'Updated', 'data' => $sugar]);
    }

    public function destroy($id)
    {
        $sugar = SugarLevel::find($id, ['*']);
        if (!$sugar) return response()->json(['status' => false, 'message' => 'Not found'], 404);

        $sugar->delete();
        return response()->json(['status' => true, 'message' => 'Deleted successfully']);
    }
}