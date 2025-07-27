<?php
// app/Models/Category.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Category modeli, veritabanındaki "categories" tablosunu temsil eder
class Category extends Model
{
    // Laravel'in model fabrika desteğini kullanmak için HasFactory trait'i dahil edilir
    // Böylece, sahte veri üretimi için fabrika kullanımına olanak sağlar
    use HasFactory;

    // Laravel varsayılan olarak tablo adını "categories" varsayar
    // Ama gerçek tablo adın farklıysa bunu açıkça belirtmelisin. Örnek:
    // protected $table = 'kategoriler';

    // Mass assignment (toplu atama) ile doldurulabilir alanları tanımlar
    // Bu örnekte sadece "name" alanı dışarıdan toplu olarak atanabilir
    protected $fillable = ['name', 'description'];

    // Category modelinin Product modeliyle olan ilişkisini tanımlar
    // Bir kategori, birden çok ürüne sahip olabilir (one-to-many ilişki)
    public function products(): HasMany
    {
        // Category -> hasMany -> Product
        // Yani: bir kategoriye ait birçok ürün olabilir
        return $this->hasMany(Product::class);
    }
}
