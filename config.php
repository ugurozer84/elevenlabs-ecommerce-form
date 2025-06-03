<?php
// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CORS ayarları
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// .env dosyasını oku
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
    
    return true;
}

// .env dosyasını yükle
loadEnv('.env');

// Konfigürasyon değişkenleri
define('ELEVENLABS_API_KEY', getenv('ELEVENLABS_API_KEY') ?: 'your_api_key_here');
define('ELEVENLABS_AGENT_ID', getenv('ELEVENLABS_AGENT_ID') ?: 'your_agent_id_here');
define('PHP_ENV', getenv('PHP_ENV') ?: getenv('NODE_ENV') ?: 'development');
define('TARGET_PHONE_NUMBER_ID', 'PN0c9541d6512d3eeec754cc858c9df897');

// Retry fonksiyonu
function retryApiCall($apiCall, $maxRetries = 3, $delay = 1000) {
    for ($i = 0; $i < $maxRetries; $i++) {
        try {
            return $apiCall();
        } catch (Exception $error) {
            error_log("API çağrısı " . ($i + 1) . ". deneme başarısız: " . $error->getMessage());
            
            if ($i === $maxRetries - 1) {
                throw $error; // Son deneme, hatayı fırlat
            }
            
            // Exponential backoff
            usleep($delay * pow(2, $i) * 1000); // usleep mikrosaniye cinsinden
        }
    }
}

// cURL ile API çağrısı yapma fonksiyonu
function makeApiCall($url, $method = 'GET', $data = null, $headers = []) {
    $ch = curl_init();
    
    // Temel cURL ayarları
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
    curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-cURL/8.0');
    
    // HTTP method ayarları
    switch (strtoupper($method)) {
        case 'POST':
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
            break;
        case 'PUT':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
            break;
        case 'DELETE':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            break;
    }
    
    // Headers ayarları
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    if ($error) {
        throw new Exception("cURL Hatası: " . $error);
    }
    
    return [
        'body' => $response,
        'status' => $httpCode
    ];
}

// JSON yanıt gönderme fonksiyonu
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Telefon numarası validasyonu
function validatePhoneNumber($phoneNumber) {
    return preg_match('/^\+90[0-9]{10}$/', $phoneNumber);
}

// Log yazma fonksiyonu
function writeLog($message) {
    $timestamp = date('Y-m-d H:i:s');
    error_log("[$timestamp] $message");
}
?> 