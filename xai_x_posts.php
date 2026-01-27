<?php
require_once("config.php");

function xai_analyze_x_posts_from_article($url, $a_sent, $a_emo) {
    global $xai_apiKey;
    $xai_api_key = $xai_apiKey;
    $xai_endpoint = 'https://api.x.ai/v1/responses';
    $model = 'grok-4-1-fast';

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return ['sentiment' => 0, 'emotion' => 'unknown', 'commentary' => 'Invalid URL'];
    }

    echo "Searching X posts for article topic...\n";

    $payload = [
        'model' => $model,
        'input' => [
            [
                'role' => 'user',
                'content' => "Find and analyze recent X (Twitter) posts discussing this article or its topic: $url

The article has sentiment $a_sent (0=very negative, 1=very positive) and dominant emotion '$a_emo'.

For the X posts you find:
1. Calculate average sentiment (0-1 scale)
2. Identify the most common dominant emotion  
3. Write 2-3 sentences comparing article tone vs X reactions

Output ONLY these XML tags:
<x_sentiment>NUMBER</x_sentiment>
<x_emotion>EMOTION</x_emotion>
<commentary>YOUR COMMENTARY</commentary>"
            ]
        ],
        'tools' => [
            ['type' => 'x_search']
        ],
        'temperature' => 0.7,
        'max_output_tokens' => 800,
        'store' => false
    ];

    $response = make_xai_responses_api_request($xai_endpoint, $xai_api_key, $payload);
    
    if (is_array($response) && isset($response['error'])) {
        echo "API error: {$response['error']} (Code: {$response['code']})\n";
        if (isset($response['details'])) {
            echo "Details: {$response['details']}\n";
        }
        return ['sentiment' => 0, 'emotion' => 'unknown', 'commentary' => 'API request failed'];
    }

    if (!$response || !is_string($response)) {
        return ['sentiment' => 0, 'emotion' => 'unknown', 'commentary' => 'No valid response from API'];
    }

    $result = array();
    $result['sentiment'] = getTextBetween($response, "<x_sentiment>", "</x_sentiment>") ?: 0;
    $result['emotion'] = getTextBetween($response, "<x_emotion>", "</x_emotion>") ?: 'unknown';
    $result['commentary'] = getTextBetween($response, "<commentary>", "</commentary>") ?: 'No commentary available';
    
    return $result;
}

function make_xai_responses_api_request($endpoint, $api_key, $payload) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: Bearer $api_key"
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    $curl_errno = curl_errno($ch);
    curl_close($ch);

    if ($response === false || $curl_errno) {
        return [
            'error' => 'cURL request failed',
            'code' => $curl_errno,
            'details' => $curl_error ?: 'No additional details available'
        ];
    }

    if ($http_code !== 200) {
        $error_details = json_decode($response, true);
        $error_message = 'Unknown error';
        
        if (isset($error_details['error'])) {
            if (is_string($error_details['error'])) {
                $error_message = $error_details['error'];
            } elseif (isset($error_details['error']['message'])) {
                $error_message = $error_details['error']['message'];
            }
        }
        
        return [
            'error' => 'API request failed',
            'code' => $http_code,
            'details' => $error_message
        ];
    }

    $data = json_decode($response, true);
    
    // The output array contains multiple items: tool calls, reasoning, and finally the message
    // We need to find the 'message' type item with 'content'
    if (isset($data['output']) && is_array($data['output'])) {
        foreach ($data['output'] as $output_item) {
            // Look for items with type 'message' and role 'assistant'
            if (isset($output_item['type']) && $output_item['type'] === 'message' 
                && isset($output_item['role']) && $output_item['role'] === 'assistant'
                && isset($output_item['content']) && is_array($output_item['content'])) {
                
                // Now look for output_text in the content array
                foreach ($output_item['content'] as $content_item) {
                    if (isset($content_item['type']) && $content_item['type'] === 'output_text') {
                        if (isset($content_item['text'])) {
                            return $content_item['text'];
                        }
                    }
                }
            }
        }
    }
    
    return [
        'error' => 'Invalid API response',
        'code' => 200,
        'details' => 'Could not extract text from response structure'
    ];
}

?>
