<?php
    require_once 'postAd.php';
    require_once 'config.php';
    #session_start();
    function storeInDatabase($seq,$path,$adid){
    
        global $c;
        //$sql = "insert into temp values(:username,:seq,:path,:adid)";
        $sql='INSERT INTO AD_IMAGES VALUES(:adid,:path,:seq)';
        $s=oci_parse($c,$sql);
        if(!$s){
            $m = oci_error($c);
            trigger_error('Could not parse statement: '. $m['message'], E_USER_ERROR);
        }
        
        oci_bind_by_name($s,':seq',$seq);
        oci_bind_by_name($s,':path',$path);
        oci_bind_by_name($s,':adid',$adid);
        $r = oci_execute($s);
        if (!$r) {
            $m = oci_error($s);
            trigger_error('Could not execute statement: '. $m['message'], E_USER_ERROR);
            return false;
        }
        else{
            return true;
        }
    }

    function deleteImage($path){
        global $c;
        #$sql = "delete from temp where path = :path";
        $sql='DELETE FROM AD_IMAGES WHERE IMAGE=:path';
        $s=oci_parse($c,$sql);
        if(!$s){
            $m = oci_error($c);
            trigger_error('Could not parse statement: '. $m['message'], E_USER_ERROR);
        }
        
        oci_bind_by_name($s,':path',$path);
        $r = oci_execute($s);
        if (!$r) {
            $m = oci_error($s);
            trigger_error('Could not execute statement: '. $m['message'], E_USER_ERROR);
            return false;
        }
        else{
            $_SESSION["total_images"]-=1;
            return true;
        }
    }
    $request=$_POST['request'];

    if($request == 1){  
      $countFiles=count($_FILES['files']['name']);
      $upload_location="product_img/";
      
      $files_arr=array();
      $username=$_SESSION['username'];
      $category="mobile";
      $adid=$_POST['adid'];
      for($i=1;$i<=$countFiles;$i++){
          $filename=$_FILES['files']['name'][$i-1];

          $ext=strtolower(pathinfo($filename,PATHINFO_EXTENSION));

          $extensions=array("jpg","jpeg","png","webp");

          if(in_array($ext,$extensions)){
              $newname=uniqid().".".$ext;
              $path=$upload_location.$newname;
              if(move_uploaded_file($_FILES['files']['tmp_name'][$i-1],$path)){
                  $seq=$i+$_SESSION["total_images"];
                  $success=storeInDatabase($seq,$path,$adid);
                  if($success==true){
                    $files_arr[]=$path;
                  }
                  else
                    echo false;
              }
          }
      }
      $_SESSION["total_images"]=$seq;
      echo json_encode($files_arr);
    }
    else if($request==2){
        $path=$_POST['location'];
        $return_text=0;

        if(file_exists($path)){
            unlink($path);
            if(deleteImage($path)){
                $return_text=1;
            }
            else{
                $return_text=0;
            }
            
        }
        else{
            $return_text=0;
        }
        echo $return_text;
    }

    
?>