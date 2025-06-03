<?php
try {
    $response = retryApiCall(function() {
        return makeApiCall(
            'https://api.elevenlabs.io/v1/convai/phone-numbers',
            'GET',
            null,
            [
                'xi-api-key: ' . ELEVENLABS_API_KEY,
                'Content-Type: application/json'
            ]
        );
    });
    
    if ($response['status'] !== 200) {
        throw new Exception('Telefon numaraları alınamadı: HTTP ' . $response['status']);
    }
    
    $phoneNumbers = json_decode($response['body'], true);
    
    writeLog('Mevcut telefon numaraları: ' . $response['body']);
    
    sendJsonResponse([
        'phoneNumbers' => $phoneNumbers,
        'count' => $phoneNumbers ? count($phoneNumbers) : 0,
        'message' => $phoneNumbers && count($phoneNumbers) > 0 
            ? 'Telefon numaraları bulundu' 
            : 'Hesabınızda telefon numarası bulunamadı. Lütfen Elevenlabs hesabınızda telefon numarası ekleyin.'
    ]);
    
} catch (Exception $error) {
    writeLog('Telefon numaraları alınamadı: ' . $error->getMessage());
    sendJsonResponse([
        'error' => 'Telefon numaraları alınamadı',
        'details' => $error->getMessage(),
        'suggestion' => 'Agent bilgilerinden telefon numarası görünüyor, bu geçici bir bağlantı sorunu olabilir.'
    ], 500);
}
?> 