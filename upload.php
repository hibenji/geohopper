<?php

// get the json from the post request
$json = file_get_contents('php://input');

// check if empty
if (empty($json)) {
    echo "Empty json, stop it.";
    die();
}

include 'config.php';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    echo "Connection failed";
}

// generate a unique id for the json
$code = uniqid();
echo $code;

// escape the json sql injection
$json = $conn->real_escape_string($json);

// remove any html tags
$json = strip_tags($json);

// get current unix timestamp
$timestamp = time();

// save it to the database
$sql = "INSERT INTO `geohopper`.`traces` (`code`, `json`, `timestamp`) VALUES ('$code', '$json', '$timestamp');";
$result = $conn->query($sql);

?>