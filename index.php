<?php

//******user id using cookies******
/*********************************/

$cookie_name = "uid";
include('connect.php');

if(!isset($_COOKIE[$cookie_name])) {
    $cookie_value = uniqid();
    $current_user = $cookie_value;
    $cookie_domain = "/";
    setcookie($cookie_name, $cookie_value, time() + (86400 * 200), $cookie_domain); // 86400 = 1 day
    print("Hello New User!");
    $data_fromdb = "";

} else {
    // print("Hey I've seen you before!");
    $current_user = $_COOKIE[$cookie_name];
    $sql_getEntries = "select data from entries where juid = '$current_user'";
    if ($result = mysqli_query($conn, $sql_getEntries)){
        $data_fromdb = mysqli_fetch_row($result)[0];
        $data_json = base64_decode($data_fromdb);
        $journal_data = json_decode($data_json, true); //for loading from backend
        $journal_entries = $journal_data['entries'];

        // var_dump($journal_entries);
        // print($data_fromdb);
        // print($journal_entries[1]['content']);
    }
}


 ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Daily Journal</title>
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.css">
    <style>
        #input-field{
    		 display: none;
    	}

    	.jumbotron{
    		padding-top: 2rem;
    		padding-bottom: 2rem;
    		padding-left: 10rem;
    		padding-right: 10rem;
    	}
    </style>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.js"></script>
    <script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.js"></script>
    <!-- <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.css"> -->
</head>

<body>
    <div class="container my-md-1 p-2">
        <div class="row">

            <div class="col-sm-2"></div>

            <div class="col-sm-8">
                <div class="text-center text-light my-2 py-4 bg-secondary">
                    <h1>Daily Journal</h1>
                </div>

                <h2 id="triggerNew" class="w-25"><button class="btn btn-lg btn-outline-primary" style="cursor: pointer;">New Entry</button></h2>
                <div id="input-field">
                    <form action="#" class="form-group">
                        <label for="post-title">Title</label>
                        <input type="text" class="form-control w-50" id="post-title" required>
                        <label for="post-content">Content</label>
                        <textarea name="" id="post-content-body" cols="30" rows="5" class="form-control" required></textarea>
                        <div class="my-2 d-flex flex-row align-items-center justify-content-center">
                            <button class="btn btn-outline-info" id="add-post" type="button">Add!</button>
                        </div>
                    </form>
                </div>
                <div class="diary my-1"></div>
            </div>
            <div class="col-sm-2"></div>
        </div>
    </div>
    <script>

    var data ={
        user : "<?=$current_user?>",

    }
    var entries = [];      
        function addEntry(entry){

            var datetime = (new Date(entry.id)).toLocaleString();
            var post_title = $("<div></div>").text(datetime);
            post_title.append("<h3>"+entry.title+"</h3>");
            post_title.addClass("card-header");


            var post_content_body = $("<div></div>").text(entry.content);
            post_content_body.addClass("card-body");


            var diary_post = $("<div></div>").addClass("diary-post card my-md-3 my-1").prop("id", entry.id);
            diary_post.append(post_title).append(post_content_body);


            $(".diary").prepend(diary_post);


            entries.push(entry);
            }
    $(document).ready(function() {

        $("#triggerNew").click(function() {
            $("#input-field").slideDown("fast");
            $("#post-title").val("");
            $("#post-content-body").val("");
        });
        $("#add-post").click(
            function() {
                if ((($("#post-content-body").val() == '') || ($("#post-title").val() == '')) == false) { //returns true when both field have some 
                    $("#input-field").slideUp("slow");
                    // $('body').append($("#post-title").val());
                    
                    var entry = { "id" : $.now(), "title":$("#post-title").val() , "content": $("#post-content-body").val() }
                    
                    // var entry_id = entry.id;
                    
                    addEntry(entry);
                    data["entries"] = entries;
                    // console.log(JSON.stringify(entries));
                    send_data(data);
                }
            });

             if ('<?=$data_fromdb?>' !== ""){
                var data_fromdb = '<?=$data_fromdb?>';
                data = JSON.parse(atob(data_fromdb));
                // data = JSON.parse(data_json);
                // console.log((data))
                data.entries.forEach(addEntry);
            }
    });
    function send_data(data){ $.post("add.php", {data : btoa(JSON.stringify(data)), juid : data['user']}); console.log};
                    
    </script>
</body>

</html>