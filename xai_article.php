<?php
require_once("config.php");

/**
 * Analyzes a news article URL for sentiment and emotions using the xAI API.
 *
 * @param string $url The URL of the news article to analyze.
 * @return array Returns array with 'sentiment' and 'emotion' keys
 */
function xai_analyze_article($url) {
    global $xai_apiKey;
    $xai_api_key = $xai_apiKey;
    $xai_endpoint = 'https://api.x.ai/v1/chat/completions';
    $model = 'grok-3-mini';

    // Validate URL
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return ['sentiment' => 0, 'emotion' => 'unknown'];
    }

    // Fetch article content
    echo "Fetching article content...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    $article_html = curl_exec($ch);
    curl_close($ch);

    if (!$article_html) {
        echo "Failed to fetch article content\n";
        return ['sentiment' => 0, 'emotion' => 'unknown'];
    }

    // Basic text extraction (strip HTML tags)
    $article_text = strip_tags($article_html);
    $article_text = preg_replace('/\s+/', ' ', $article_text); // Clean whitespace
    $article_text = trim(substr($article_text, 0, 4000)); // Limit to 4000 chars

    echo "Analyzing with Grok...\n";
    
    // Prepare xAI API request payload (WITHOUT search_parameters)
    $payload = [
        'model' => $model,
        'messages' => [
            [
                'role' => 'system',
                'content' => 'You are a helpful AI assistant with expertise in sentiment and emotion analysis.'
            ],
            [
                'role' => 'user',
                'content' => "Analyze the following article for sentiment and emotion. Provide sentiment as a number between 0 (very negative) and 1 (very positive), and identify the single most dominant emotion. Output ONLY in these XML tags with no other text: <article_sentiment>NUMBER</article_sentiment> <article_emotion>EMOTION</article_emotion>\n\nArticle text:\n$article_text"
            ]
        ],
        'temperature' => 0.7,
        'max_tokens' => 500
    ];

    // Make xAI API request
    $response = make_xai_api_request($xai_endpoint, $xai_api_key, $payload);
    
    if (!$response || (is_array($response) && isset($response['error']))) {
        echo "API request failed\n";
        return ['sentiment' => 0, 'emotion' => 'unknown'];
    }

    // Parse response
    $sent = getTextBetween($response, "<article_sentiment>", "</article_sentiment>");
    $emo = getTextBetween($response, "<article_emotion>", "</article_emotion>");

    $result = array();
    $result['sentiment'] = $sent ? $sent : 0;
    $result['emotion'] = $emo ? $emo : 'unknown';
    
    return $result;
}

/**
 * Makes a POST request to the xAI API.
 *
 * @param string $endpoint The xAI API endpoint.
 * @param string $api_key The xAI API key.
 * @param array $payload The request payload.
 * @return string|null The response content or null on failure.
 */
function make_xai_api_request($endpoint, $api_key, $payload) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: Bearer $api_key"
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200 || $response === false) {
        echo "API error: HTTP $http_code\n";
        return null;
    }

    $data = json_decode($response, true);
    return $data['choices'][0]['message']['content'] ?? null;
}

?>
