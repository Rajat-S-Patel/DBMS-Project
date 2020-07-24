<?php
    session_start();
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        header("location:login.php");
        exit;
    }
    require_once 'config.php';
    function getDetails($adid,$category){
        $details=array();
        global $c;
        $sql='select *from ad where adid=:adid';
        $sqlUpdateViews="UPDATE ITEM_STATS SET VIEWS = case when USERID =:userid then VIEWS else VIEWS+1 end where adid=:adid";
        $sqlSeeViews='SELECT VIEWS,LIKES FROM ITEM_STATS WHERE ADID=:adid';
        $sql2='SELECT *FROM '.$category.' WHERE ADID=:adid';
        $sqlImages='SELECT IMAGE FROM AD_IMAGES WHERE ADID=:adid';
        $sqlUser='SELECT NAME,PROFILE_IMG_PATH,PHONE,DATE_OF_JOIN,COLLEGE FROM USERS WHERE USERID=:userid';
        $conn=oci_parse($c,$sql);
        $conn2=oci_parse($c,$sql2);
        $conn3=oci_parse($c,$sqlImages);
        $conn4=oci_parse($c,$sqlUser);
        $s_update_views=oci_parse($c,$sqlUpdateViews);
        $s_see_views=oci_parse($c,$sqlSeeViews);
        if (!$conn) {
            $m = oci_error($c);
            trigger_error('Could not parse statement: '. $m['message'], E_USER_ERROR);
        }
        oci_bind_by_name($conn,':adid',$adid);
        oci_bind_by_name($conn2,':adid',$adid);
        oci_bind_by_name($conn3,':adid',$adid);
        oci_bind_by_name($s_update_views,':userid',$_SESSION["username"]);
        oci_bind_by_name($s_update_views,':adid',$adid);
        oci_bind_by_name($s_see_views,':adid',$adid);
        $r_update_views=oci_execute($s_update_views);
        
        $r = oci_execute($conn);
        $r2=oci_execute($conn2);
        $r3=oci_execute($conn3);
        
        $r_see_views=oci_execute($s_see_views);
        
        if (!$r) {
            $m = oci_error($conn);
            trigger_error('Could not execute statement: '. $m['message'], E_USER_ERROR);
        }
        $userid="";
        
        $row=oci_fetch_array($s_see_views,OCI_ASSOC);
        $details["STATS"]=$row;
        while($row=oci_fetch_array($conn,OCI_ASSOC)){
            $details["INFO"]=$row;
            $userid=$row["USERID"];            
        }
        while ($row=oci_fetch_array($conn2,OCI_ASSOC)) {
            $details["SPECS"]=$row;
        }
       
        oci_bind_by_name($conn4,':userid',$userid);
        $r4=oci_execute($conn4);

        $i=0;
        $images=array();
        oci_fetch_all($conn3,$images,null, null,OCI_FETCHSTATEMENT_BY_ROW+OCI_NUM);
        $details["images"]=$images;
        
        $row=oci_fetch_array($conn4,OCI_ASSOC);
        $details["SELLER"]=$row;
    
        echo json_encode($details);
    }

    if(isset($_POST["adid"])){
        //echo "category = ".$_POST["category"];
        getDetails($_POST["adid"],$_POST["category"]);
    }   
?>