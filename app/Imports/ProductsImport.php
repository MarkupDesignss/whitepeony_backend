<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\File;

class ProductsImport implements ToModel, WithHeadingRow
{
    protected $imageBasePath;

    public function __construct()
    {
        // path where ZIP was extracted
        $this->imageBasePath = storage_path('app/public/products/');
    }

    private function getRealImageName($imageName)
    {
        if (empty($imageName)) return null;

        // Remove spaces
        $imageName = trim($imageName);

        // Get name without extension
        $nameOnly = pathinfo($imageName, PATHINFO_FILENAME);

        // Find actual file with any extension
        $files = glob($this->imageBasePath . $nameOnly . '.*');

        if (!empty($files)) {
            // Return only filename (not full path)
            return 'products/' . basename($files[0]);
        }

        return null; // If not found
    }

    public function model(array $row)
    {
        // Convert comma separated categories into array & encode
        $categoryIds = isset($row['category_id'])
            ? json_encode(array_map('trim', explode(',', $row['category_id'])))
            : json_encode([]);

        // ✅ Dynamically resolve correct image extension
        $frontImage = $this->getRealImageName($row['front_image'] ?? null);
        $backImage  = $this->getRealImageName($row['back_image'] ?? null);
        $sideImage  = $this->getRealImageName($row['side_image'] ?? null);

        // Create Product
        $product = Product::create([
            'name'         => $row['name'],
            'description'  => $row['description'] ?? null,
            'product_type' => $row['product_type'] ?? 'b2c',
            'brewing_info' => $row['brewing_info'] ?? null,
            'category_id'  => $categoryIds,
            'front_image'  => $frontImage,
            'back_image'   => $backImage,
            'side_image'   => $sideImage,
        ]);

        // Create Variant (1 variant per row expected)
        if (!empty($row['unit']) && !empty($row['price']) && !empty($row['stock'])) {
            $product->variants()->create([
                'unit'  => $row['unit'],
                'price' => $row['price'],
                'stock' => $row['stock'],
            ]);
        }

        return $product;
    }
}