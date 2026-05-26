<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class UserController extends Controller
{
    
public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => $request->user()
        ]);
    }



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

    // ================= CLOUDINARY IMAGE =================
    if ($request->hasFile('image')) {

        // ❌ (optional) delete old image from Cloudinary
        if ($user->image) {
            preg_match('/\/upload\/(?:v\d+\/)?(.+)\./', $user->image, $match);
            if (isset($match[1])) {
                Cloudinary::destroy($match[1]);
            }
        }

        // ✅ upload new image
        $uploadedFile = Cloudinary::uploadApi()->upload(
            $request->file('image')->getRealPath(),
            [
                'folder' => 'profile-pictures'
            ]
        );

        $user->image = $uploadedFile['secure_url'];
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
        "name"     => "required|string|max:255",
        "email"    => "required|email|unique:users,email",
        "password" => "required|string|min:8",
        "image"    => "nullable|image|mimes:jpeg,png,jpg,webp|max:2048",
    ]);

    $image_url = null;

    if ($request->hasFile('image')) {

        $uploadedFile = Cloudinary::uploadApi()->upload(
            $request->file('image')->getRealPath(),
            [
                'folder' => 'profile-pictures'
            ]
        );

        $image_url = $uploadedFile['secure_url'];
    }

    $user = User::create([
        "name"     => $request->name,
        "email"    => $request->email,
        "password" => Hash::make($request->password),
        "image"    => $image_url,
        "role"     => $request->role ?? 'user',
    ]);

    return response()->json([
        'message' => 'Create user successfully',
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
    $user = User::find($id);

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'User not found'
        ], 404);
    }

    // VALIDATION
    $request->validate([
        'name'  => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
        'role'  => 'nullable|in:admin,user',
        'password' => 'nullable|min:6',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ]);

    // UPDATE
    $user->name = $request->name;
    $user->email = $request->email;
    $user->role = $request->role ?? $user->role;

    // PASSWORD
    if ($request->filled('password')) {
        $user->password = bcrypt($request->password);
    }

    // IMAGE
    if ($request->hasFile('image')) {

        // delete old image
        if ($user->image_public_id) {
            Cloudinary::uploadApi()->destroy(
                $user->image_public_id
            );
        }

        // upload new image
        $uploaded = Cloudinary::uploadApi()->upload(
            $request->file('image')->getRealPath(),
            [
                'folder' => 'users'
            ]
        );

        $user->image = $uploaded['secure_url'];
        $user->image_public_id = $uploaded['public_id'];
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