<?php
include 'header.php'
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Ads</title>
    <script src="js/jquery.min.js"></script>
    <script src="js/Cards.js"></script>
    <link rel="stylesheet" href="css/sliderCSS2.css">
    <link rel="stylesheet" href="css/ad_post.css">
    <style>
        .card-fav {
            box-shadow: 0px 4px 4px 4px lightgray;
            margin: 20px 10px 10px 10px;
            display: flex;
            flex-flow: column;
            height: 100%;
        }
    </style>
</head>

<body>
    <div class="card-fav">
        <h2 style="padding:5px;border-bottom:2px solid lightgray">My Ads</h2>

        <div style="width: 100%;" class="card_list" id="card_items">

        </div>
    </div>
</body>
<script>
    $(document).ready(function() {
        getData('myads');
    });
</script>

</html>