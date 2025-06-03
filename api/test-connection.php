<?php
try {
    writeLog('API bağlantısı test ediliyor...');
    
    $response = makeApiCall(
        'https://api.elevenlabs.io/v1/user',
        'GET',
        null,
        [
            'xi-api-key: ' . ELEVENLABS_API_KEY,
            'Accept: application/json',
            'User-Agent: PHP-cURL'
        ]
    );
    
    if ($response['status'] !== 200) {
        throw new Exception('API bağlantı hatası: HTTP ' . $response['status']);
    }
    
    $userData = json_decode($response['body'], true);
    
    writeLog('API bağlantısı başarılı: ' . $response['status']);
    
    sendJsonResponse([
        'success' => true,
        'message' => 'API bağlantısı başarılı',
        'status' => $response['status'],
        'subscription' => $userData['subscription']['tier'] ?? 'unknown'
    ]);
    
} catch (Exception $error) {
    writeLog('API bağlantı hatası: ' . $error->getMessage());
    
    sendJsonResponse([
        'error' => 'API bağlantı hatası',
        'message' => $error->getMessage(),
        'details' => PHP_ENV === 'development' ? $error->getMessage() : null
    ], 500);
}
?> 