<?php
    include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Category</title>
    <link rel="stylesheet" type="text/css" href="css/ad_post.css">
    <script src='js/jquery.min.js'></script>
    <style>
        .card-category{
            box-shadow: 0px 4px 4px 4px lightgray;
            width: 100%;
            max-height: 100px;
            
        }
    </style>
</head>
<body>
    <div class="card-main">
        <center>
            <h2 class="main-head">POST YOUR AD</h2>
        </center>
        <div class="head-1" style="border-bottom:none;">
                    <h3><b>SELECT CATEGORY</b></h3>
        </div>
        <div class="list-group">
            <a  class="list-group-item list-group-item-action" id="BOOKS">
                BOOKS
            </a>
            <a  class="list-group-item list-group-item-action" id="LAPTOP/MOBILE">LAPTOP/MOBILE</a>
            <a  class="list-group-item list-group-item-action" id="PG/HOSTEL">PG/HOSTEL(SHARE)</a>
            <a  class="list-group-item list-group-item-action" id="OTHER">OTHER</a>
            
        </div>
    </div> 


</body>
<script>
    $('.list-group-item').click(function(e){
        var id=e.target.id;
        console.log("clicked");
        sessionStorage.setItem('category',id);
        document.location='AdUpload.php';
    });
    $('.list-group-item').hover(function(e){
        //console.log(e.target);
        e.target.className+=" active";
        
    }); 
    $('.list-group-item').mouseleave(function(e){
        console.log("leave");
        e.target.classList.remove("active");
    });
</script>
</html>