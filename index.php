<?php

//******user id using cookies******
/*********************************/

$cookie_name = "uid";

if(!isset($_COOKIE[$cookie_name])) {
    $cookie_value = uniqid();
    $cookie_domain = "/";
    setcookie($cookie_name, $cookie_value, time() + (86400 * 30), $cookie_domain); // 86400 = 1 day
    print("Hello New User!");

} else {
    print("Hey I've seen you before!");
    $current_user = $_COOKIE[$cookie_name];
    $journal_entries = ""; //for loading from backend
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
        <div class="jumbotron text-center text-dark">
            <h1>Daily Journal</h1>
        </div>
        <div class="container">
            <h2 id="triggerNew" class="w-25"><span class="text-primary" style="cursor: pointer;">New Entry</span></h2>
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
            <div class="diary my-1">
            	
            </div>
        </div>
    </div>
    <script>

    var data ={
        user : "<?=$current_user?>",

    }
    var entries = [];      
    $(document).ready(function() {

        $("#triggerNew").click(function() {
            $("#input-field").slideDown("fast");
        });
        $("#add-post").click(
            function() {
                if ((($("#post-content-body").val() == '') || ($("#post-title").val() == '')) == false) { //returns true when both field have some 
                    $("#input-field").slideUp("slow");
                    // $('body').append($("#post-title").val());
                    var datetime = (new Date).toLocaleString();
                    var entry_id = $.now();


                    var post_title = $("<div></div>").text(datetime);
                    post_title.append("<h3>"+$("#post-title").val()+"</h3>");
                    post_title.addClass("card-header");


                    var post_content_body = $("<div></div>").text($("#post-content-body").val());
                    post_content_body.addClass("card-body");


                    var diary_post = $("<div></div>").addClass("diary-post card my-md-3").prop("id", entry_id);
                    diary_post.append(post_title).append(post_content_body);


                    $(".diary").prepend(diary_post);


                    var entry = { "id" : entry_id, "title":$("#post-title").val() , "content": $("#post-content-body").val() }
                    entries.push(entry);
                }
            });
    });
    data["entries"] = entries;
    </script>
</body>

</html>