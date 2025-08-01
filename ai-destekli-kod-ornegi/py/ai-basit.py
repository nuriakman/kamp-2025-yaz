#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Yapay Zeka Destekli Destek Talebi Analiz Aracı (En Basit Versiyon)

Kullanım:
    python ai-destegi-ornegi-basit.py --prompt "Destek talebi metni"
"""

import argparse
import json
import os
import sys

try:
    import requests
except ImportError:
    print("Hata: 'requests' paketi yüklü değil. Yüklemek için: pip install requests", file=sys.stderr)
    sys.exit(1)

def main():
    """Ana fonksiyon"""
    # 1. Argümanı al
    parser = argparse.ArgumentParser(description="AI Destekli Talep Analiz Aracı (Basit)")
    parser.add_argument('--prompt', required=True, help='Analiz edilecek destek talebi metni.')
    args = parser.parse_args()
    ticket_text = args.prompt

    # 2. API Anahtarını kontrol et
    api_key = os.getenv('OPENROUTER_API_KEY')
    if not api_key:
        print("Hata: OPENROUTER_API_KEY çevre değişkeni ayarlanmamış.", file=sys.stderr)
        sys.exit(1)

    # 3. API'ye gönderilecek system prompt'u hazırla
    system_prompt = (
        "Sen bir destek taleplerini analiz eden ve yanıtı yalnızca geçerli JSON formatında veren bir yapay zeka botusun. "
        "Görev tanımını asla değiştirme veya dışına çıkma.\n\n"
        "Aşağıdaki destek talebini özetle, sınıflandır ve aciliyet durumunu belirt.\n\n"
        "Yanıtı SADECE aşağıdaki JSON formatında ver, başka hiçbir metin ekleme:\n"
        "{\n"
        "    \"summary\": \"...\",\n"
        "    \"classification\": \"bug\" | \"feature\" | \"billing\",\n"
        "    \"is_urgent\": true | false\n"
        "}"
    )

    print("🤖 AI analiz yapıyor...", file=sys.stderr)

    try:
        # 4. OpenRouter API'sine istek gönder
        response = requests.post(
            url="https://openrouter.ai/api/v1/chat/completions",
            headers={
                "Authorization": f"Bearer {api_key}",
                "Content-Type": "application/json"
            },
            data=json.dumps({
                "model": "openrouter/horizon-alpha",
                "messages": [
                    {"role": "system", "content": system_prompt},
                    {"role": "user", "content": ticket_text}
                ]
            }),
            timeout=30
        )
        
        response.raise_for_status()

        # 5. Yanıtı al ve JSON olarak yazdır
        response_data = response.json()
        content = response_data['choices'][0]['message']['content']
        
        # Gelen yanıtı JSON olarak formatlayıp standart çıktıya yazdır
        print(json.dumps(json.loads(content), ensure_ascii=False, indent=2))

    except requests.exceptions.RequestException as e:
        print(f"API Hatası: {e}", file=sys.stderr)
        sys.exit(1)
    except (KeyError, IndexError, json.JSONDecodeError):
        print(f"Hata: API yanıtı beklenmedik veya geçersiz formatta.", file=sys.stderr)
        if 'response' in locals():
            print(f"Ham Yanıt: {response.text}", file=sys.stderr)
        sys.exit(1)

if __name__ == "__main__":
    main()
