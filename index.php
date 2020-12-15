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
    if ($result = $conn -> query($sql_getEntries)){
        $data_fromdb = $result -> fetch_row()[0];
        
    }
}
/************
 * get assignees
 */
$sql_assignees = "SELECT * FROM `asignees`";
$assignees = array();
$idx = 0;
if ($result = $conn -> query($sql_assignees)){
    //$data_fromdb = mysqli_fetch_row($result); 
    while($row = $result -> fetch_assoc()){
        $assignees[$idx]["name"] = $row["name"];
        $assignees[$idx]["id"] = $row["id"];
        $idx++;
        // print_r($row["name"]);
        // print("\n");
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
    var all_assignees = <?= json_encode($assignees) ?>;
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
            delete_button.addClass("btn btn-sm btn-danger mx-1 del-btn").attr("onClick", "deleteMe(this)");

            var edit_button = $("<button></button>").text("Change");
            edit_button.addClass("btn btn-sm btn-primary edit-btn").attr("onClick", "editMe(this)");

            var label_assign = $("<label></label>").html("Assigned to :&nbsp");

            var select_assign = $("<select></select>");
            select_assign.append("<option value=\"0\">none</option>");
            all_assignees.forEach(function (item){
                select_assign.append(`<option value=${item['name']}>`+item['name']+"</option>");
            });

            if (entry.assignee !== ""){
                select_assign.val(entry.assignee).change();
            }
            select_assign.attr("onChange", "selectMe(this)");
            
            post_title.prepend(delete_button).prepend(edit_button).append(label_assign).append(select_assign);

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
        function selectMe(elem){
            // alert($(elem).children("option:selected").val());
            entry_id = $(elem).parent().parent().attr("id");
            data.entries.forEach(function(item){
                // console.log(item.id, item.id == entry_id);
                if (item.id == entry_id){
                    item["assignee"] = $(elem).children("option:selected").val();
                    // console.log(item['assignee']);
                }
            });
            send_data(data);
        };

    $(document).ready(function() {
        //implement delete https://stackoverflow.com/a/20690490/11764123

        if ('<?=$data_fromdb?>' !== ""){
                var data_fromdb = '<?=$data_fromdb?>';
                data = JSON.parse(atob(data_fromdb));
                // data = JSON.parse(data_json);
                // console.log((data))
                data.entries.forEach(addEntry);
            }

        $("#triggerNew").click(function() {
            $("#input-field").slideDown("fast");
            $("#post-title").val("");
            $("#post-content-body").val("");
        });
        $("#add-post").click(
            function() {
                if ((($("#post-content-body").val() == '') || ($("#post-title").val() == '')) == false) { //returns true when both field have some 
                    $("#input-field").slideUp("slow");                                        
                    var entry = { "id" : Date.now(), "title":$("#post-title").val() , "content": $("#post-content-body").val(), "assignee": "" }
                    addEntry(entry);
                    data["entries"].push(entry);
                    // console.log(JSON.stringify(entries));
                    send_data(data);
                }
            });
           
        });
                    
    </script>
</body>

</html>