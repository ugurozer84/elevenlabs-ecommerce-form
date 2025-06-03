<?php
try {
    $response = makeApiCall(
        'https://api.elevenlabs.io/v1/convai/agents/' . ELEVENLABS_AGENT_ID,
        'GET',
        null,
        [
            'xi-api-key: ' . ELEVENLABS_API_KEY,
            'Content-Type: application/json'
        ]
    );
    
    if ($response['status'] !== 200) {
        throw new Exception('Agent bilgileri alınamadı: HTTP ' . $response['status']);
    }
    
    $data = json_decode($response['body'], true);
    
    writeLog('Agent bilgileri: ' . $response['body']);
    sendJsonResponse($data);
    
} catch (Exception $error) {
    writeLog('Agent bilgileri alınamadı: ' . $error->getMessage());
    sendJsonResponse([
        'error' => 'Agent bilgileri alınamadı',
        'details' => $error->getMessage()
    ], 500);
}
?> 