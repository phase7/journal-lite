<?php
    include('connect.php');
    if ($_SERVER["REQUEST_METHOD"]=="POST"){
        // print_r($_POST);
        $data = (($_POST['data']));
        $juid = $_POST['juid'];
        $sql_insert = "INSERT INTO `entries`(`juid`, `data`) VALUES ('$juid', '$data') on duplicate KEY UPDATE `data` = '$data'";
         $sql_result_insert = $conn -> query($sql_insert);
         if ($conn -> error) {
            print($conn -> error);
           }
        // print( 'executed : '.$sql_insert);
    }
?>