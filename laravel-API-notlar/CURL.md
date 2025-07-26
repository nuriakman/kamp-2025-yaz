# cURL ile API Test Talimatları

## Temel cURL Kullanımı

## cURL, komut satırı üzerinden HTTP istekleri göndermek için kullanılan güçlü bir araçtır. Aşağıda, API'nizi test etmek için kullanabileceğiniz temel cURL komutlarını bulabilirsiniz.

## cURL Çıktısının Formatlanması

cURL komutları ile alınan çıktının JSON formatının düzgün gösterimi için "jq" komutu kullanılır.

```bash
# jq paketinin yüklenmesi
sudo apt install -y jq
```

**Kullanım**
curl komutu sonuna pipe ile jq komutu kullanılarak `|jq` JSON formatının düzgün gösterilmesi sağlanır.

```bash
curl <parametreler> | jq
```

**Örnek Çıktı (jq kullanmadan)**

```text
$ curl -X GET http://localhost:8000/api/categories/1

{"id":1,"name":"Elektronik","created_at":"2025-07-26T08:49:12.000000Z","updated_at":"2025-07-26T08:49:12.000000Z"}
```

**Örnek Çıktı (jq kullanarak)**

```json
$ curl -X GET http://localhost:8000/api/categories/1|jq
  % Total    % Received % Xferd  Average Speed   Time    Time     Time  Current
                                 Dload  Upload   Total   Spent    Left  Speed
100   114    0   114    0     0   5339      0 --:--:-- --:--:-- --:--:--  5428
{
  "id": 1,
  "name": "Elektronik",
  "created_at": "2025-07-26T08:49:12.000000Z",
  "updated_at": "2025-07-26T08:49:12.000000Z"
}

```

## KATEGORİLER

### A.1. Yeni Kategori Oluşturma (POST)

```bash
curl -X POST http://localhost:8000/api/categories \
  -H "Content-Type: application/json" \
  -d '{"name": "Elektronik"}' | jq
```

### A.2. Tüm Kategorileri Listeleme (GET)

```bash
curl -X GET http://localhost:8000/api/categories
```

### A.3. Belirli Bir Kategoriyi Görüntüleme (GET)

```bash
curl -X GET http://localhost:8000/api/categories/1
```

### A.4. Kategori Güncelleme (PUT/PATCH)

```bash
curl -X PUT http://localhost:8000/api/categories/1 \
  -H "Content-Type: application/json" \
  -d '{"name": "Elektronik Eşyalar"}'
```

### A.5. Kategori Silme (DELETE)

```bash
curl -X DELETE http://localhost:8000/api/categories/1
```

---

## ÜRÜNLER

### B.1. Yeni Ürün Oluşturma (POST)

```bash
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -d '{"name": "Akıllı Telefon", "description": "Son model, güçlü işlemcili", "price": 25000.00, "category_id": 1}'
```

### B.2. Tüm Ürünleri Listeleme (GET)

```bash
curl -X GET http://localhost:8000/api/products
```

### B.3. Belirli Bir Ürünü Görüntüleme (GET)

```bash
curl -X GET http://localhost:8000/api/products/1
```

### B.4. Ürün Güncelleme (PUT/PATCH)

```bash
curl -X PUT http://localhost:8000/api/products/1 \
  -H "Content-Type: application/json" \
  -d '{"name": "Yeni Model Akıllı Telefon", "price": 27000.00}'
```

### B.5. Ürün Silme (DELETE)

```bash
curl -X DELETE http://localhost:8000/api/products/1
```

---

## cURL Parametreleri Açıklaması:

- `-X`: HTTP metodunu belirtir (GET, POST, PUT, DELETE vb.)
- `-H`: İstek başlıklarını belirtir
- `-d`: Gönderilecek veriyi (request body) belirtir
- `-i`: Yanıt başlıklarını da gösterir
- `-v`: Detaylı çıktı verir (verbose)
- `-o`: Çıktıyı dosyaya yazdırır

## Örnek Detaylı cURL Kullanımı:

```bash
curl -v -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name": "Örnek Ürün", "price": 99.99, "category_id": 1}' \
  -o response.json
```

Bu komut:

1. Detaylı çıktı verir (`-v`)
2. POST isteği yapar (`-X POST`)
3. İçerik tipini JSON olarak ayarlar
4. JSON verisini gönderir
5. Yanıtı `response.json` dosyasına kaydeder
