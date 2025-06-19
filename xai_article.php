<?php
require_once("config.php");

/**
 * Analyzes a news article URL for sentiment and emotions using the xAI API.
 *
 * @param string $url The URL of the news article to analyze.
 * @return void Outputs JSON with sentiment, emotions, and confidence scores.
 */
function xai_analyze_article($url) {
    // xAI API configuration
    //$xai_api_key = 'YOUR_XAI_API_KEY'; // Replace with your xAI API key
    global $xai_apiKey;
    $xai_api_key = $xai_apiKey;
    $xai_endpoint = 'https://api.x.ai/v1/chat/completions';
    $model = 'grok-3-latest';

    // Validate URL
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        echo json_encode(['error' => 'Invalid URL provided']);
        return;
    }

    // Prepare xAI API request payload
    $payload = [
        'model' => $model,
        'messages' => [
            [
                'role' => 'system',
                'content' => 'You are a helpful AI assistant with expertise in sentiment and emotion analysis, capable of accessing web content in real-time via DeepSearch.'
            ],
            [
                'role' => 'user',
                'content' => "Fetch the content of the news article at the following URL and analyze its sentiment (scale of 0=very negative to 1=very positive) and find the single most dominant emotion in the article. Provide output in XML tags: article_sentiment, article_emotion. No other output. URL: \"$url\""
            ]
        ],
        'search_parameters' => [
            'mode' => 'auto',
            'return_citations' => true,
            'sources' => [['type' => 'web']] // Enable web content fetching
        ],
        'temperature' => 0.7,
        'max_tokens' => 1000
    ];

    // Make xAI API request
    $response = make_xai_api_request($xai_endpoint, $xai_api_key, $payload);
    if (!$response) {
        echo json_encode(['error' => 'Failed to get response from xAI API']);
        return;
    }

    // Output the result
    //echo json_encode($response, JSON_PRETTY_PRINT);
	//echo $response;
	$sent = getTextBetween($response,"<article_sentiment>","</article_sentiment>");
	$emo = getTextBetween($response,"<article_emotion>","</article_emotion>");

	$result = array();
	$result['sentiment'] = $sent;
	$result['emotion'] = $emo;
	return $result;

}

/**
 * Makes a POST request to the xAI API.
 *
 * @param string $endpoint The xAI API endpoint.
 * @param string $api_key The xAI API key.
 * @param array $payload The request payload.
 * @return array|null The parsed response or null on failure.
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
        return ['error' => "API request failed with HTTP code $http_code"];
    }

    $data = json_decode($response, true);
    return $data['choices'][0]['message']['content'] ?? ['error' => 'No content in API response'];
}



// Example usage
//$article_url = 'https://www.yahoo.com/news/israel-warns-major-strikes-tehran-154429058.html'; // Replace with a real article URL
//xai_analyze_article($article_url);

?>
