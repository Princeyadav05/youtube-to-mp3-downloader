<?php

use youtubetomp3\youtubetomp3\test;

class youtubetomp3Test extends PHPUnit_Framework_TestCase {

  public function testyoutubetomp3()
  {
    $download = new downloader;
    $this->assertTrue($download->downloadSong(nwkaIHkn5hQ));
  }

}
