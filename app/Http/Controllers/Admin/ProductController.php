<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductPricing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Imports\ProductsImport;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('pricings')->get();
        $allCategories = DB::table('categories')->pluck('name', 'id');
        $tiers = DB::table('tiers')->pluck('type', 'id');
        
        return view('admin.products.index', compact('products', 'tiers','allCategories'));
    }

    public function create()
    {
        $tiers = DB::table('tiers')->pluck('type', 'id'); 
        $category = DB::table('categories')->pluck('name', 'id');

        return view('admin.products.create', compact('tiers','category'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'product_type'    => 'required|in:b2b,b2c',
            'front_image'     => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'back_image'      => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'side_image'      => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'brewing_info'    => 'nullable|string',
            // 'stock_quantity'  => 'required|integer|min:0',
            'category_id'     => 'required|array',
            'category_id.*'   => 'exists:categories,id',
            'variants'        => 'required|array',
            'variants.*.unit' => 'required|string|max:50',
            'variants.*.price'=> 'required|numeric|min:0',
            'variants.*.stock'=> 'required|numeric|min:1',
        ]);

        $data = $request->only([
            'name', 'description', 'product_type',
            'brewing_info'
        ]);

        $data['category_id'] = json_encode($request->category_id);

        foreach (['front_image', 'back_image', 'side_image'] as $imageField) {
            if ($request->hasFile($imageField)) {
                $data[$imageField] = $request->file($imageField)->store('products', 'public');
            }
        }

        // dd($data);
        $product = Product::create($data);

        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                $product->variants()->create($variant);
            }
        }

        // ✅ Dynamic redirect based on product type
        $redirectRoute = $request->product_type === 'b2b' 
            ? 'admin.products.b2b.index' 
            : 'admin.products.b2c.index';

        return redirect()->route($redirectRoute)->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $tiers = DB::table('tiers')->pluck('type', 'id');
        $category = DB::table('categories')->pluck('name', 'id');

        // Decode category IDs stored in the product table
        $selectedCategories = json_decode($product->category_id, true) ?? [];

        // Load variants
        $variants = $product->variants ?? collect();

        return view('admin.products.edit', compact('product', 'tiers', 'category', 'selectedCategories', 'variants'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'product_type'    => 'required|string|in:b2b,b2c',
            'front_image'     => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
            'back_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
            'side_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
            'brewing_info'    => 'nullable|string',
            // 'stock_quantity'  => 'required|integer|min:0',
            'category_id'     => 'required|array',
            'category_id.*'   => 'exists:categories,id',
            'variants'        => 'required|array',
            'variants.*.unit' => 'required|string|max:50',
            'variants.*.price'=> 'required|numeric|min:0',
            'variants.*.stock'=> 'required|numeric|min:1',
        ]);
        $data = $request->only([
            'name', 'description', 'product_type',
            'brewing_info'
        ]);
        $data['category_id'] = json_encode($request->category_id);

        // Handle image updates
        foreach (['front_image', 'back_image', 'side_image'] as $imageField) {
            if ($request->hasFile($imageField)) {
                if ($product->$imageField && Storage::disk('public')->exists($product->$imageField)) {
                    Storage::disk('public')->delete($product->$imageField);
                }
                $data[$imageField] = $request->file($imageField)->store('products', 'public');
            }
        }

        $product->update($data);

        // Variants (keep logic same)
        $product->variants()->delete();
        foreach ($request->variants as $variant) {
            $product->variants()->create($variant);
        }

        // ✅ Dynamic redirect based on product type
        $redirectRoute = $request->product_type === 'b2b' 
            ? 'admin.products.b2b.index' 
            : 'admin.products.b2c.index';

        return redirect()->route($redirectRoute)->with('success', 'Product created successfully.');
    }

    public function destroy(Product $product)
    {
        $product->pricings()->delete();
        $product->delete();
         $redirectRoute = $product->product_type === 'b2b' 
            ? 'admin.products.b2b.index' 
            : 'admin.products.b2c.index';
         return redirect()->route($redirectRoute)->with('success', 'Product created successfully.');
    }

    // public function b2bIndex()
    // {
    //     $products = Product::where('product_type', 'b2b')->with('variants')->get();
    //     $allCategories = DB::table('categories')->pluck('name', 'id');
    //     $tiers = DB::table('tiers')->pluck('type', 'id');
    //     return view('admin.products.b2b.index', compact('products', 'tiers', 'allCategories'));
    // }

    public function b2bIndex()
    {
        $products = Product::where('product_type', 'b2b')
            ->with('variants')
            ->orderBy('position', 'asc')
            ->paginate(10); 

        $allCategories = DB::table('categories')->pluck('name', 'id')->toArray();
        $tiers = DB::table('tiers')->pluck('type', 'id')->toArray();

        return view('admin.products.b2b.index', compact('products', 'tiers', 'allCategories'));
    }
    
