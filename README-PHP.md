# Elevenlabs E-ticaret Formu - PHP Versiyonu

Bu proje tamamen PHP ile yazılmış Elevenlabs AI asistan arama sistemidir. Tüm API çağrıları cURL ile yapılmaktadır.

## 🚀 Kurulum

### 1. PHP Kurulumu (macOS)

```bash
# Homebrew ile PHP kur
brew install php

# PHP versiyonunu kontrol et
php --version
```

### 2. Proje Kurulumu

```bash
# Proje dizinine git
cd elevenlabs-mikropor-final-complete

# .env dosyasını kontrol et (API anahtarlarınızı ekleyin)
nano .env
```

### 3. Sunucuyu Başlat

```bash
# PHP built-in sunucu ile başlat
php -S localhost:8080 index.php

# Veya farklı port ile
php -S localhost:3000 index.php
```

## 📁 Dosya Yapısı

```
elevenlabs-mikropor-final-complete/
├── index.php              # Ana router
├── config.php              # Konfigürasyon ve yardımcı fonksiyonlar
├── .htaccess               # Apache URL rewrite kuralları
├── api/                    # API endpoint'leri
│   ├── health.php          # Health check
│   ├── call.php            # Arama başlatma
│   ├── call-status.php     # Arama durumu
│   ├── agent-info.php      # Agent bilgileri
│   ├── phone-numbers.php   # Telefon numaraları
│   └── test-connection.php # API bağlantı testi
├── index.html              # Frontend
├── script.js               # JavaScript
├── style.css               # CSS
├── test-php.php            # PHP test sayfası
└── .env                    # Çevre değişkenleri
```

## 🔧 API Endpoint'leri

- `GET /` - Ana sayfa (index.html)
- `GET /health` - Health check
- `POST /api/call` - Arama başlat
- `GET /api/call-status/{batchId}` - Arama durumu
- `GET /api/agent-info` - Agent bilgileri
- `GET /api/phone-numbers` - Telefon numaraları
- `GET /api/test-connection` - API bağlantı testi

## 🌐 Özellikler

- ✅ **Tamamen cURL tabanlı**: Tüm API çağrıları PHP cURL ile yapılır
- ✅ **Retry mekanizması**: API hatalarında otomatik tekrar deneme
- ✅ **CORS desteği**: Frontend-backend entegrasyonu
- ✅ **Hata yönetimi**: Detaylı hata loglama
- ✅ **Demo modu**: API hatalarında demo yanıtları
- ✅ **Telefon validasyonu**: Türk telefon numarası formatı
- ✅ **Health check**: Sistem durumu kontrolü

## 🔑 Çevre Değişkenleri (.env)

```env
ELEVENLABS_API_KEY=sk_your_api_key_here
ELEVENLABS_AGENT_ID=your_agent_id_here
PORT=8080
PHP_ENV=development
```

## 📞 Kullanım

1. Tarayıcınızda `http://localhost:8080` adresine gidin
2. Türk telefon numarası girin (5XX XXX XX XX formatında)
3. "Hemen Ara" butonuna tıklayın
4. AI asistan sizi arayacak

## 🛠️ Geliştirme

### Debugging
- PHP hataları error_log'a yazılır
- `PHP_ENV=development` ile detaylı hata mesajları
- Browser console'da network isteklerini kontrol edin

### Test
```bash
# Health check
curl http://localhost:8080/health

# API bağlantı testi
curl http://localhost:8080/api/test-connection

# Telefon numaraları
curl http://localhost:8080/api/phone-numbers

# PHP test sayfası
curl http://localhost:8080/test-php.php
```

## 📋 Gereksinimler

- PHP 7.4+
- cURL extension (genellikle varsayılan olarak gelir)
- JSON extension (genellikle varsayılan olarak gelir)
- Web sunucusu (Apache/Nginx) veya PHP built-in sunucu

## 🚀 Production Deployment

### Apache ile
1. Dosyaları web sunucunuza upload edin
2. `.htaccess` dosyası URL rewrite'ları yönetir
3. `.env` dosyasında production API anahtarlarını ayarlayın
4. `PHP_ENV=production` olarak ayarlayın

### Nginx ile
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
}
```

### cPanel Hosting
1. Dosyaları public_html klasörüne upload edin
2. `.htaccess` otomatik çalışacak
3. `.env` dosyasını web root dışında tutun
4. File Manager'da dosya izinlerini kontrol edin

## 📝 Notlar

- ✅ **Pure PHP**: Harici bağımlılık yok
- ✅ **cURL tabanlı**: Güvenilir HTTP istekleri
- ✅ **Hosting uyumlu**: Çoğu shared hosting'de çalışır
- ✅ **Hızlı deployment**: Sadece dosyaları upload edin
- ✅ **Kolay bakım**: Basit PHP kodu, kolay anlaşılır 