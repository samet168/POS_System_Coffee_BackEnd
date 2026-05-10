<?php

namespace App\Http\Controllers;

use App\Models\IceLevel;
use Illuminate\Http\Request;

class IceLevelController extends Controller
{
    public function index()
    {
        $data = IceLevel::all();
        return response()->json(['status' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'level_name' => 'required|string|unique:ice_levels,level_name'
        ]);

        $ice = IceLevel::create(['level_name' => $request->level_name]);

        return response()->json([
            'status' => true,
            'message' => 'Ice level created successfully',
            'data' => $ice
        ], 201);
    }

    public function show($id)
    {
        $ice = IceLevel::find($id, ['*']);
        if (!$ice) return response()->json(['status' => false, 'message' => 'Not found'], 404);
        return response()->json(['status' => true, 'data' => $ice]);
    }

    public function update(Request $request, $id)
    {
        $ice = IceLevel::find($id, ['*']);
        if (!$ice) return response()->json(['status' => false, 'message' => 'Not found'], 404);

        $ice->update($request->only(['level_name']));
        return response()->json(['status' => true, 'message' => 'Updated', 'data' => $ice]);
    }

    public function destroy($id)
    {
        $ice = IceLevel::find($id, ['*']);
        if (!$ice) return response()->json(['status' => false, 'message' => 'Not found'], 404);

        $ice->delete();
        return response()->json(['status' => true, 'message' => 'Deleted successfully']);
    }
}