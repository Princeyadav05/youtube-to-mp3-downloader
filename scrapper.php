<?php
use YoutubeDl\YoutubeDl;
// difference b/w curl and file_get_contents
/* Function to get the conetnts of a url (html)
   Two methods can be used for this either by cURL library
   or by file_get_content function.
*/
function get_data($url) {
    //$data = file_get_contents($url);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

/*This function converts the video length (in seconds) to 00:00:00 format.
  this format is required when sending time to databse.*/
function secToDuration($seconds) {
    $minutes = intval($seconds / 60);
    $seconds = ($seconds - ($minutes * 60));
    $hours   = intval($minutes / 60);
    return "$hours:$minutes:$seconds";
}

function searchVideo($search) {

    // changing arijit singh to arijit+singh
    $search_query = str_replace(' ', '+', $search);
    $search_url   = "https://www.youtube.com/results?search_query=" . $search_query;
    echo "\nSearching on youtube ... \nSearch URL is : " . $search_url . "\n\n";

    // using the get function to get the html of the page with given url.
    $webpage_data = get_data($search_url);
    // Dom installation
    //sudo apt-get install php7.0-xml

    // need to convert the html to DOM` to parse the data.
    $dom = new DOMDocument();
    @$dom->loadHTML($webpage_data);

    /* The Video Title on YT has <a></a> tags. So picking up all the <a> tags.
       Then from all the <a> tags selecting the ones which have
       href='watch/something' and then geting their textContent ang href data
       and storing then in an associative array with url as key and textContent
       as value.
    */

    foreach ($dom->getElementsByTagName('a') as $link) {
        # Show the <a href>
        $urls = $link->getAttribute('href');
        if (stripos($urls, 'watch') !== false) {
            $video_data        = $link->textContent;
            $duration_array[] = $video_data;
            $data_array[$urls] = $video_data;
        } else {
            continue;
        }

    }

    return array($data_array, $duration_array);
}

function displayVideos($data_array, $duration_array) {
  /* The associative arrays dont have index. So indexing the keys of the
     arrays with array_keys. And displaying all the values so user can select
     one of the videos.
  */
  $keys  = array_keys($data_array);
  $index = 0;
  foreach ($data_array as $key => $value) {
      echo $index . ". " . $value;
      $index++;
      echo "\n";
  }

  echo "\n";
  $num = readline("Type the number in front of the video to download it : ");

  // From number entered by user, getting url(key of that value) and hence video_id
  if (is_numeric($num)) {
      $video_title = $data_array[$keys[$num]];
      echo "Downloading video - " . $video_title;
      $watch_id = $keys[$num];
      $video_duration = $duration_array[$num+$num];
      //global $video_id;
      //$video_id = explode('=',$watch_id, 2)[1];
      $video_url = "https://www.youtube.com/" . $watch_id;

  }


  //$dom = new DOMDocument();
  //@$dom->loadHTML($webpage_data);

  //foreach ($dom->getElementsByTagName('span') as $link) {
  //    $data = $link->textContent;
  //    if (stripos($data, $video_title) !== false) {
  //        $new_array = array($data);
  //    } else {
  //        continue;
  //    }
  //}

  //print_r($new_array);
  return array($video_url, $video_title, $video_duration);
}

function downloadVideo($video_url, $video_title){
  $dl = new YoutubeDl([
      'extract-audio' => true,
      'audio-format' => 'mp3',
      'audio-quality' => 0, // best
      'output' => '%(title)s.%(ext)s',
  ]);
  $dl->setDownloadPath('/opt/lampp/htdocs/Song Downloader/songs');
  $songs_path = "songs/" . $video_title . ".mp3";

  $video = $dl->download($video_url);
  return $songs_path;
}
?>
