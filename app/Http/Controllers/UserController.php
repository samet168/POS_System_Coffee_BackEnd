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

// public function list(Request $request)
// {
//     $request->validate([
//         'name'     => 'nullable|string|max:100',
//         'email'    => 'nullable|string|max:100',
//         'role'     => 'nullable|in:admin,user',
//         'per_page' => 'nullable|integer|min:1|max:100',
//         'sort_by'  => 'nullable|in:name,email,created_at',
//         'sort_dir' => 'nullable|in:asc,desc',
//     ]);

//     $query = User::query();

//     // Dynamic Filtering
//     if ($request->filled('name')) {
//         $query->where('name', 'like', '%' . $request->name . '%');
//     }

//     if ($request->filled('email')) {
//         $query->where('email', 'like', '%' . $request->email . '%');
//     }

//     if ($request->filled('role')) {
//         $query->where('role', $request->role);
//     }

//     // Sorting
//     $sortBy = $request->get('sort_by', 'created_at');
//     $sortDir = $request->get('sort_dir', 'desc');
//     $query->orderBy($sortBy, $sortDir);

//     // Pagination
//     $perPage = $request->get('per_page', 10);
//     $users = $query->paginate($perPage);

//     return response()->json([
//         'status'  => true,
//         'message' => 'Users retrieved successfully',
//         'data'    => $users,
//     ]);
// }
public function list(Request $request)
{
    try {
        $users = User::query()
            // Global Search
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->searchText($request->search);
            })

            // Filter by Role
            ->when($request->filled('role'), function ($query) use ($request) {
                $query->where('role', $request->role);
            })

            // Sorting
            ->when($request->filled('sort_by'), function ($query) use ($request) {
                $sortBy = $request->sort_by;
                $sortDir = $request->get('sort_dir', 'desc');
                $allowed = ['name', 'email', 'role', 'created_at'];

                if (in_array($sortBy, $allowed)) {
                    $query->orderBy($sortBy, $sortDir);
                }
            }, function ($query) {
                // Default sorting
                $query->orderBy('_id', 'desc');
            })

            // Pagination
            ->paginate((int) $request->get('per_page', 10));

        return response()->json([
            'status'       => true,
            'message'      => 'Users retrieved successfully',
            'total'        => $users->total(),
            'current_page' => $users->currentPage(),
            'per_page'     => $users->perPage(),
            'last_page'    => $users->lastPage(),
            'data'         => $users->items()
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => false,
            'message' => 'Error occurred',
            'error'   => $e->getMessage()
        ], 500);
    }
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