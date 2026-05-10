<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // GET ALL USERS
    public function index()
    {
        $users = User::all();

        return response()->json([
            'status' => true,
            'message' => 'Users retrieved successfully',
            'data' => $users
        ], 200);
    }

    // CREATE USER
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role'     => 'nullable|in:admin,user',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $user = new User();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->role = $request->role ?? 'user';

        // Upload Image
        if ($request->hasFile('image')) {

            $file = $request->file('image');

            $fileName = time() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('images'), $fileName);

            $user->image = url('images/' . $fileName);
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    // SHOW USER
    public function show($id)
    {
        $user = User::find($id, ['*']);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $user
        ], 200);
    }

    // UPDATE USER
    public function update(Request $request, $id)
    {
        $user = User::find($id, ['*']);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role'  => 'nullable|in:admin,user',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role ?? $user->role;

        // Update Password
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        // Update Image
        if ($request->hasFile('image')) {

            $file = $request->file('image');

            $fileName = time() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('images'), $fileName);

            $user->image = url('images/' . $fileName);
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ], 200);
    }

    // DELETE USER
    public function destroy($id)
    {
        $user = User::find($id ,['*']);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'User deleted successfully'
        ], 200);
    }
}