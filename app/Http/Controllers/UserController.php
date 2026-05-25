<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
class UserController extends Controller
{
    
public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => $request->user()
        ]);
    }

    // ================= UPDATE PROFILE =================
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        // VALIDATION
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // UPDATE BASIC INFO
        $user->name = $request->name;
        $user->email = $request->email;

        // PASSWORD (OPTIONAL)
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        // ================= IMAGE =================
        if ($request->hasFile('image')) {

            // DELETE OLD IMAGE
            if ($user->image) {
                $oldPath = public_path(parse_url($user->image, PHP_URL_PATH));

                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            // UPLOAD NEW IMAGE
            $file = $request->file('image');
            $fileName = time() . '_' . rand(1000,9999) . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('images/users'), $fileName);

            $user->image = url('images/users/' . $fileName);
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }

public function changePassword(Request $request)
{
    try {

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        // check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is wrong'
            ], 400);
        }

        // update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password changed successfully'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => $e->getMessage()
        ], 500);
    }
}

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

    // =========================
    // IMAGE UPLOAD (AWS + LOCAL)
    // =========================
    if ($request->hasFile('image')) {

        $file = $request->file('image');
        $fileName = 'users/' . time() . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();

        try {
            // 👉 Upload to AWS S3
            $path = $file->storeAs('users', basename($fileName), 's3');

            Storage::disk('s3')->setVisibility($path, 'public');

            $user->image = Storage::disk('s3')->url($path);

        } catch (\Exception $e) {

            // 👉 fallback to local
            $destinationPath = public_path('images/users');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, basename($fileName));

            $user->image = url('images/users/' . basename($fileName));
        }
    }

    $user->save();

    return response()->json([
        'status'  => true,
        'message' => 'User created successfully',
        'data'    => $user
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