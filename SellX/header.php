<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location:login.php");
    exit;
}

require_once 'config.php';
require 'upload_function.php';


?>
<!DOCTYPE html>
<html lang="en" style="margin-bottom: 10px">

<head>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/Search.js"></script>
    <!--<link type="text/css" rel="stylesheet" href="css/style.css" />-->
    <link type="text/css" rel="stylesheet" href="css/head_style.css" />


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link type="text/css" rel="stylesheet" href="css/profile_style.css" />
    <script src="js/jquery.min.js"></script>
    <script src="js/Cards.js"></script>

    <style>
        .tab-selected {
            line-height: 1.2;
            border-bottom: 2px solid royalblue;
        }

        #arrow_img_before {
            width: 15px;
            height: 15px;
            animation: arrowrotateout 0.3s forwards;
            animation-timing-function: ease-out;
            margin-top: 15px;
        }

        #arrow_img {
            width: 15px;
            height: 15px;
            animation: arrowrotate 0.3s forwards;
            animation-timing-function: ease-out;
            margin-top: 15px;
        }

        @keyframes arrowrotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(180deg);
            }
        }

        @keyframes arrowrotateout {
            from {
                transform: rotate(180deg);
            }

            to {
                transform: rotate(0deg);
            }
        }

        #header_pic {
            width: 50px;
            height: 50px;
        }

        .user-info {
            display: flex;
        }


        .acc-drop-content-invisible {
            display: none;
            right: 32px;
            top: 50px;
        }

        .acc-drop-content-visible {
            display: block;
            position: absolute;
            float: left;
            right: 32px;
            top: 55px;
            margin: 0px;
            padding: 0px;
            background: white;
            z-index: 15;
        }

        .acc-drop-content-visible ul {
            list-style: none;
            margin: 0px;
            padding: 0px;
            font-family: Arial, Helvetica, sans-serif;
        }

        .acc-img-dropdown-text {
            display: block;
            margin-top: 6px;
            margin-left: 3px;
        }

        .acc-drop-content-visible ul li {
            margin: 0px;
            background-color: #f9f9f9;
            min-width: 80px;
            width: 150px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            padding: 10px 16px 10px 16px;
            z-index: 1;
            cursor: pointer;
        }



        .acc-drop-content-visible ul li:hover {
            background-color: #e3e3e3;
            color: red;
            left: auto;
            right: 0;
        }
    </style>
    <script>
        $(document).ready(function() {
            $.post('functions.php',{action:'getProfileData',username:'<?php echo $_SESSION['username'] ?>'}
            ,function(res){
                res=JSON.parse(res);
                
                $('#user_name').text(res.NAME.split(' ')[0]);
                $('#profile_pic').attr('src',res.PROFILE_IMG_PATH);
            });
        });

        function startdropdown() {
            var arrows = document.getElementsByClassName("arrow-image");

            if (arrows[0].id != "arrow_img") {
                arrows[0].id = "arrow_img";
            } else {
                arrows[0].id = "arrow_img_before";
            }
            const dropdown = document.getElementById("dropdown");
            dropdown.classList.toggle("acc-drop-content-visible");
        }

        function tabSelected(type) {
            var tablinks = document.getElementsByClassName("tab");
            for (var i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" tab-selected", "");
            }

            document.getElementById(type).className += " tab-selected";
            console.log('type = ' + type);
            if (type != 'HOME')
                getFilterData(type);

            getData(type);

        }

        function redirectOnTab(tab) {
            document.location = 'body.php?tab=' + tab;
        }

        window.onclick = function(event) {
            if (!event.target.matches('.arrow-image')) {
                var dropdown = document.getElementById("dropdown");
                if (dropdown.classList.contains('acc-drop-content-visible')) {
                    dropdown.classList.remove('acc-drop-content-visible')
                    dropdown.classList.add('acc-drop-content-invisible');
                    document.getElementsByClassName("arrow-image")[0].id = "arrow_img_before";
                }
            }
        }
    </script>
