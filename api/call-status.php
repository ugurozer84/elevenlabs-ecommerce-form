<?php
try {
    if (!isset($_GET['batchId'])) {
        sendJsonResponse(['error' => 'Batch ID gerekli'], 400);
    }
    
    $batchId = $_GET['batchId'];
    
    $response = makeApiCall(
        "https://api.elevenlabs.io/v1/convai/batch-calling/batches/$batchId",
        'GET',
        null,
        [
            'xi-api-key: ' . ELEVENLABS_API_KEY,
            'Content-Type: application/json'
        ]
    );
    
    if ($response['status'] !== 200) {
        throw new Exception('Arama durumu kontrol edilemedi: HTTP ' . $response['status']);
    }
    
    $data = json_decode($response['body'], true);
    sendJsonResponse($data);
    
} catch (Exception $error) {
    writeLog('Durum kontrolü hatası: ' . $error->getMessage());
    sendJsonResponse([
        'error' => 'Arama durumu kontrol edilemedi',
        'details' => $error->getMessage()
    ], 500);
}
?> 