<?php
sendJsonResponse([
    'status' => 'OK',
    'timestamp' => date('c'),
    'environment' => PHP_ENV,
    'apiKeyConfigured' => !empty(ELEVENLABS_API_KEY) && ELEVENLABS_API_KEY !== 'your_api_key_here',
    'agentIdConfigured' => !empty(ELEVENLABS_AGENT_ID) && ELEVENLABS_AGENT_ID !== 'your_agent_id_here',
    'phoneNumber' => '+1 860 943 5227',
    'server' => 'PHP/' . phpversion()
]);
?> 