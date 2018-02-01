<?php

class createConnection {
    // if used localhost on cli, this error is thrown. PHP Warning:  mysqli::__construct(): (HY000/2002): No such file or directory in /opt/lampp/htdocs/Song Downloader/connect.php
    var $dbhost = "127.0.0.1";
    var $dbuser = "root";
    var $dbpass = "";
    var $db = "songdata";
    var $my_conn;

    // function to connect to db named "songdata"
    function connectToDatabase() {
        // sudo apt-get install mysqli
        $conn = new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->db);

        if (!$conn) {
            die("Connect failed: %s\n" . $conn->error);
        } else {
            $this->my_conn = $conn;
            echo "\n\nConnection established \n";
        }

        return $this->my_conn;
        echo "returned";
    }

    function closeConnection() {
        mysqli_close($this->my_conn);
        echo "Connection closed";
    }

}


// saving data to database into table "song_info"
function savingToDb($video_title, $video_duration, $video_url, $songs_path) {
    // connection to database using class
    $connection = new createConnection();
    $my_conn    = $connection->connectToDatabase();
    $sql = "INSERT INTO songs_info (Name, Duration, URL, Path )
  VALUES ('$video_title','$video_duration' ,'$video_url', '$songs_path')";

    if ($my_conn->query($sql) === TRUE) {
        echo "Data has been successfully stored in Database.";
    } else {
        echo "Error: " . $sql . "<br>" . $my_conn->error;
    }
}


?>