public function reorder(Request $request)
{
    $request->validate([
        'order' => 'required|array',
        'order.*' => 'integer|exists:products,id',
    ]);

    foreach ($request->order as $index => $productId) {
        Product::where('id', $productId)->update([
            'position' => $index + 1
        ]);
    }

    return response()->json(['success' => true]);
}


    public function b2bCreate()
    {
        $tiers = DB::table('tiers')->pluck('type', 'id');
        $category = DB::table('categories')->pluck('name', 'id');
        return view('admin.products.b2b.create', compact('tiers', 'category'));
    }

    public function b2bStore(Request $request)
    {
        $request->merge(['product_type' => 'b2b']);
        return $this->store($request);
    }

    public function b2cIndex()
    {
        $products = Product::where('product_type', 'b2c')
            ->with('variants')
            ->orderBy('position', 'asc')
            ->paginate(10); 

        $allCategories = DB::table('categories')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        $tiers = DB::table('tiers')
            ->orderBy('id', 'asc')
            ->pluck('type', 'id')
            ->toArray();

        return view('admin.products.b2c.index', compact('products', 'tiers', 'allCategories'));
    }


    public function b2cCreate()
    {
        $tiers = DB::table('tiers')->pluck('type', 'id');
        $category = DB::table('categories')->pluck('name', 'id');
        return view('admin.products.b2c.create', compact('tiers', 'category'));
    }

    public function b2cStore(Request $request)
    {
        $request->merge(['product_type' => 'b2c']);
        return $this->store($request);
    }

    public function bulkUploadForm()
    {
        return view('admin.products.bulk-upload');
    }
  
    public function bulkUploadStore(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'images_zip' => 'required|mimes:zip'
        ]);

        $extractPath = storage_path('app/public/products');

        // ✅ Ensure folder exists (but do NOT delete old files)
        if (!File::exists($extractPath)) {
            File::makeDirectory($extractPath, 0777, true, true);
        }

        // ✅ Extract ZIP (new images overwrite if same file names)
        $zip = new \ZipArchive;
        if ($zip->open($request->file('images_zip')) === TRUE) {
            $zip->extractTo($extractPath);
            $zip->close();
        } else {
            return back()->with('error', 'Failed to upload images zip.');
        }

        // ✅ Import Excel
        Excel::import(new ProductsImport, $request->file('file'));

        return back()->with('success', 'Bulk products uploaded successfully.');
    }

    public function mostSellingProducts()
    {
        try {

            $products = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->select(
                    'products.id',
                    DB::raw('COUNT(order_items.product_id) as sold_count')
                )
                ->groupBy('products.id')
                ->orderByDesc('sold_count')
                ->limit(20)
                ->get(); // <-- get instead of pluck to carry sold_count

            // Get product IDs
            $productIds = $products->pluck('id');

            $data = Product::with(['variants', 'reviews.customer'])
                ->whereIn('id', $productIds)
                ->get()
                ->map(function ($product) use ($products) {
                    $product->sold_count = $products->where('id', $product->id)->first()->sold_count;
                    $product->average_rating = round($product->reviews->avg('rating') ?? 0, 1);
                    $product->review_count   = $product->reviews->count();
                    return $product;
                })
                ->sortByDesc('sold_count')
                ->values();

            return view('admin.products.best_selling_products', compact('data'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}