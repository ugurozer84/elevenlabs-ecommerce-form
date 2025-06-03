# Elevenlabs E-ticaret AI Asistan Formu

Bu proje, e-ticaret sitelerinde müşterilerin telefon numaralarını girerek Elevenlabs yapay zeka asistanının kendilerini aramasını sağlayan bir form uygulamasıdır.

## Özellikler

- 🎯 Modern ve kullanıcı dostu arayüz
- 📱 Türk telefon numarası validasyonu
- 🤖 Elevenlabs Conversational AI entegrasyonu
- 🔄 Gerçek zamanlı durum güncellemeleri
- 📱 Responsive tasarım
- ⚡ Hızlı ve güvenli arama başlatma

## Kurulum

### 1. Projeyi İndirin
```bash
git clone <repository-url>
cd elevenlabs-ecommerce-form
```

### 2. Bağımlılıkları Yükleyin
```bash
npm install
```

### 3. Çevre Değişkenlerini Ayarlayın
`.env` dosyasını düzenleyin ve kendi Elevenlabs bilgilerinizi girin:

```env
ELEVENLABS_API_KEY=your_actual_api_key_here
ELEVENLABS_AGENT_ID=your_actual_agent_id_here
PORT=3000
NODE_ENV=development
```

### 4. Uygulamayı Başlatın
```bash
npm start
```

Uygulama `http://localhost:3000` adresinde çalışacaktır.

## Geliştirme Modu

Geliştirme sırasında otomatik yeniden başlatma için:

```bash
npm run dev
```

## API Endpoints

### POST /api/call
Yeni bir arama başlatır.

**Request Body:**
```json
{
  "phoneNumber": "+905XXXXXXXXX"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Arama başarıyla başlatıldı",
  "conversationId": "conv_xxx",
  "phoneNumber": "+905XXXXXXXXX"
}
```

### GET /api/call-status/:conversationId
Arama durumunu kontrol eder.

### GET /health
Sunucu durumunu kontrol eder.

## Elevenlabs Kurulumu

1. [Elevenlabs](https://elevenlabs.io) hesabınızda oturum açın
2. Conversational AI bölümünden yeni bir agent oluşturun
3. Agent'ınızı Twilio ile entegre edin
4. API anahtarınızı ve Agent ID'nizi alın
5. Bu bilgileri `.env` dosyasına ekleyin

## Dosya Yapısı

```
elevenlabs-ecommerce-form/
├── index.html          # Ana HTML dosyası
├── style.css           # CSS stilleri
├── script.js           # Frontend JavaScript
├── server.js           # Backend Express server
├── package.json        # NPM bağımlılıkları
├── .env               # Çevre değişkenleri
└── README.md          # Bu dosya
```

## Teknolojiler

- **Frontend:** HTML5, CSS3, Vanilla JavaScript
- **Backend:** Node.js, Express.js
- **API:** Elevenlabs Conversational AI
- **Telefon:** Twilio entegrasyonu

## Güvenlik

- Telefon numarası validasyonu
- API anahtarları çevre değişkenlerinde
- CORS koruması
- Hata yönetimi

## Lisans

MIT License

## Destek

Herhangi bir sorun yaşarsanız, lütfen issue açın veya iletişime geçin.
