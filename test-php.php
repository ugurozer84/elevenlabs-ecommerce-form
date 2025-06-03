<?php
// Basit PHP test scripti
echo "<!DOCTYPE html>\n";
echo "<html>\n<head>\n<title>PHP Test</title>\n</head>\n<body>\n";
echo "<h1>🐘 PHP Test Sayfası</h1>\n";
echo "<p><strong>PHP Versiyonu:</strong> " . phpversion() . "</p>\n";
echo "<p><strong>Zaman:</strong> " . date('Y-m-d H:i:s') . "</p>\n";

// cURL test et
if (function_exists('curl_init')) {
    echo "<p>✅ <strong>cURL:</strong> Destekleniyor</p>\n";
} else {
    echo "<p>❌ <strong>cURL:</strong> Desteklenmiyor</p>\n";
}

// JSON test et
if (function_exists('json_encode')) {
    echo "<p>✅ <strong>JSON:</strong> Destekleniyor</p>\n";
} else {
    echo "<p>❌ <strong>JSON:</strong> Desteklenmiyor</p>\n";
}

// .env dosyası test et
if (file_exists('.env')) {
    echo "<p>✅ <strong>.env dosyası:</strong> Bulundu</p>\n";
} else {
    echo "<p>❌ <strong>.env dosyası:</strong> Bulunamadı</p>\n";
}

echo "<h2>🔧 Test Linkleri</h2>\n";
echo "<ul>\n";
echo "<li><a href='/health'>/health - Health Check</a></li>\n";
echo "<li><a href='/api/test-connection'>/api/test-connection - API Test</a></li>\n";
echo "<li><a href='/api/phone-numbers'>/api/phone-numbers - Telefon Numaraları</a></li>\n";
echo "<li><a href='/'>/index.html - Ana Sayfa</a></li>\n";
echo "</ul>\n";

echo "</body>\n</html>\n";
?> 