<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ItemCategory;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
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
    // VALIDATION
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

    // DATA
    $data = $request->only([
        'item_category_id',
        'name',
        'status'
    ]);

    $data['status'] = $data['status'] ?? 'In Stock';

    // IMAGE UPLOAD
    if ($request->hasFile('image')) {

        $uploaded = Cloudinary::uploadApi()->upload(
            $request->file('image')->getRealPath(),
            [
                'folder' => 'items'
            ]
        );

        $data['image'] = $uploaded['secure_url'];

        $data['image_public_id'] = $uploaded['public_id'];
    }

    // CREATE
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
    $item = Item::find($id);

    if (!$item) {
        return response()->json([
            'status'  => false,
            'message' => 'Item not found'
        ], 404);
    }

    // VALIDATION
    $validator = Validator::make($request->all(), [
        'item_category_id' => 'nullable|exists:item_categories,id',
        'name'             => 'nullable|string|max:255',
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

    // DATA
    $data = $request->only([
        'item_category_id',
        'name',
        'status'
    ]);

    // IMAGE UPDATE
    if ($request->hasFile('image')) {

        // delete old image from cloudinary
        if ($item->image_public_id) {
            Cloudinary::uploadApi()->destroy(
                $item->image_public_id
            );
        }

        // upload new image
        $uploaded = Cloudinary::uploadApi()->upload(
            $request->file('image')->getRealPath(),
            [
                'folder' => 'items'
            ]
        );

        $data['image'] = $uploaded['secure_url'];

        $data['image_public_id'] = $uploaded['public_id'];
    }

    // UPDATE ITEM
    $item->update($data);

    return response()->json([
        'status'  => true,
        'message' => 'Item updated successfully',
        'data'    => $item->fresh()->load('category')
    ]);
}


public function destroy($id)
{
    $item = Item::find($id);

    if (!$item) {
        return response()->json([
            'status'  => false,
            'message' => 'Item not found'
        ], 404);
    }

    // DELETE IMAGE FROM CLOUDINARY
    if ($item->image_public_id) {

        Cloudinary::uploadApi()->destroy(
            $item->image_public_id
        );
    }

    // DELETE ITEM
    $item->delete();

    return response()->json([
        'status'  => true,
        'message' => 'Item deleted successfully'
    ], 200);
}
}