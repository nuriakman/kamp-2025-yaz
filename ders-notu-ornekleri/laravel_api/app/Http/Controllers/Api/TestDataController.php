<?php
// app/Http/Controllers/TestDataController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class TestDataController extends Controller
{
  public function createTestData(Request $request)
  {
    $data = $request->all();

    foreach ($data['categories'] as $categoryData) {
      $category = Category::create([
        'name' => $categoryData['name']
      ]);

      foreach ($categoryData['products'] as $productData) {
        $productData['category_id'] = $category->id;
        Product::create($productData);
      }
    }

    return response()->json([
      'message' => 'Test verileri başarıyla oluşturuldu',
      'total_categories_count' => Category::count(),
      'total_products_count' => Product::count()
    ], 201);
  }
}
