<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ItemCategory;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
public function index()
{
    $items = Item::with(['category', 'sizePrices.size'])
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
//         $categories = ItemCategory::latest()->get();

//         $result = [];

//         foreach ($categories as $category) {
//             $query = Item::query();

//             $query->where('item_category_id', $category->_id)
//                   ->orWhere('item_category_id', (string)$category->_id);

//             if ($request->filled('search')) {
//                 $query->searchText($request->search);
//             }

//             if ($request->filled('status')) {
//                 $query->where('status', $request->status);
//             }

//             $items = $query->with(['category', 'sizePrices.size'])
//                            ->orderBy('name', 'asc')
//                            ->get();

//             $result[] = [
//                 'category_id'   => (string)$category->_id,
//                 'category_name' => $category->name,
//                 'items_count'   => $items->count(),
//                 'items'         => $items->map(function ($item) {
//                     return [
//                         'id'     => $item->id,
//                         'name'   => $item->name,
//                         'image'  => $item->image,
//                         'status' => $item->status,
//                         'prices' => $item->sizePrices->map(function ($price) {
//                             return [
//                                 'size'  => $price->size->size_name ?? null,
//                                 'code'  => $price->size->size_code ?? null,
//                                 'price' => $price->price,
//                             ];
//                         }),
//                     ];
//                 }),
//             ];
//         }

//         return response()->json([
//             'status'           => true,
//             'message'          => 'Items retrieved successfully by category',
//             'total_categories' => count($result),
//             'data'             => $result
//         ]);

//     } catch (\Exception $e) {
//         return response()->json([
//             'status'  => false,
//             'message' => 'Error occurred',
//             'error'   => $e->getMessage()
//         ], 500);
//     }
// }
// public function list(Request $request)
// {
//     try {
//         $categories = ItemCategory::latest()->get();

//         $result = [];

//         foreach ($categories as $category) {

//             $query = Item::query()
//                 ->where(function ($q) use ($category) {
//                     $q->where('item_category_id', $category->_id)
//                       ->orWhere('item_category_id', (string)$category->_id);
//                 });

//             // search (safe grouping)
//             if ($request->filled('search')) {
//                 $search = $request->search;

//                 $query->where(function ($q) use ($search) {
//                     $q->where('name', 'like', "%$search%");
//                 });
//             }

//             // status filter
//             if ($request->filled('status')) {
//                 $query->where('status', $request->status);
//             }

//             $items = $query->with(['category', 'sizePrices.size'])
//                            ->orderBy('name', 'asc')
//                            ->get();

//             $result[] = [
//                 'category_id'   => (string)$category->_id,
//                 'category_name' => $category->name,
//                 'items_count'   => $items->count(),
//                 'items'         => $items->map(function ($item) {
//                     return [
//                         'id'     => $item->id,
//                         'name'   => $item->name,
//                         'image'  => $item->image,
//                         'status' => $item->status,
//                         'prices' => $item->sizePrices->map(function ($price) {
//                             return [
//                                 'size'  => $price->size->size_name ?? null,
//                                 'code'  => $price->size->size_code ?? null,
//                                 'price' => $price->price,
//                             ];
//                         }),
//                     ];
//                 }),
//             ];
//         }

//         return response()->json([
//             'status'           => true,
//             'message'          => 'Items retrieved successfully by category',
//             'total_categories' => count($result),
//             'data'             => $result
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
            $categories = ItemCategory::with(['items' => function ($query) use ($request) {

                $query->with(['category', 'sizePrices.size'])
                    ->orderBy('name', 'asc');

                if ($request->filled('search')) {
                    $query->where('name', 'like', "%{$request->search}%");
                }

                if ($request->filled('status')) {
                    $query->where('status', $request->status);
                }
            }])->latest()->get();

            return response()->json([
                'status' => true,
                'message' => 'Items by category',
                'data' => $categories->map(function ($category) {
                    return [
                        'category_id'   => $category->id,
                        'category_name' => $category->name,
                        'items_count'   => $category->items->count(),
                        'items' => $category->items->map(function ($item) {
                            return [
                                'id'     => $item->id,
                                'name'   => $item->name,
                                'image'  => $item->image,
                                'status' => $item->status,
                                'prices' => $item->sizePrices->map(function ($price) {
                                    return [
                                        'size'  => $price->size->size_name ?? null,
                                        'code'  => $price->size->size_code ?? null,
                                        'price' => $price->price,
                                    ];
                                }),
                            ];
                        })
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
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


// public function store(Request $request)
// {
//     $validator = Validator::make($request->all(), [
//         'item_category_id' => 'required|exists:item_categories,id',
//         'name'             => 'required|string|max:255',
//         'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
//         'status'           => 'nullable|in:In Stock,Out of Stock',
//     ]);

//     if ($validator->fails()) {
//         return response()->json([
//             'status'  => false,
//             'message' => 'Validation failed',
//             'errors'  => $validator->errors()
//         ], 422);
//     }

//     $data = $request->only(['item_category_id', 'name', 'status']);

//     // =========================
//     // IMAGE UPLOAD (AWS + LOCAL)
//     // =========================
//     if ($request->hasFile('image')) {

//         $file = $request->file('image');
//         $fileName = 'items/' . time() . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();

//         try {
//             // 👉 Upload to AWS S3 first
//             $path = $file->storeAs('items', basename($fileName), 's3');

//             Storage::disk('s3')->setVisibility($path, 'public');

//             $data['image'] = Storage::disk('s3')->url($path);

//         } catch (\Exception $e) {

//             // 👉 fallback to local storage
//             $destinationPath = public_path('images/items');

//             if (!file_exists($destinationPath)) {
//                 mkdir($destinationPath, 0755, true);
//             }

//             $file->move($destinationPath, basename($fileName));

//             $data['image'] = url('images/items/' . basename($fileName));
//         }
//     }

//     $item = Item::create($data);

//     return response()->json([
//         'status'  => true,
//         'message' => 'Item created successfully',
//         'data'    => $item->load('category')
//     ], 201);
// }
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