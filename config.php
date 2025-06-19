<?php
$news_apiKey="";
$xai_apiKey="";
$table_url = ""; // full url of the table.php file, used to take a screenshot + post to X
// YOU MUST ALSO CONFIGURE YOUR API ACCESS TOKEN + SECRET INFO IN x_post.php
function getTextBetween($string, $start, $end, $inclusive = false) {
    $startPos = strpos($string, $start);
    
    if ($startPos === false) {
        return false;
    }
    
    $startPos += $inclusive ? 0 : strlen($start);
    $endPos = strpos($string, $end, $startPos);
    
    if ($endPos === false) {
        return false;
    }
    
    if ($inclusive) {
        $endPos += strlen($end);
    }
    
    return substr($string, $startPos, $endPos - $startPos);
}

/**
 * Extract ALL occurrences of text between two strings
 * 
 * @param string $string The source string
 * @param string $start The starting delimiter
 * @param string $end The ending delimiter
 * @param bool $inclusive Whether to include the delimiters in results (default: false)
 * @return array Array of found strings
 */
function getAllTextBetween($string, $start, $end, $inclusive = false) {
    $results = [];
    $offset = 0;
    
    while (($startPos = strpos($string, $start, $offset)) !== false) {
        $searchStart = $startPos + ($inclusive ? 0 : strlen($start));
        $endPos = strpos($string, $end, $searchStart);
        
        if ($endPos === false) {
            break;
        }
        
        $extractEnd = $inclusive ? $endPos + strlen($end) : $endPos;
        $results[] = substr($string, $searchStart, $extractEnd - $searchStart);
        
        $offset = $endPos + strlen($end);
    }
    
    return $results;
}

?>
