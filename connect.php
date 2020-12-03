<?php
include('dbconfig.php');
$servername = $myservername;
$username = $myusername;
$password = $mypassword;
$dbname = $mydbname;

// Create connection
$conn = mysqli_connect($servername, $username, $password,$dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
//echo "Connected successfully";
?>