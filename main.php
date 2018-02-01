<?php
require 'vendor/autoload.php';
require 'scrapper.php';
include 'database.php';

echo "** WELCOME TO THE MP3 DOWNLOADER ** \n \n";
$name = readline("Hey There. Lets start with your name : ");
echo "\nHello " . $name . ".\n";


$search = readline("Please enter the search query : ");
$data = searchVideo($search); //returns data array
$data_array = $data[0];
$duration_array = $data[1];
$video_info = displayVideos($data_array, $duration_array);
// returns video url,video_title, video_duration
$video_url = $video_info[0] ;
$video_title = $video_info[1];
$video_duration = $video_info[2];
$songs_path = downloadVideo($video_url, $video_title);
// returns dongs path
savingToDb($video_title, $video_duration, $video_url, $songs_path)
?>