</head>

<body id="header_body">
    <header>
        <div class="container-header">
            <div class="row-header">
                <!-- row of header -->
                <div class="col-md1">
                    <div class="logo">
                        <!-- logo of header -->
                        <a href="#">
                            <img src="system_img/sellx_logo.png" width="100%" height="100%" style="border-radius: 20px;min-height:40px;min-width:90px">
                        </a>
                    </div>
                </div>
                <div class="col-md6">
                    <div class="my-header-search">
                        <!-- for search bar -->
                        <form style="display: flex">
                            <select class="my-input-select form-control" id='search_category'>
                                <option value="ALL">All Categories</option>
                                <option value="BOOKS">Books</option>
                                <option value='LAPTOP'>Laptop</option>
                                <option value="PG_HOSTEL">PG/Hostel (share)</option>
                                <option value="OTHER">Other Gadgets</option>
                            </select>
                            <div class="search-list" id="search_list" style="display:block;">
                                <input class="my-header-input" id="search_header" type="text" placeholder="Search here" onkeyup="search(this.value)">
                                <div class='list-group' id='search_result'>

                                </div>
                            </div>
                            <button id="search_btn" class="search-btn">Search</button>
                        </form>
                    </div>
                </div>
                <div class="col-md_1">
                    <div class="account-info" style="margin-bottom:0px;padding-bottom:10px">
                        <!-- for information on top right corner (user account info) -->
                        <div class="sell-btn">
                            <input type="button" value="SELL" onclick="document.location='choose_category.php'"></input>
                        </div>

                        <div class="favourite-info" id="fav_btn">
                            <img src="system_img/fav-icon.png" id="fav_icon" width="30px" onclick=document.location='Favourite.php'>
                            <!--<p style="color:white; margin:0px;padding:0px">favourite</p>-->
                        </div>


                        <div class="user-info">
                            <img src="system_img/account-icon.png" height="30px;" style="padding-right:5px;">
                            <!-- <p style="color:white; margin:0px;">account</p>-->
                            <div class="arrow" id="acc_btn">
                                <!-- remove text "arrow"-->
                                <img onclick="startdropdown()" class="arrow-image" id="arrow_img_before" src="system_img/arrowicon.png" alt="">
                                <div class="acc-drop-content-invisible" id="dropdown">
                                    <ul>
                                        <li>
                                            <?php retrieveProfilePic(); ?>
                                            <div class="acc-img-dropdown" style="display:flex;">
                                                <div class="image-cropper" id="header_pic">
                                                    <img id="profile_pic">
                                                </div>
                                                <div class="acc-img-dropdown-text">
                                                    <h5 style="margin:2px;">Hello,</h5>
                                                    <h4 style="margin:0px;" id="user_name"></h4>
                                                </div>
                                            </div>
                                        </li>
                                        <li onclick="document.location='MyAds.php'">My Ads</li>
                                        <li onclick="document.location='myprofile.php'">My Profile</li>
                                        <li onclick="document.location='logout.php'">Logout</li>
                                    </ul>
                                </div>
                            </div>

                        </div>


                    </div>
                </div>
            </div>


            <div class="header-2">
                <ul>

                    <li id="HOME" data-toggle="tab" class="tab" onclick="redirectOnTab(this.id)">Home</li>
                    <li id="BOOKS" class="tab" onclick="redirectOnTab(this.id)" data-toggle="tab">Books</li>
                    <li id="LAPTOP" class="tab" onclick="redirectOnTab(this.id)" data-toggle="tab">Laptop</li>
                    <li id="PG_HOSTEL" class="tab" onclick="redirectOnTab(this.id)" data-toggle="tab">PG/Hostel(share)</li>
                    <li id="OTHER" class="tab" onclick="redirectOnTab(this.id)" data-toggle="tab">Other</li>
                </ul>
            </div>

        </div>

    </header>
</body>

</html>