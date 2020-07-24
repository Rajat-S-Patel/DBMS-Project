<?php
      include 'header.php';
      
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link type="text/css" rel="stylesheet" href="css/profile_style.css"/>
    <script src="js/jquery.min.js"></script>

    <style>
        #image_cropper{
            width:75px;
            height:75px;
            
        }
        #image_cropper img{
            margin-right: auto;
            margin-left: auto;
            display: block;
        }
        h2{
            text-align: center;
            padding: 5px;
            margin:2px auto 2px auto;
        }
        .form_table input[type="text"]{
            height: 20px;
        }

    </style>
</head>
<body id="profile_body">
    <h2>My Profile</h2>
    <div class="profile-display">
    <div class="side-nav-bar">
        <div class="card-welcome">
            <div class="img-profile">
               
                <div class="image-cropper" id="image_cropper">
                <img id="profileimg" style="width:83px;">
                </div>
            </div>
            <div class="name-profile">
                <p style="height: 10px">Welcome,</p>
               <p> <b id="welcome_name"></b></p>
            </div>
        </div>

        <div class="card-psw" id="card_psw">
            <div class="psw-change-btn" id="psw_btn">
                <input type="button" value="Change Password" onclick="document.location='password_reset.php'" id="psw-input"></input>
            </div>
            <div class="dp-change-btn" id="dp_btn">
                <input type="button" value="Change Profile Pic." onclick="document.location='changeprofileimg.php'"></input>
            </div>
        </div>
    </div>
    <div class="card-main-profile">
        <div class="card-basic">
            <h3 id="basic_title" style="margin:5px auto 5px auto;">Basic Information</h3>
            <table class="form_table">
                <tr>
                <th><label>Name : </label></th>
                <th><input class="form-control" id="name" type="text" value=<?php echo $_SESSION['username']?>></th>
                </tr>
                <tr>
                <th><label>Semester : </label></th>
                <th><select class="form-control" id="semester">
                    <option value="1">1st</option>
                    <option value="2">2nd</option>
                    <option value="3">3rd</option>
                    <option value="4">4th</option>
                    <option value="5">5th</option>
                    <option value="6">6th</option>
                    <option value="7">7th</option>
                    <option value="8">8th</option>
                </select></th>
                </tr>
               <tr>
                   <th><label>Branch</label></th>
                   <th><select class="form-control" id="branch">
                    <option value="CSE">CSE</option>
                    <option value="EC">EC</option>
                    <option value="MECH">MECH</option>
                    <option value="EE">EE</option>
                    <option value="IC">IC</option>
                    <option value="CHEM">CHEM</option>
                    <option value="CIVIL">CIVIL</option>
                    
                </select>
                       
                   </th>
               </tr>
               <tr>
                   <th><label>Registered On :&nbsp</label></th>
                   <th><label id="date_of_join" ></label></th>
               </tr>
               <tr>
                   <th><label>Last Visited :</label></th>
                   <th><label id="last_visit" ></label></th>
               </tr>
            </table>
        </div>
        <div class="card-contact">
            <hr>
        <h3 id="contact_title ">Contact Information</h3>
        <table class="form_table">
            <tr>
                <th><label>Email : </label>
                <th><label  id="email"></label></th>
            </tr>
            <tr>
                <th><label>Contact No. : </label></th>
                <th><input class="form-control" type="text" id="phone"></th>
            </tr>
        </table>
        </div>
        <div class="save-btn">
            <input id="save_changes" type="button" value="Save Changes">
        </div>
    </div>
    </div>
</body>

<script type="text/javascript">

    function setProfileData(response){
        document.getElementById("profileimg").setAttribute("src",response.PROFILE_IMG_PATH);
        document.getElementById("name").value=response.NAME;
        document.getElementById("welcome_name").textContent=response.NAME;
        document.getElementById("semester").value=response.SEMESTER;
        document.getElementById("branch").value=response.BRANCH;
        document.getElementById("email").textContent=response.USERID;
        document.getElementById("phone").value=response.PHONE;
        $('#date_of_join').text(response.DATE_OF_JOIN);
        $('#last_visit').text(response.LAST_VISIT);
    }

    $("#save_changes").click(function(){
            const name=document.getElementById("name").value;
            const semester=document.getElementById("semester").value;
            const branch=document.getElementById("branch").value;
            const phone=document.getElementById("phone").value;

        $.ajax({
            type:"POST",
            url:'functions.php',
            data:{action:'updateProfile',name:name,semester:semester,branch:branch,phone:phone},
            dataType:"JSON",
            success:function(response){
                alert("updated successfully");
            },
            error(response){
                console.log('error in updating profile');
                console.log(response.statusText);
            }

        });
    }); 

    $(document).ready(function(){
        console.log("loaded");
        $.ajax({
                type:'POST',
                url:'functions.php',
                data:{action:'getProfileData',username:'<?php echo $_SESSION['username'] ?>'},
                dataType:"json",
                success:function(response){
                    console.log(response);
                    console.log("successfull");
                    setProfileData(response);
                },
                error(response){
                    console.log("error ");
                    console.log(response);
                    console.log(response.statusText);
                }            
        });
    });

</script>
</html>