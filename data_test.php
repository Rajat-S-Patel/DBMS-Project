<?php
require_once 'config.php';
session_start();

function getFavourite($userid,$action){
    global $c;
    if($action=='favourite')
    $sql='SELECT *FROM AD INNER JOIN FAVOURITE ON FAVOURITE.ADID=AD.ADID AND FAVOURITE.USERID=:userid';
    else if($action=='myads')
    $sql='SELECT *FROM AD WHERE USERID = :userid';
    else if($action=='searchTitle'){
        $title=$_POST['data'];
        if ($_POST['category']=='ALL')
        $sql="SELECT *FROM AD WHERE TITLE LIKE :title";
        else
        $sql="SELECT *FROM AD WHERE TITLE LIKE :title AND CATEGORY = :category";
    }
    $s=oci_parse($c,$sql);
    if($action=='searchTitle'){
        $title='%'.$title.'%';
        oci_bind_by_name($s,':title',$title);
        if($_POST['category']!='ALL')
        oci_bind_by_name($s,':category',$_POST['category']);
    }
        
    else
        oci_bind_by_name($s,':userid',$userid);
    $r=oci_execute($s);
    if($r){
        
        $i = 0;

        $products=array();
        while (($row = oci_fetch_array($s, OCI_ASSOC)) != false) {

            $temp=$row;
            $temp['isfavourite']=isFavourite($row["ADID"]);
            $products[]=$temp;
            $i++;
        }
        echo json_encode($products);
    }
}

function isFavourite($adid)
{
    global $c;
    $sql = 'SELECT *FROM FAVOURITE WHERE ADID=:adid AND USERID=:userid';
    $s = oci_parse($c, $sql);
    if ($s) {  // echo "username = ".$_SESSION['username'];
        oci_bind_by_name($s, ':userid', $_SESSION['username']);
        oci_bind_by_name($s, ':adid', $adid);
        $r = oci_execute($s);
        if ($r) {
            $row = oci_fetch_array($s);
            //echo "row = ".($row);
            if ($row)
                return true;
            else
                return false;
        }
    }
}

if (isset($_POST["action"])) {
    $action = $_POST['action'];
    if ($action == 'HOME'||$action=='BOOKS'||$action=='LAPTOP'||$action=='PG_HOSTEL'||$action=='OTHER') {
        
        switch ($action) {
            case 'HOME':
                $sql = "SELECT *FROM Ad ORDER BY DATE_OF_POST DESC";
                break;
            default:
                $sql="SELECT *FROM AD WHERE CATEGORY = '".$action."' ORDER BY DATE_OF_POST DESC";
                break;
        }
        if($action=='HOME')
        $sqlMinMax='SELECT MIN(PRICE) MIN_PRICE,MAX(PRICE) MAX_PRICE FROM AD';
        else
        $sqlMinMax="SELECT MIN(PRICE) MIN_PRICE,MAX(PRICE) MAX_PRICE FROM (SELECT PRICE FROM AD WHERE CATEGORY = '".$action."')";
        $s = oci_parse($c, $sql);
        $s_min_max=oci_parse($c,$sqlMinMax);
        if (!$s) {
            $m = oci_error($c);
            trigger_error('Could not parse statement: ' . $m['message'], E_USER_ERROR);
        }
        $r = oci_execute($s);
        $r_min_max=oci_execute($s_min_max);
        if (!$r) {
            $m = oci_error($s);
            trigger_error('Could not execute statement: ' . $m['message'], E_USER_ERROR);
        }

        $i = 0;

        $products=array();
        $temp2=array();
        while (($row = oci_fetch_array($s, OCI_ASSOC)) != false) {

            $temp=$row;
            $temp['isfavourite']=isFavourite($row["ADID"]);
            $temp2[]=$temp;
        }
        $row=oci_fetch_array($s_min_max,OCI_ASSOC);
        $products['PRICE_DATA']=$row;
        $products['PRODUCTS']=$temp2;
        echo json_encode($products);
    }
    else if($action=='favourite'||$action=='myads'||$action=='searchTitle'){
        //echo json_encode($_SESSION['username']);
        getFavourite($_SESSION["username"],$action);
    }
    
}
