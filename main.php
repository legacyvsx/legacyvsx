<?php
require_once("get_news.php");
require_once("xai_article.php");
require_once("xai_x_posts.php");
require_once("database.php");

global $table_url;

$conn = connectDB();
$result = get_news();

// Handle the result
if ($result['status'] === 'ok') {
    foreach ($result['articles'] as $article) {
        echo "Title: " . ($article['title'] ?? 'N/A') . "\n";
        echo "Source: " . ($article['source']['name'] ?? 'N/A') . "\n";
        echo "Published: " . ($article['publishedAt'] ?? 'N/A') . "\n";
        echo "URL: " . ($article['url'] ?? 'N/A') . "\n";
        //echo "----------------------------------------\n";
	$article_stats = xai_analyze_article($article['url']);
	echo "Article sentiment: " . $article_stats['sentiment'] . "\n";
	echo "Article emotion: " . $article_stats['emotion'] . "\n";
	$x_stats=xai_analyze_x_posts_from_article($article['url'],$article_stats['sentiment'],$article_stats['emotion']);
	
	echo "X sentiment: " . $x_stats['sentiment'] . "\n";
	echo "X emotion: " . $x_stats['emotion'] . "\n";
	echo "Commentary: " . $x_stats['commentary'] . "\n";
	echo "\n";
	
	// Prepare data for database insertion
        $current_date = date('Y-m-d');
        $title = mysqli_real_escape_string($conn, $article['title'] ?? '');
        $url = mysqli_real_escape_string($conn, $article['url'] ?? '');
        $pub_date = $article['publishedAt'] ?? null;
        $a_sent = (float)($article_stats['sentiment'] ?? 0);
        $a_emo = mysqli_real_escape_string($conn, $article_stats['emotion'] ?? '');
        $x_sent = (float)($x_stats['sentiment'] ?? 0);
        $x_emo = mysqli_real_escape_string($conn, $x_stats['emotion'] ?? '');
        $commentary = mysqli_real_escape_string($conn, $x_stats['commentary'] ?? '');
        
        // Build SQL query
        $sql = "INSERT INTO headlines (date, pub_date, title, url, a_sent, a_emo, x_sent, x_emo, commentary) VALUES (";
        $sql .= "'{$current_date}', ";
        
        // Handle pub_date with STR_TO_DATE if it exists
        if ($pub_date) {
            $sql .= "STR_TO_DATE('{$pub_date}', '%Y-%m-%dT%H:%i:%sZ'), ";
        } else {
            $sql .= "NULL, ";
        }
        
        $sql .= "'{$title}', ";
        $sql .= "'{$url}', ";
        $sql .= "{$a_sent}, ";
        $sql .= "'{$a_emo}', ";
        $sql .= "{$x_sent}, ";
        $sql .= "'{$x_emo}', ";
        $sql .= "'{$commentary}'";
        $sql .= ")";
        
        // Execute the query
        $result_insert = executeQuery($sql, $conn);
        
        if ($result_insert) {
            echo "Successfully inserted article into database.\n";
        } else {
            echo "Failed to insert article into database.\n";
        }
    }
        //print_r($result['articles']);
	exec("wkhtmltoimage  --format jpg --quality 50 $table_url ./latest.jpg");
} else {
    echo "Error: " . $result['message'] . "\n";
}


?>
