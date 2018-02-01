<?php
namespace youtubetomp3;

class downloader {
  function downloadSong($video_id) {
      $video_fetch_url = "http://www.youtube.com/get_video_info?&video_id=" . $video_id . "&asv=3&el=detailpage&hl=en_US";
      // using the get function to get the html of the page with given url.
      $video_data = get_data($video_fetch_url);
      $video_url = "https://www.youtube.com/watch?v=" . $video_id;
      // COMMAND TO INSTALL CURL sudo apt-get install php5-curl
      /* CHECK THE PHP VERSION IF THE COMMAND DOESNT WORKS. USE php7.0-curl if the version is 7.x*/

      //parsing string to array.
      // from video_data string to video_info array
      parse_str($video_data, $video_info);
      /* If $video_info is a SimpleXml object, you can't access some of its
      attributes directly. It's a trick that is used to convert
      SimpleXml object to a classical object and get access to all of its
      attributes :)
      */
      $video_info = json_decode(json_encode($video_info));
      if (!$video_info->status === "ok") {
          die("error in fetching youtube video data");
      }

      // Getting details from video_info
      echo "\nThe Details of the file are : \n\n";
      echo "Title : " . $video_title = $video_info->title . "\n";
      echo "Video Author : " . $video_author = $video_info->author . "\n";
      $video_duration_secs = $video_info->length_seconds;
      echo "Durartion : " . $video_duration = secToDuration($video_duration_secs) . "\n";

      // url_encoded_fmt_stream_map : contains video and audio stream
      if (!isset($video_info->url_encoded_fmt_stream_map)) {
          die('No data found');
      }

      // explode splits from the delimeter ','
      $streams = explode(",", $video_info->url_encoded_fmt_stream_map);
      foreach ($streams as $stream) {
          parse_str($stream, $data);
          /* So now you have an array with type,url,itag and quality of videos
             we need to download the video in lowest quality possible.
             itags represent the video quality.
             so itag = 17 is for 3gp 144p video.
             now stripos will find itag 17, get its url and then download the
             video
          */
          if (stripos($data['itag'], '17') !== false) {
              echo "\nDownloading Video \n";
              $video = fopen($data['url'], 'r'); //the video
              $file  = fopen('video.3gp', 'w');
              stream_copy_to_stream($video, $file); //copy it to the file
              fclose($video);
              fclose($file);
              echo "Youtube Video Download finished! Now check the file. \n";

              // Audio Conversion
              echo "\nCoverting audio \n";
              $ffmpeg       = FFMpeg\FFMpeg::create();
              $video        = $ffmpeg->open('video.3gp');
              $audio_format = new FFMpeg\Format\Audio\Mp3();
              $video->save($audio_format, 'audio.mp3');
              //renaming the files and moving them to songs and videos directory
              rename("video.3gp", $video_title . ".3gp");
              rename("audio.mp3", $video_title . ".mp3");
              rename($video_title . ".3gp", "videos/" . $video_title . ".3gp");
              rename($video_title . ".mp3", "songs/" . $video_title . ".mp3");
              echo "\nAudio converted.\n\n";

              // path where song is stored. To send to db.
              $songs_path = "songs/" . $video_title . ".mp3";

              //an array to return these 4 values;
              $video_details = array($video_title, $video_duration, $video_url, $songs_path);
              return $video_details;


          } else {
              continue;
          }
      }
  }
}
?>
