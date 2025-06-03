<?php
try {
    // POST verilerini al
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['phoneNumber'])) {
        sendJsonResponse(['error' => 'Telefon numarası gerekli'], 400);
    }
    
    $phoneNumber = $input['phoneNumber'];
    
    // Telefon numarası validasyonu
    if (!validatePhoneNumber($phoneNumber)) {
        sendJsonResponse([
            'error' => 'Geçersiz telefon numarası formatı. +905XXXXXXXXX formatında olmalıdır.'
        ], 400);
    }
    
    writeLog("Arama isteği alındı: $phoneNumber");
    
    try {
        // Önce Agent bilgilerini al ve telefon numarasını oradan çek
        $agentResponse = retryApiCall(function() {
            return makeApiCall(
                'https://api.elevenlabs.io/v1/convai/agents/' . ELEVENLABS_AGENT_ID,
                'GET',
                null,
                [
                    'xi-api-key: ' . ELEVENLABS_API_KEY,
                    'Content-Type: application/json'
                ]
            );
        });
        
        if ($agentResponse['status'] !== 200) {
            throw new Exception('Agent bilgileri alınamadı: HTTP ' . $agentResponse['status']);
        }
        
        $agentData = json_decode($agentResponse['body'], true);
        
        writeLog('Agent yanıtı: ' . $agentResponse['body']);
        
        // Agent'dan telefon numaralarını al
        if (!isset($agentData['phone_numbers']) || empty($agentData['phone_numbers'])) {
            sendJsonResponse([
                'success' => false,
                'message' => 'Demo Modu: Telefon numarası henüz entegre edilmemiş',
                'phoneNumber' => $phoneNumber,
                'demoMode' => true,
                'instructions' => [
                    '🔧 Gerçek arama için şu adımları takip edin:',
                    '1. Elevenlabs hesabınıza gidin: https://elevenlabs.io',
                    '2. Conversational AI > Phone Numbers bölümüne gidin',
                    '3. "Add Phone Number" butonuna tıklayın',
                    '4. Twilio seçeneğini seçin',
                    '5. Telefon numaranız: +18609435227',
                    '6. Twilio Account SID ve Auth Token bilgilerinizi girin',
                    '7. Bu sayfayı yenileyin ve tekrar deneyin'
                ]
            ]);
        }
        
        // İlk telefon numarasını kullan veya belirli ID'yi ara
        $selectedPhoneNumber = $agentData['phone_numbers'][0];
        
        // Eğer belirli bir telefon numarası ID'si varsa onu kullan
        foreach ($agentData['phone_numbers'] as $phone) {
            if (isset($phone['phone_number_id']) && $phone['phone_number_id'] === TARGET_PHONE_NUMBER_ID) {
                $selectedPhoneNumber = $phone;
                writeLog('Hedef telefon numarası bulundu: ' . json_encode($selectedPhoneNumber));
                break;
            }
        }
        
        if ($selectedPhoneNumber === $agentData['phone_numbers'][0]) {
            writeLog('Hedef telefon numarası bulunamadı, ilk numarayı kullanıyorum: ' . json_encode($selectedPhoneNumber));
        }
        
        // Elevenlabs Batch Calling API çağrısı - retry ile
        $elevenlabsResponse = retryApiCall(function() use ($phoneNumber, $selectedPhoneNumber) {
            $payload = json_encode([
                'call_name' => 'E-ticaret AI Asistan Araması',
                'agent_id' => ELEVENLABS_AGENT_ID,
                'agent_phone_number_id' => $selectedPhoneNumber['phone_number_id'],
                'recipients' => [
                    [
                        'phone_number' => $phoneNumber
                    ]
                ],
                'scheduled_time_unix' => time() + 5 // 5 saniye sonra ara
            ]);
            
            return makeApiCall(
                'https://api.elevenlabs.io/v1/convai/batch-calling/submit',
                'POST',
                $payload,
                [
                    'xi-api-key: ' . ELEVENLABS_API_KEY,
                    'Content-Type: application/json'
                ]
            );
        });
        
        if ($elevenlabsResponse['status'] !== 200) {
            throw new Exception('Arama başlatılamadı: HTTP ' . $elevenlabsResponse['status']);
        }
        
        $elevenlabsData = json_decode($elevenlabsResponse['body'], true);
        
        writeLog('Elevenlabs yanıtı: ' . $elevenlabsResponse['body']);
        
        sendJsonResponse([
            'success' => true,
            'message' => 'Arama başarıyla başlatıldı! AI asistanımız sizi 5 saniye içinde arayacak.',
            'batchId' => $elevenlabsData['id'],
            'phoneNumber' => $phoneNumber,
            'scheduledTime' => $elevenlabsData['scheduled_time_unix'],
            'callerNumber' => $selectedPhoneNumber['phone_number'],
            'phoneNumberId' => $selectedPhoneNumber['phone_number_id']
        ]);
        
    } catch (Exception $apiError) {
        writeLog('API Hatası: ' . $apiError->getMessage());
        
        // API hatası durumunda demo modu
        sendJsonResponse([
            'success' => false,
            'message' => 'Demo Modu: API bağlantı sorunu',
            'phoneNumber' => $phoneNumber,
            'demoMode' => true,
            'error' => $apiError->getMessage(),
            'instructions' => [
                '🔧 Gerçek arama için şu adımları takip edin:',
                '1. Elevenlabs hesabınıza gidin: https://elevenlabs.io',
                '2. Conversational AI > Phone Numbers bölümüne gidin',
                '3. "Add Phone Number" butonuna tıklayın',
                '4. Twilio seçeneğini seçin',
                '5. Telefon numaranız: +18609435227',
                '6. Twilio Account SID ve Auth Token bilgilerinizi girin',
                '7. Bu sayfayı yenileyin ve tekrar deneyin'
            ]
        ]);
    }
    
} catch (Exception $error) {
    writeLog('Genel arama hatası: ' . $error->getMessage());
    
    sendJsonResponse([
        'error' => 'Arama başlatılırken bir hata oluştu',
        'details' => PHP_ENV === 'development' ? $error->getMessage() : null
    ], 500);
}
?> 