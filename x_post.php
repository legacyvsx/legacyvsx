<?php
require 'vendor/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;
$consumer_key = '';
$consumer_secret = '';
$access_token = '';
$access_token_secret = '';

$imagemagick = '/usr/bin/convert'; // Update to '/usr/local/bin/convert' if compiled from source
$input_file = '/var/www/html/latest.jpg';
$output_file = '/var/www/html/latest_processed.jpg';

// Resize to 50% height or X won't let us post it

$imagemagick_command = escapeshellcmd("$imagemagick " . escapeshellarg($input_file) . " -strip -quality 100 -interlace none -crop 1024x7001+0+0 " . escapeshellarg($output_file));
exec($imagemagick_command);



$connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);

$image = [];
$connection->setApiVersion('1.1');
$media = $connection->upload('media/upload', ['media' => '/var/www/html/latest_processed.jpg']);
var_dump($media);

array_push($image, $media->media_id_string);

$media = $connection->upload('media/upload', ['media' => '/var/www/html/logo.jpg']);
var_dump($media);

array_push($image, $media->media_id_string);

$latest = "LegacyVSX is an app that tracks the differences in how news stories are presented in legacy media vs X. See the website for more info and full data. Attached is the data for " . date("Y-m-d");

$data =  [
   'text' => $latest,
   'media'=> ['media_ids' => $image]
];

$connection->setApiVersion('2');
$content = $connection->post("tweets", $data, array());

var_dump($content);
?>
