<?php
include 'config.php';

// get the json from the post request
$ip = file_get_contents('php://input');

// if it's empty, stop it
if (empty($ip)) {
  echo "Empty ip, stop it.";
  die();
}

// Create connection
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
  echo "Connection failed";
}

// escape the ip sql injection
$ip = $conn->real_escape_string($ip);

// save it to the database
$sql = "SELECT * FROM `geohopper`.`ip_cache` WHERE `ip` = '$ip';";
$result = $conn->query($sql);

// get the json from the database
if ($result->num_rows > 0) {
  // output data of each row
  $row = $result->fetch_assoc();
  $json = $row["json"];
  echo $json;
} else {
  // get the json from the ipinfo api
  $json = file_get_contents("http://ipinfo.io/$ip/json?token=$ipinfo_token");

  echo $json;
  // get unix timestamp
  $timestamp = time();

  // save it to the database
  $sql = "INSERT INTO `geohopper`.`ip_cache` (`ip`, `json`, `timestamp`) VALUES ('$ip', '$json', '$timestamp');";
  $result = $conn->query($sql);

}

?>