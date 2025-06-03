<?php
require_once 'config.php';

// OPTIONS isteklerini işle (CORS için)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// URL routing
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Query string'i temizle
$path = parse_url($requestUri, PHP_URL_PATH);

// Routing tablosu
switch (true) {
    // Ana sayfa
    case $path === '/' || $path === '/index.php':
        if ($requestMethod === 'GET') {
            // index.html dosyasını serve et
            if (file_exists('index.html')) {
                readfile('index.html');
            } else {
                sendJsonResponse(['error' => 'index.html dosyası bulunamadı'], 404);
            }
        }
        break;
    
    // Health check
    case $path === '/health':
        if ($requestMethod === 'GET') {
            include 'api/health.php';
        }
        break;
    
    // Arama API'si
    case $path === '/api/call':
        if ($requestMethod === 'POST') {
            include 'api/call.php';
        } else {
            sendJsonResponse(['error' => 'Method not allowed'], 405);
        }
        break;
    
    // Arama durumu kontrolü
    case preg_match('#^/api/call-status/(.+)$#', $path, $matches):
        if ($requestMethod === 'GET') {
            $_GET['batchId'] = $matches[1];
            include 'api/call-status.php';
        } else {
            sendJsonResponse(['error' => 'Method not allowed'], 405);
        }
        break;
    
    // Agent bilgileri
    case $path === '/api/agent-info':
        if ($requestMethod === 'GET') {
            include 'api/agent-info.php';
        } else {
            sendJsonResponse(['error' => 'Method not allowed'], 405);
        }
        break;
    
    // Telefon numaraları
    case $path === '/api/phone-numbers':
        if ($requestMethod === 'GET') {
            include 'api/phone-numbers.php';
        } else {
            sendJsonResponse(['error' => 'Method not allowed'], 405);
        }
        break;
    
    // Bağlantı testi
    case $path === '/api/test-connection':
        if ($requestMethod === 'GET') {
            include 'api/test-connection.php';
        } else {
            sendJsonResponse(['error' => 'Method not allowed'], 405);
        }
        break;
    
    // Static dosyalar (CSS, JS, HTML)
    case preg_match('#\.(css|js|html|png|jpg|jpeg|gif|ico)$#', $path):
        $filePath = ltrim($path, '/');
        if (file_exists($filePath)) {
            $mimeTypes = [
                'css' => 'text/css',
                'js' => 'application/javascript',
                'html' => 'text/html',
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif' => 'image/gif',
                'ico' => 'image/x-icon'
            ];
            
            $ext = pathinfo($filePath, PATHINFO_EXTENSION);
            $mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';
            
            header('Content-Type: ' . $mimeType);
            readfile($filePath);
        } else {
            http_response_code(404);
            echo '404 - Dosya bulunamadı';
        }
        break;
    
    // 404 - Sayfa bulunamadı
    default:
        sendJsonResponse(['error' => 'Endpoint bulunamadı'], 404);
        break;
}
?> 