<?php
    include('connect.php');
    if ($_SERVER["REQUEST_METHOD"]=="POST"){
        // print_r($_POST);
        $data = base64_encode(json_encode($_POST['data']));
        $juid = $_COOKIE["uid"];
        $sql_insert = "INSERT INTO `entries`(`juid`, `data`) VALUES ('$juid', '$data') on duplicate KEY UPDATE `data` = '$data'";
        print($sql_insert);
    }
?>