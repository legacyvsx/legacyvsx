<?php
require_once("config.php");

/**
 * Analyzes X posts related to a news article URL using the xAI API.
 *
 * @param string $url The URL of the news article.
 * @return void Outputs XML with average X post sentiment, most common emotion, and commentary.
 */
function xai_analyze_x_posts_from_article($url,$a_sent,$a_emo) {
    // xAI API configuration
	global $xai_apiKey;
	$xai_api_key = $xai_apiKey;
    $xai_endpoint = 'https://api.x.ai/v1/chat/completions';
    $model = 'grok-3-latest';

    // Validate URL
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<error>Invalid URL provided</error>';
        return;
    }

    // Prepare xAI API request payload
    $payload = [
        'model' => $model,
        'messages' => [
            [
                'role' => 'system',
                'content' => 'You are a helpful AI assistant with expertise in sentiment and emotion analysis, capable of accessing X posts in real-time via DeepSearch.'
            ],
            [
                'role' => 'user',
                'content' => "Find recent popular X posts about the same topic as $url. For each post (up to 20), analyze the sentiment (very negative=0, negative=0.25, neutral=0.5, positive=0.75, very positive=1) and dominant emotion. Assume sentiment of the article is $a_sent and $a_emo is its dominant emotion. Compute average sentiment, most common dominant emotion, and a max 3-sentence commentary on the differences in tone, sentiment, and emotion between the article and X posts. Output these as xml tags x_sentiment, x_emotion, commentary. Don't output the content of each post, there should be only 3 tags in the XML output, no sub tags, the first should be only a single numerical value, second is 1 emotion, third is commentary. If sentiment scores are referred to it in the commentary, make sure they are logically correct. For example, do not say X posts have a more negative sentiment than the article's sentiment if that is not true."
            ]
        ],
        'search_parameters' => [
            'mode' => 'auto',
            'return_citations' => true,
            //'sources' => [
                //['type' => 'web'], // For article content
              //  ['type' => 'x_posts'] // For X posts
            //]
		// I'm honestly not sure why I had to get rid of the sources parameter as it says to use it in the docs, I asked Grok which was no help, but I always got a 422 error with sources enabled.
        ],
        'temperature' => 0.7,
        'max_tokens' => 1500
    ];

    // Make xAI API request
    $response = make_xai_api_request2($xai_endpoint, $xai_api_key, $payload);
    if (is_array($response) && isset($response['error'])) {
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo "<error>{$response['error']} (Code: {$response['code']}): {$response['details']}</error>";
        return;
    }

	//echo $response;
	$result = array();
	$result['sentiment'] = getTextBetween($response,"<x_sentiment>","</x_sentiment>");
	$result['emotion'] = getTextBetween($response,"<x_emotion>","</x_emotion>");
	$result['commentary'] = getTextBetween($response,"<commentary>","</commentary>");
	return $result;
	/*

    // Parse response
    $parsed_response = parse_xai_response($response);
    if (!$parsed_response) {
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<error>Invalid or incomplete API response</error>';
        return;
    }

    // Output XML
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<result>';
    echo "<x_sentiment>{$parsed_response['average_sentiment']}</x_sentiment>";
    echo "<x_emotion>{$parsed_response['most_common_emotion']}</x_emotion>";
    echo "<commentary>" . htmlspecialchars($parsed_response['commentary']) . "</commentary>";
    echo '</result>';
	*/
}

/**
 * Makes a POST request to the xAI API and outputs detailed errors.
 *
 * @param string $endpoint The xAI API endpoint.
 * @param string $api_key The xAI API key.
 * @param array $payload The request payload.
 * @return array|string|null The parsed response content, error array, or null on failure.
 */
function make_xai_api_request2($endpoint, $api_key, $payload) {
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
    $curl_error = curl_error($ch);
    $curl_errno = curl_errno($ch);
    curl_close($ch);

    // Handle cURL errors
    if ($response === false || $curl_errno) {
        return [
            'error' => 'cURL request failed',
            'code' => $curl_errno,
            'details' => $curl_error ?: 'No additional details available'
        ];
    }

    // Handle HTTP errors
    if ($http_code !== 200) {
        $error_details = json_decode($response, true);
        return [
            'error' => 'API request failed',
            'code' => $http_code,
            'details' => $error_details['error']['message'] ?? "HTTP $http_code: No error message provided"
        ];
    }

    // Parse response
    $data = json_decode($response, true);
    if (!isset($data['choices'][0]['message']['content'])) {
        return [
            'error' => 'Invalid API response',
            'code' => 200,
            'details' => 'No content found in response'
        ];
    }

    return $data['choices'][0]['message']['content'];
}

/**
 * Parses the xAI API response to extract sentiment, emotion, and commentary.
 *
 * @param string $response The raw API response content.
 * @return array|null Parsed data or null if invalid.
 */
function parse_xai_response($response) {
    // Hypothetical parsing (API response format may vary)
    try {
        // Example response structure (based on prompt expectations)
        $data = json_decode($response, true);
        if (!$data || !isset($data['average_sentiment'], $data['most_common_emotion'], $data['commentary'])) {
            // Fallback: Parse text response manually (simplified)
            $lines = explode("\n", $response);
            $average_sentiment = 0.5; // Default neutral
            $most_common_emotion = 'unknown';
            $commentary = 'No commentary available.';

            foreach ($lines as $line) {
                if (preg_match('/Average Sentiment: (\d+\.\d+)/', $line, $matches)) {
                    $average_sentiment = floatval($matches[1]);
                } elseif (preg_match('/Most Common Emotion: (\w+)/', $line, $matches)) {
                    $most_common_emotion = $matches[1];
                } elseif (strpos($line, 'Commentary:') !== false) {
                    $commentary = trim(str_replace('Commentary:', '', $line));
                }
            }

            return [
                'average_sentiment' => $average_sentiment,
                'most_common_emotion' => $most_common_emotion,
                'commentary' => $commentary
            ];
        }

        return $data;
    } catch (Exception $e) {
        return null;
    }
}

// Example usage
//$article_url = 'https://time.com/7294024/pinterest-time100-talk/'; // Replace with a real article URL
//xai_analyze_x_posts_from_article($article_url);

?>
