<?php
require_once("config.php");

function get_news() {
    	global $news_apiKey;
	$api_key=$news_apiKey;
	// Get the current date programmatically in YYYY-MM-DD format
    $current_date = date('Y-m-d');
    
    // NewsAPI endpoint and parameters
    $endpoint = 'https://newsapi.org/v2/everything';
    $params = [
        'q' => 'global',                  // Broad query to get general news
        'from' => $current_date,       // Articles from today
        'to' => $current_date,         // Articles up to today
        'sortBy' => 'popularity',      // Sort by popularity
        'pageSize' => 20,              // Limit to 20 articles
	'language' => 'en',
        'apiKey' => $api_key           // Your NewsAPI key
    ];
    
    // Build query string
    $query_string = http_build_query($params);
    $url = $endpoint . '?' . $query_string;
    
    // Initialize cURL
    $ch = curl_init();
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: MyNewsApp/1.0' // NewsAPI requires a User-Agent
    ]);
    
    // Execute cURL request
    $response = curl_exec($ch);
    
    // Check for cURL errors
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return [
            'status' => 'error',
            'message' => 'cURL error: ' . $error
        ];
    }
    
    // Get HTTP status code
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Close cURL session
    curl_close($ch);
    
    // Decode JSON response
    $data = json_decode($response, true);
    
    // Check for API errors or non-200 status code
    if ($http_code !== 200 || isset($data['status']) && $data['status'] === 'error') {
        return [
            'status' => 'error',
            'message' => isset($data['message']) ? $data['message'] : 'HTTP error: ' . $http_code
        ];
    }
    
    // Return successful response with articles
    return [
        'status' => 'ok',
        'articles' => $data['articles'] ?? []
    ];
}
/*
$result = get_news();

// Handle the result
if ($result['status'] === 'ok') {
    foreach ($result['articles'] as $article) {
        echo "Title: " . ($article['title'] ?? 'N/A') . "\n";
        echo "Source: " . ($article['source']['name'] ?? 'N/A') . "\n";
        echo "Published: " . ($article['publishedAt'] ?? 'N/A') . "\n";
        echo "URL: " . ($article['url'] ?? 'N/A') . "\n";
        echo "----------------------------------------\n";
    
	print_r($result['articles']);
    
} else {
    echo "Error: " . $result['message'] . "\n";
}
*/

?>
