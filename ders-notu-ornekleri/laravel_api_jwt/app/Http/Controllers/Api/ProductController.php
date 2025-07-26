<?php
// app/Http/Controllers/Api/ProductController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    /**
     * Tüm ürünleri, ilişkili kategorileriyle birlikte listeler.
     */
    public function index()
    {
        // Eager loading ile N+1 problemini önlüyoruz.
        return Product::with('category')->get();
    }

    /**
     * Yeni bir ürün oluşturur ve veritabanına kaydeder.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|integer|exists:categories,id' // Kategori var olmalı
        ]);

        $product = Product::create($request->all());

        return response()->json($product, Response::HTTP_CREATED); // 201
    }

    /**
     * Belirtilen bir ürünü, kategorisiyle birlikte gösterir.
     */
    public function show(Product $product)
    {
        // Route-Model Binding sayesinde bulunan ürünü, kategorisiyle birlikte yüklüyoruz.
        return response()->json($product->load('category'));
    }

    /**
     * Belirtilen bir ürünü günceller.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'category_id' => 'sometimes|required|integer|exists:categories,id'
        ]);

        $product->update($request->all());

        return response()->json($product);
    }

    /**
     * Belirtilen bir ürünü siler.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT); // 204
    }
}
