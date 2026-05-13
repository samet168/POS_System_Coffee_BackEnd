<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    
    public function index()
    {
        $users = User::all();

        return response()->json([
            'status' => true,
            'message' => 'Users retrieved successfully',
            'data' => $users
        ], 200);
    }

    public function list(Request $request)
    {
        $users = User::query()

            
            ->when($request->name, function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->name . '%');
            })

            
            ->when($request->email, function ($query) use ($request) {
                $query->where('email', 'like', '%' . $request->email . '%');
            })

            
            ->when($request->role, function ($query) use ($request) {
                $query->where('role', $request->role);
            })

            ->latest()
            ->paginate(20);

        return response()->json([
            'status'  => true,
            'message' => 'Users retrieved successfully',
            'data'    => $users
        ]);
    }
    
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

    // IMAGE UPLOAD
    if ($request->hasFile('image')) {

        $file = $request->file('image');
        $fileName = time() . '_' . rand(100000, 999999) . '.' . $file->getClientOriginalExtension();
        $destinationPath = public_path('images/users');

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $fileName);

        $user->image = url('images/users/' . $fileName);
    }

    $user->save();

    return response()->json([
        'status' => true,
        'message' => 'User created successfully',
        'data' => $user
    ], 201);
}

    
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

        
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        
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