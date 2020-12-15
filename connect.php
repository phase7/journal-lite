<?php
include('dbconfig.php');
$servername = $myservername;
$username = $myusername;
$password = $mypassword;
$dbname = $mydbname;

// Create connection
$conn =new mysqli($servername, $username, $password,$dbname);

// Check connection
if ($conn -> connect_errno ) {
    die("Connection failed: " . $conn -> connect_errno );
}
//echo "Connected successfully";
?>