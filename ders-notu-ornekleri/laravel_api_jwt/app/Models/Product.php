<?php
// app/Models/Product.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'stock', 'category_id'];

    // Product modelinin Category modeliyle olan ilişkisini tanımlar
    // Bir ürün yalnızca bir kategoriye aittir (many-to-one ilişki)
    public function category(): BelongsTo
    {
        // Product -> belongsTo -> Category
        // Yani: bu ürün, bir kategoriye aittir
        return $this->belongsTo(Category::class);
    }
}
