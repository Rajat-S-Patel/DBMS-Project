<?php

    session_start();
    if(!isset($_SESSION["loggedin"])||$_SESSION["loggedin"]!==true){
        header("location:login.php");
        exit;
    }

    require_once "config.php";
    require 'upload_function.php';

    $target_dir = "user_img/";
    $name=$_FILES["fileToUpload"]["name"];
    echo "name ==  ".$name;
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $sql= "update users set profile_img_path = :path where userid=:username";
    uploadImage($target_file,$target_dir,$sql);
   
?>