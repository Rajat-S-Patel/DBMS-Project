<?php

include 'header.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <link type="text/css" rel="stylesheet" href="css/profile_style.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Pic.</title>

    <script>
        function preview_image(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('pic');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
    <style>
        #main_card .image-cropper:hover {
            border: 2px solid red;
        }

        #main_card input {
            margin-left: auto;
            margin-right: auto;
            display: block;
            margin-top: 20px;
            width: 100%;
            height: 40px;
            background-color: royalblue;
            border: none;
            font-size: 95%;
            text-align: center;
            color: white;
            border-radius: 3px;
        }

      
        #label_preview{
            display:inline;
        }
        .image-cropper {
            width: 250px;
            height: 250px;
            position: relative;
            overflow: hidden;
            border-radius: 50%;
        }

        .image-cropper img {
            margin-left: auto;
            margin-right: auto;
            display: block;
            height: 100%;
            width: auto;
        }
    </style>

    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
-->
</head>

<body>
    <div class="profile-display">
        <div class="card-main-profile" id="main_card">
            <h3>Profile Picture</h3>
            <hr>
            <?php retrieveProfilePic();
            ?>

            <form action="upload.php" method="post" enctype="multipart/form-data">
                <center>
                    <div class="image-cropper">
                        <label id="label_preview"for="file-input"><img id="pic" src=<?php echo $_SESSION["src"]; ?> ></label>
                        <input id="file-input" type="file" style="display: none;" onchange="preview_image(event)" name="fileToUpload">
                    </div>
                    <input type="submit" value="Save Changes">
                </center>
            </form>


        </div>
    </div>
</body>

    

</html>