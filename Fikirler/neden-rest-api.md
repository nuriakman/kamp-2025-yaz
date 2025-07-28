# Neden REST API?

## Giriş

REST API, yazılım dünyasında **veriye erişim ve işlem yapma süreçlerini standartlaştırmak** için geliştirilmiş bir yaklaşımdır. CRUD işlemleriyle birebir örtüşen HTTP metotları (GET, POST, PUT, DELETE) sayesinde, farklı ekipler, diller veya sistemler arasında **anlam birliği** sağlar.

Eğer REST gibi bir standart olmasaydı, herkes veri alma için farklı yollar (örneğin `/veri/getir`, `/veri/oku`, `/veri/listele`) uydurabilir ve sistemler arası uyum sağlanamazdı. REST sayesinde, `GET /kullanicilar` ifadesi her zaman kullanıcı listesini çeker; bu netlik, **anlaşılabilirlik**, **tekrar kullanılabilirlik** ve **bakım kolaylığı** sağlar.

Yani REST API, sadece bir teknik tercih değil, **iletişimsel ve yapısal bir uzlaşıdır.**

---

## Neden REST API?

### CRUD ile Doğrudan Uyumlu

- REST API’nin temel HTTP metodları (GET, POST, PUT/PATCH, DELETE), CRUD işlemleriyle birebir örtüşür:

  - `GET` → Read (Oku)
  - `POST` → Create (Oluştur)
  - `PUT/PATCH` → Update (Güncelle)
  - `DELETE` → Delete (Sil)

- Bu eşleşme sayesinde hem geliştiriciler hem de sistemler **ne yapılmak istendiğini kolayca anlar**.

---

### Evrensel Anlaşılabilirlik

- REST API, platformdan bağımsızdır. Mobil, web, IoT veya masaüstü sistemleri, ortak bir yapı üzerinden iletişim kurabilir.
- `GET /products` ya da `DELETE /user/15` gibi endpointler, sade ve evrensel bir dil sunar.
- Bu yapı, farklı ekipler (frontend/backend/devops) arasında **söz birliği** sağlar.

---

### Tekrar Kullanılabilirlik (Reusability)

- Bir kez yazılan bir REST servisi, farklı uygulamalar (web, mobil, 3. taraf) tarafından yeniden kullanılabilir.
- Örneğin: `GET /orders` endpoint’i hem müşteri panelinde hem yönetici panelinde aynı şekilde işlev görür.

---

### Bakım Kolaylığı ve Genişletilebilirlik

- Standart URL yapısı ve işlem tipleri sayesinde yeni özellikler kolayca eklenebilir.
- Ayrıca, REST API'ler genellikle sürümlenebilir (`/api/v1/...`) olduğundan **geriye dönük uyumluluk** korunabilir.

---

### Dökümantasyon ve Otomasyon Kolaylığı

- Swagger (OpenAPI), Postman, Insomnia gibi araçlarla REST API’ler kolayca dokümante ve test edilebilir.
- Bu sayede projeye yeni katılan bir geliştirici, API'nin nasıl kullanılacağını hemen anlayabilir.

---

### Örnek REST API Endpoint Yapısı:

| İşlem               | HTTP Metodu | Endpoint   | Açıklama                        |
| ------------------- | ----------- | ---------- | ------------------------------- |
| Kullanıcı ekle      | POST        | `/users`   | Yeni kullanıcı oluşturur        |
| Kullanıcıları getir | GET         | `/users`   | Tüm kullanıcıları listeler      |
| Kullanıcı güncelle  | PUT         | `/users/7` | ID:7 olan kullanıcıyı günceller |
| Kullanıcı sil       | DELETE      | `/users/7` | ID:7 olan kullanıcıyı siler     |

---

### Sonuç:

> REST API, sadece bir veri alışveriş modeli değil; **anlaşılabilirlik, düzen ve sürdürülebilirlik** için kabul edilmiş **ortak bir yazılım dili**dir.
> Eğer REST gibi bir standart olmasaydı, her ekip kendi kavramlarını kullanır, sistemler arası entegrasyon bir kâbusa dönüşürdü.

---

## Neden standarda ihtiyaç duyuyoruz?

Yazılım geliştirme dünyasında **standartlaşma**, iletişimin ve sürdürülebilirliğin temel taşıdır. CRUD kavramı (Create, Read, Update, Delete) bu bağlamda evrensel bir dil gibidir. Eğer bu tür bir kavramsal standart olmasaydı, her geliştirici "veri ekleme", "veri görüntüleme", "veri değiştirme" ve "veri silme" gibi temel işlemleri kendi anlayışına göre isimlendirecekti. Kimisi "ekle", kimisi "yarat", bir diğeri "tanımla" ya da "varlık oluştur" diyebilirdi. Bu da hem ekip içi hem ekipler arası iletişimi zorlaştırır; bakım, dokümantasyon ve entegrasyon süreçlerini karmaşık hale getirirdi.

