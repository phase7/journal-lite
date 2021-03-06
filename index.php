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
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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
        entries : []

    }      
        function addEntry(entry){

            var datetime = (new Date(entry.id)).toLocaleString();
            var post_title = $("<div></div>").html("<br>"+datetime);
            post_title.append("<h3>"+entry.title+"</h3>");
            post_title.addClass("card-header");

            var delete_button = $("<button></button>").text("Delete!");
            delete_button.addClass("btn btn-sm btn-danger del-btn").attr("onClick", "deleteMe(this)");

            var edit_button = $("<button></button>").text("Change");
            edit_button.addClass("btn btn-sm btn-primary mx-1 edit-btn").attr("onClick", "editMe(this)");

            post_title.prepend(delete_button).prepend(edit_button);

            var post_content_body = $("<div></div>").text(entry.content);
            post_content_body.addClass("card-body");

            var diary_post = $("<div></div>").addClass("diary-post card my-md-3 my-1").prop("id", entry.id);
            diary_post.append(post_title).append(post_content_body);

            $(".diary").prepend(diary_post);   
            }

        function send_data(data){ $.post("add.php", {data : btoa(JSON.stringify(data)), juid : data['user']});};
        
        function deleteMe(elem){
                var diary_post = $(elem).closest(".diary-post");
                diary_post.slideUp();
                data.entries = data.entries.filter(function(item){
                    return item.id != diary_post.attr("id");
                });


                send_data(data);
            };

        function editMe(elem){
            var diary_post = $(elem).closest(".diary-post");
            diary_post.slideUp();
            data.entries = data.entries.filter(function(item){
                return item.id != diary_post.attr("id");
            });
            var post_title = $(elem).siblings("h3").html();
            var post_content = $(elem).parent().siblings(".card-body").html();
            $('#triggerNew').trigger('click');
            $("#post-title").val(post_title);
            $("#post-content-body").val(post_content);
        };            

    $(document).ready(function() {
        //implement delete https://stackoverflow.com/a/20690490/11764123

        $("#triggerNew").click(function() {
            $("#input-field").slideDown("fast");
            $("#post-title").val("");
            $("#post-content-body").val("");
        });
        $("#add-post").click(
            function() {
                if ((($("#post-content-body").val() == '') || ($("#post-title").val() == '')) == false) { //returns true when both field have some 
                    $("#input-field").slideUp("slow");                                        
                    var entry = { "id" : Date.now(), "title":$("#post-title").val() , "content": $("#post-content-body").val() }
                    addEntry(entry);
                    data["entries"].push(entry);
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
                    
    </script>
</body>

</html>
