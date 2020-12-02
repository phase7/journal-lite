<?php
    include('connect.php');
    if ($_SERVER["REQUEST_METHOD"]=="POST"){
        // print_r($_POST);
        $data = base64_encode(json_encode($_POST['data']));
        $juid = $_POST['data']['user'];
        $sql_insert = "INSERT INTO `entries`(`juid`, `data`) VALUES ('$juid', '$data') on duplicate KEY UPDATE `data` = '$data'";
         $sql_result_insert = mysqli_query($conn, $sql_insert);
         if (!$sql_result_insert) {
            print(mysqli_error($conn));
           }
        print( 'executed'.$sql_insert);
    }
?>