Hazırladığımız tabloda görüldüğü gibi, tek bir CRUD terimi için anlamca yakın **20'den fazla Türkçe kelime** kullanılabilir. Bu çeşitlilik, zengin bir dilin avantajı olduğu kadar, yazılım terminolojisinde bir **standart eksikliği durumunda doğabilecek kaosun da göstergesidir**. Örneğin bir geliştirici "sil" derken, diğeri "temizle", bir başkası "iptal et" veya "yok say" diyebilir. Bu farklılık, sistemler arasında veri işleme mantıklarının anlaşılmasını ve yorumlanmasını ciddi anlamda zorlaştırır.

Bu nedenle, CRUD gibi terimlerin sadece teknik değil, aynı zamanda **iletişimsel standartlar** olarak da değeri büyüktür. Yazılım ekipleri, farklı dillerde ve kültürlerde çalışıyor olsalar bile, "CRUD işlemleri" dendiğinde herkes aynı temel işlevleri kasteder. Böylece ortak bir çerçevede düşünmek, karar almak ve yazmak mümkün hale gelir. Standartlar olmazsa, her sistem kendi kelime dünyasında yaşar ve bu dünya başkaları için okunması güç, anlaşılması zor bir hale gelir.

### CRUD için neler kullanılabilirdi?

| **Create** (Oluştur) | **Read** (Oku) | **Update** (Güncelle) | **Delete** (Sil) |
| -------------------- | -------------- | --------------------- | ---------------- |
| Ekle                 | Listele        | Güncelle              | Sil              |
| Oluştur              | Oku            | Değiştir              | Kaldır           |
| Yarat                | Göster         | Düzenle               | Temizle          |
| Kaydet               | Getir          | Yenile                | Yok et           |
| Başlat               | Sorgula        | Düzelt                | İptal et         |
| Kur                  | İncele         | Biçimle               | Geri al          |
| Tanımla              | Görüntüle      | Ayarla                | Pasifleştir      |
| Aç                   | Al             | Üzerine yaz           | Arşivle          |
| Hazırla              | Seç            | Tazele                | Ayıkla           |
| Sun                  | Ara            | Güncel tut            | Devre dışı bırak |
| Türet                | Filtrele       | Tamamla               | Dışla            |
| Belirle              | İzle           | Ek yap                | Yok say          |
| Tayin et             | Tarat          | Yeniden tanımla       | Sök              |
| Diz                  | Denetle        | Sürümle               | Engelle          |
| Yerleştir            | Göz at         | Geliştir              | Görmezden gel    |
| Üret                 | Tahlil et      | Tümünü değiştir       | Boşalt           |
| Aktar                | Ayıkla         | Revize et             | Silinsin         |
| Başvuru al           | Tespit et      | Yeni sürüm yap        | Engelle          |
| Varlık ekle          | Kayıt çek      | Güncel bilgi gir      | Sistemden kaldır |
| Kayıt oluştur        | Bilgi al       | Bilgileri yenile      | Tamamen sil      |

### Kod Okunabilirliğinin Bozulması

Varsayalım ki bir yazılımcı veri silme işlemi için `temizleVeri()` adını kullandı. Başka biri aynı projeye sonradan katıldığında, bu fonksiyonun gerçekten "veriyi sildiğini" mi, yoksa "verinin içini boşaltıp bıraktığını" mı yaptığına emin olamaz. Oysa `deleteData()` veya `destroy()` gibi isimler bir sektörel standarda dayanır ve çok daha net anlam taşır.

### API Uç Noktalarında Karışıklık

Aşağıda iki farklı API geliştiricisinin aynı işlev için nasıl farklı adlandırmalar kullandığını düşünün:

- Geliştirici A: `POST /veri/uret` → veri oluştur
- Geliştirici B: `POST /veri/kaydet` → veri oluştur
- Geliştirici C: `POST /veri/olustur` → veri oluştur

Hepsi aynı şeyi yapıyor olabilir, ama entegrasyon yapacak üçüncü bir taraf hangi uç noktanın ne işe yaradığını ancak dökümana bakarak veya deneme-yanılmayla anlayabilir. Eğer tümü `POST /veri` standardını kullansaydı, karmaşa olmazdı.

### Ekipler Arası Uyuşmazlık

Farklı ekiplerin aynı uygulama için farklı isimler kullanması uyumsuzluklara neden olabilir. Bir ekip "tanımla", diğer ekip "oluştur", başka bir ekip "kayıt al" gibi işlemleri tanımlıyorsa, ortak bir arayüz dili geliştirmek neredeyse imkânsız hâle gelir. Bu durum büyük projelerde zaman kaybına, hata riskine ve kullanıcı arayüzlerinde tutarsızlığa yol açar.

### Yeni Başlayanlar İçin Öğrenme Güçlüğü

Yeni yazılımcılar veya stajyerler için ortak terimler büyük kolaylık sağlar. CRUD gibi evrensel kavramlar öğrenme sürecini hızlandırır. Ancak her projede farklı kelimeler kullanılıyorsa, yeni başlayanların kavramları anlaması ve sisteme adapte olması daha uzun zaman alır.

### Sonuç:

Standartların olmadığı bir dünyada, her geliştirici kendi lügatini kullanır; bu da yazılım dünyasında **dilsel kuleler** inşa eder. CRUD gibi kavramlar, sadece teknik değil, aynı zamanda **anlaşılabilirlik ve sürdürülebilirlik standartlarıdır**. Bu nedenle, yazılım geliştirmede kavramsal birlik şarttır.
