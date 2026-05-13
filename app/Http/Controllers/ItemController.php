<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ItemCategory;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with('category')
                     ->latest()
                     ->paginate(20);

        return response()->json([
            'status'  => true,
            'message' => 'Items retrieved successfully',
            'data'    => $items
        ]);
    }

// public function list(Request $request)
// {
//     try {
//         // ទាញ Category ទាំងអស់
//         $categories = ItemCategory::latest()->get();

//         $result = [];

//         foreach ($categories as $category) {
//             $query = Item::query();

//             // Filter Items តាម Category
//             $query->where('item_category_id', $category->_id)
//                   ->orWhere('item_category_id', (string)$category->_id);

//             // Global Search
//             if ($request->filled('search')) {
//                 $query->searchText($request->search);
//             }

//             // Filter by Status
//             if ($request->filled('status')) {
//                 $query->where('status', $request->status);
//             }

//             $items = $query->orderBy('name', 'asc')->get();

//             $result[] = [
//                 'category_id'   => (string)$category->_id,
//                 'category_name' => $category->name,
//                 'items_count'   => $items->count(),
//                 'items'         => $items->load('category')   
//             ];
//         }

//         return response()->json([
//             'status'  => true,
//             'message' => 'Items retrieved successfully by category',
//             'total_categories' => count($result),
//             'data'    => $result
//         ]);

//     } catch (\Exception $e) {
//         return response()->json([
//             'status'  => false,
//             'message' => 'Error occurred',
//             'error'   => $e->getMessage()
//         ], 500);
//     }
// }


public function list(Request $request)
{
    try {
        $query = Item::query();

       
        if ($request->filled('search')) {
            $query->searchText($request->search);
        }


        if ($request->filled('item_category_id')) {
            $catId = (string) $request->item_category_id;  
            $query->where('item_category_id', $catId);
        }

  
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

  
        $sortBy = $request->get('sort_by', '_id');
        $sortDir = $request->get('sort_dir', 'desc');

        $allowed = ['_id', 'name', 'created_at'];
        if (in_array($sortBy, $allowed)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('_id', 'desc');
        }

      
        $perPage = (int) $request->get('per_page', 10);
        $perPage = max(1, min(100, $perPage));

        $items = $query->paginate($perPage);

        $items->load('category');

        return response()->json([
            'status'       => true,
            'message'      => 'Items retrieved successfully',
            'total'        => $items->total(),
            'current_page' => $items->currentPage(),
            'per_page'     => $items->perPage(),
            'last_page'    => $items->lastPage(),
            'data'         => $items->items()
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
        $validator = Validator::make($request->all(), [
            'item_category_id' => 'required|exists:item_categories,id',
            'name'             => 'required|string|max:255',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'status'           => 'nullable|in:In Stock,Out of Stock',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $request->only(['item_category_id', 'name', 'status']);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . rand(100000, 999999) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('images/items');

            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $fileName);
            $data['image'] = url('images/items/' . $fileName);
        }

        $item = Item::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Item created successfully',
            'data'    => $item->load('category')
        ], 201);
    }

    public function show($id)
    {
        $item = Item::with('category')->find($id);

        if (!$item) {
            return response()->json([
                'status'  => false,
                'message' => 'Item not found'
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Item retrieved successfully',
            'data'    => $item
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = Item::find($id, ['*']);

        if (!$item) {
            return response()->json([
                'status'  => false,
                'message' => 'Item not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'item_category_id' => 'nullable|exists:item_categories,id',
            'name'             => 'string|max:255',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'status'           => 'nullable|in:In Stock,Out of Stock',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $request->only(['item_category_id', 'name', 'status']);


        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . rand(100000, 999999) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('images/items');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $fileName);
            $data['image'] = url('images/items/' . $fileName);


            if ($item->image) {
                $oldImagePath = public_path(str_replace(url('/'), '', $item->image));
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
        }

        $item->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'Item updated successfully',
            'data'    => $item->fresh()->load('category')
        ]);
    }

    public function destroy($id)
    {
        $item = Item::find($id, ['*']);

        if (!$item) {
            return response()->json([
                'status'  => false,
                'message' => 'Item not found'
            ], 404);
        }

        if ($item->image) {
            $oldImagePath = public_path(str_replace(url('/'), '', $item->image));
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        $item->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Item deleted successfully'
        ], 200);
    }
}