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
        return Category::all();
    }

    /**
     * Yeni bir kategori oluşturur ve veritabanına kaydeder.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:categories|max:255',
        ]);

        $category = Category::create($request->all());

        return response()->json($category, Response::HTTP_CREATED); // 201 Created
    }

    /**
     * Belirtilen bir kategoriyi gösterir.
     */
    public function show(Category $category)
    {
        // Laravel'in Route-Model Binding özelliği sayesinde, URL'deki {category}
        // ID'sine sahip olan Category modeli otomatik olarak bulunur ve enjekte edilir.
        return response()->json($category);
    }

    /**
     * Belirtilen bir kategoriyi günceller.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            // Kategori adının benzersiz olmasını kontrol ederken, mevcut kategorinin kendisini hariç tutarız.
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update($request->all());

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
