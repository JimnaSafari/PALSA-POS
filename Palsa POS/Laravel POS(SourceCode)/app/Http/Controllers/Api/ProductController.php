<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\InventoryService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private InventoryService $inventoryService,
        private FileUploadService $fileUploadService
    ) {}

    public function index(Request $request)
    {
        $query = Product::with(['category', 'ratings'])
            ->where('is_active', true);

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // In stock filter
        if ($request->boolean('in_stock_only')) {
            $query->inStock();
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate($request->get('per_page', 15));

        $formattedProducts = $products->getCollection()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => [
                    'amount' => $product->price,
                    'formatted' => \App\Helpers\CurrencyHelper::formatKESSymbol($product->price),
                    'currency' => 'KES'
                ],
                'purchase_price' => [
                    'amount' => $product->purchase_price,
                    'formatted' => \App\Helpers\CurrencyHelper::formatKESSymbol($product->purchase_price),
                    'currency' => 'KES'
                ],
                'category' => $product->category,
                'image' => $product->image,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'stock_count' => $product->count,
                'available_stock' => $product->available_stock,
                'is_active' => $product->is_active,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $formattedProducts,
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ]
            ]
        ]);
    }

    public function show($id)
    {
        $product = Product::with(['category', 'comments.user', 'ratings'])
            ->findOrFail($id);

        $averageRating = $product->ratings->avg('count');
        $ratingCount = $product->ratings->count();

        return response()->json([
            'success' => true,
            'data' => [
                'product' => $product,
                'average_rating' => round($averageRating ?? 0, 1),
                'rating_count' => $ratingCount,
                'available_stock' => $product->available_stock,
                'profit_margin' => $product->profit_margin,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:products',
            'price' => 'required|numeric|min:0.01',
            'purchase_price' => 'required|numeric|min:0|lt:price',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:1000',
            'count' => 'required|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:50|unique:products',
            'barcode' => 'nullable|string|max:50|unique:products',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only([
            'name', 'price', 'purchase_price', 'category_id', 
            'description', 'count', 'min_stock_level', 'sku', 'barcode'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $this->fileUploadService->uploadProductImage($request->file('image'));
        }

        $product = Product::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => ['product' => $product->load('category')]
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $id,
            'price' => 'required|numeric|min:0.01',
            'purchase_price' => 'required|numeric|min:0|lt:price',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:1000',
            'count' => 'required|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:50|unique:products,sku,' . $id,
            'barcode' => 'nullable|string|max:50|unique:products,barcode,' . $id,
        ]);

        $data = $request->only([
            'name', 'price', 'purchase_price', 'category_id', 
            'description', 'count', 'min_stock_level', 'sku', 'barcode'
        ]);

        $product->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => ['product' => $product->load('category')]
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Check if product has orders
        if ($product->orders()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete product with existing orders'
            ], 422);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

    public function uploadImage(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $filename = $this->fileUploadService->uploadProductImage(
            $request->file('image'),
            $product->image
        );

        $product->update(['image' => $filename]);

        return response()->json([
            'success' => true,
            'message' => 'Product image updated successfully',
            'data' => ['image_url' => asset('storage/products/' . $filename)]
        ]);
    }

    public function lowStock()
    {
        $lowStockProducts = $this->inventoryService->getLowStockProducts();

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $lowStockProducts,
                'count' => $lowStockProducts->count()
            ]
        ]);
    }

    public function adjustStock(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'adjustment' => 'required|integer',
            'reason' => 'required|string|max:255',
        ]);

        $newStock = $product->count + $request->adjustment;

        if ($newStock < 0) {
            return response()->json([
                'success' => false,
                'message' => 'Stock adjustment would result in negative stock'
            ], 422);
        }

        $this->inventoryService->updateStock($id, $newStock);

        return response()->json([
            'success' => true,
            'message' => 'Stock adjusted successfully',
            'data' => [
                'old_stock' => $product->count,
                'adjustment' => $request->adjustment,
                'new_stock' => $newStock
            ]
        ]);
    }
}