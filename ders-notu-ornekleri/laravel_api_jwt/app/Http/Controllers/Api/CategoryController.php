<?php
// app/Http/Controllers/Api/CategoryController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response; // Response sabitleri için ekliyoruz

class CategoryController extends Controller
{
    /**
     * Tüm kategorileri listeler.
     */
    public function index()
    {
        $categories = Category::with('products')->get();
        return response()->json($categories);
    }

    /**
     * Yeni bir kategori oluşturur ve veritabanına kaydeder.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($validated);

        return response()->json($category, Response::HTTP_CREATED);
    }

    /**
     * Belirtilen bir kategoriyi gösterir.
     */
    public function show(Category $category)
    {
        $category->load('products');
        return response()->json($category);
    }

    /**
     * Belirtilen bir kategoriyi günceller.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return response()->json($category);
    }

    /**
     * Belirtilen bir kategoriyi siler.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        // Başarılı silme işleminden sonra içerik döndürmeye gerek yoktur.
        return response()->json(null, Response::HTTP_NO_CONTENT); // 204 No Content
    }
}
