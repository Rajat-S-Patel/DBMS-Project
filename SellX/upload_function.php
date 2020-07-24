<?php
    
    if(!isset($_SESSION["loggedin"])||$_SESSION["loggedin"]!==true){
        header("location:login.php");
        exit;
    }
    //retrieveProfilePic();
    require_once "config.php";


    $target_dir="";
    $name="";
    $imageFileType="";
    $extension_arr=array("jpg","png","jpeg");
    $uploadOk=false;
    
    function uploadImage($filename,$dir,$sql){
        global $target_dir,$name,$imageFileType;
        $name=$filename;
        $target_dir=$dir;
        $imageFileType=strtolower(pathinfo($name,PATHINFO_EXTENSION));
        saveimage($sql);
    }
    function saveimage($sql){
        global $target_dir,$name,$imageFileType,$extension_arr,$uploadOk;
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if($check !== false&&in_array($imageFileType,$extension_arr)){
            echo "File is an image - " . $check["mime"] . ".";
            echo "name = ".$name;
            $uploadOk = true;          
        }
        else {
            echo "File is not an image.";
            $uploadOk = false;
        }

        if($uploadOk){
            echo '<br>'."file temp name = ".$_FILES["fileToUpload"]["tmp_name"];
            $RandomAccountNumber = uniqid();
            $name=$RandomAccountNumber.".".$imageFileType;
            echo "<br>"."file name = ".$name;
            $target_file=$target_dir.$name;
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                insert_image($_SESSION["username"],$target_file,$sql);
                echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
        
    }
    
    function insert_image($username,$path,$sql){
        global $c;
        
        $s = oci_parse($c, $sql);

        if(!$s){
            $m=oci_error($c);
            trigger_error('Could not parse statement: '. $m['message'], E_USER_ERROR);

        }
        oci_bind_by_name($s,':username',$username);
        oci_bind_by_name($s,':path',$path);
        $r=oci_execute($s);
        if(!$r){
            $m=oci_error($s);
            trigger_error('Could not execute statement: '. $m['message'], E_USER_ERROR); 
        }
        else{
            echo "<br>"."Data entered Successfully";  
            deleteImage($_SESSION['src']);
        }
    }
    function retrieveProfilePic(){
        $username=$_SESSION['username'];
        
        global $c;
       // echo "hello world";
        $sql="select *from users where userid=:username";
        $s = oci_parse($c, $sql);

        if(!$s){
            $m=oci_error($c);
            trigger_error('Could not parse statement: '. $m['message'], E_USER_ERROR);

        }
        oci_bind_by_name($s,':username',$username);
        //oci_bind_by_name($s,':path',$path);
        $r=oci_execute($s);
        if(!$r){
            $m=oci_error($s);
            trigger_error('Could not execute statement: '. $m['message'], E_USER_ERROR); 
        }
        $row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS);
        //echo "row = ".$row;
        if(!$row){
            echo "Error, No Data Found";
        }
        else{

            $img_src=$row["PROFILE_IMG_PATH"];
           //echo "user = ".$username;
            //echo "$img_src";
            
            $_SESSION["src"]=$img_src;
        }
        
    }
    function deleteImage($filepath){
        
        if (file_exists($filepath)&&basename($filepath)!="account-icon.png") {
            unlink($filepath);
        }
    }




?>
