# Basit AI Entegrasyonu

## Örnek Kullanım:

```bash
$ py ai-basit.py --prompt "ACİL DURUM: YANLIŞ FATURA KESİMİ. Geçen ay otomatik ödeme ile abone olduğum Premium paketin faturasını yanlış kesmişsiniz. Abone olduğum paket: Aylık 49.99 TL, Kesilen tutar: 499.90 TL. Bu hatayı fark ettiğimde hemen müşteri hizmetlerini aradım ancak 45 dakika beklememe rağmen kimseyle görüşemedim. E-posta attım, 3 iş günü geçmesine rağmen yanıt alamadım. Lütfen en kısa sürede fazla çekilen tutarı iade edin ve aboneliğimi iptal edin."
🤖 AI analiz yapıyor...
```

```json
{
  "summary": "Müşteri, Premium paket için 49.99 TL yerine 499.90 TL faturalandırıldığını belirtiyor; müşteri hizmetlerine ulaşamamış ve e-postasına 3 iş günüdür yanıt alamamış. Fazla çekilen tutarın iadesini ve aboneliğin iptalini talep ediyor.",
  "classification": "billing",
  "is_urgent": true
}
```
