<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;

class dashobardController extends Controller
{
    //
    public function stats()
{
    try {
        return response()->json([
            'status' => true,
            'data' => [
                'total_users' => User::count(),
                'total_items' => Item::count(),
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
}
