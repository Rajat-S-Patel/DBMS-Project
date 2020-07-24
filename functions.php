<?php
require_once 'config.php';
require 'data_test.php';
//session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location:login.php");
    exit;
}



function addToFavourite($adid)
{
    global $c;
    $sql = 'INSERT INTO FAVOURITE VALUES(:userid,:adid)';
    $sqlIncLike = 'UPDATE ITEM_STATS SET LIKES = LIKES+1 where adid=:adid';
    $s = oci_parse($c, $sql);
    $s_inc_likes = oci_parse($c, $sqlIncLike);
    oci_bind_by_name($s, ':userid', $_SESSION["username"]);
    oci_bind_by_name($s, ':adid', $adid);
    oci_bind_by_name($s_inc_likes, ':adid', $adid);

    $r = oci_execute($s);
    $r_inc_like = oci_execute($s_inc_likes);
    if ($r && $r_inc_like) {
        echo true;
    } else
        echo false;
}

function removeFavourite($adid)
{
    global $c;
    $sql = 'DELETE FROM FAVOURITE WHERE ADID=:adid and USERID=:userid';
    $sqlDecLikes = 'UPDATE ITEM_STATS SET LIKES=LIKES-1 WHERE ADID=:adid';
    $s = oci_parse($c, $sql);
    $s_dec_likes = oci_parse($c, $sqlDecLikes);
    oci_bind_by_name($s, ':adid', $adid);
    oci_bind_by_name($s, ':adid', $adid);
    oci_bind_by_name($s_dec_likes, ':adid', $adid);
    oci_bind_by_name($s, ':userid', $_SESSION['username']);
    $r = oci_execute($s);
    $r_dec_like = oci_execute($s_dec_likes);
    if ($r && $r_dec_like)
        echo true;
    else
        echo false;
}

function getPublication($option)
{
    global $c;
    if ($option == 1)
        $sql = "select *from choices where category='BOOKS' order by name asc";
    else if ($option == 2)
        $sql = "select *from choices where category='LAPTOP' order by name asc";
    else if ($option == 3)
        $sql = "select *from choices where category='AREA' order by name asc";
    $s = oci_parse($c, $sql);

    if (!$s) {
        $m = oci_error($c);
        trigger_error('Could not parse statement: ' . $m['message'], E_USER_ERROR);
    }

    $r = oci_execute($s);
    if (!$r) {
        $m = oci_error($s);
        trigger_error('Could not execute statement: ' . $m['message'], E_USER_ERROR);
    }
    $name = array();
    while ($row = oci_fetch_array($s, OCI_BOTH)) {
        $name[] = $row["NAME"];
    }
    echo json_encode($name);
}

function getAdData($adid, $category)
{
    global $c;
    $sql = 'SELECT *FROM (SELECT *FROM AD NATURAL JOIN ' . $category . ') WHERE ADID=:adid';
    $sql2 = 'SELECT IMAGE,SEQ_NUM FROM AD_IMAGES WHERE ADID=:adid ORDER BY SEQ_NUM ASC';
    //$sql3='INSERT INTO TEMP (SELECT *FROM AD_IMAGES WHERE ADID=:adid)';
    // $sql3='SELECT *FROM AD_IMAGES WHERE ADID=:adid';
    $s = oci_parse($c, $sql);
    $s2 = oci_parse($c, $sql2);
    // $s3=oci_parse($c,$sql3);
    //oci_bind_by_name($s,':userid',$_SESSION['username']);
    oci_bind_by_name($s, ':adid', $adid);
    oci_bind_by_name($s2, ':adid', $adid);
    // oci_bind_by_name($s3,':adid',$adid);
    oci_execute($s);
    oci_execute($s2);
    // oci_execute($s3);
    $result = array();
    while ($row = oci_fetch_array($s, OCI_ASSOC)) {
        $result = $row;
    }

    oci_fetch_all($s2, $row);
    $_SESSION['images'] = array();
    $result['images'] = $row['IMAGE'];
    $i = 0;

    foreach ($result['images'] as $key => $value) {
        $_SESSION['images'][(int) $row['SEQ_NUM'][$i]] = $row['IMAGE'][$i];
        $i++;
    }
    $_SESSION["total_images"] = count($row['IMAGE']);
    // echo json_encode($_SESSION['images']);
    echo json_encode($result);
}


function updateProfile($username, $name, $semester, $branch, $phone)
{
    global $c;
    $sql = 'update users set NAME=:name,SEMESTER=:semester,BRANCH=:branch,PHONE=:phone where USERID=:username';

    $s = oci_parse($c, $sql);

    if (!$s) {
        $m = oci_error($c);
        trigger_error('Could not parse statement: ' . $m['message'], E_USER_ERROR);
    }

    $ba = array(':username' => $username, ':name' => $name, ':semester' => $semester, ':branch' => $branch, ':phone' => $phone);

    foreach ($ba as $key => $value) {
        oci_bind_by_name($s, $key, $ba[$key]);
    }

    $r = oci_execute($s);
    if (!$r) {
        $m = oci_error($s);
        trigger_error('Could not execute statement: ' . $m['message'], E_USER_ERROR);
    } else {
        $response = array(["status" => "success"]);
        echo json_encode($response);
    }
}

function convertForIn($arr)
{
    $ans = "";
    for ($i = 0; $i < count($arr); $i++) {
        if ($i == 0)
            $ans=$ans."'".$arr[$i]."'";
        else
            $ans =$ans. "," . "'" . $arr[$i] . "'";
    }
    return $ans;
}

function filter($criteria, ...$params)
{
    global $c;
    $type = "";
    $f_spec = "";


    if (isset($_POST['options'])) {
        $arr = json_decode($_POST['options']);
        $ans = convertForIn($arr);
        $f_spec = " NATURAL JOIN " . $_POST['for'];
        $type = " AND " . $_POST['f_specs'] . " IN (" . $ans . ")";
    }
    // echo $criteria." ".$params[0]." ".$params[1];
    if (count($params) != 0 && $params[0] == 'search') {

        $type = $type . " AND TITLE = '" . $params[1] . "'";
        if (isset($params[2])) {
            echo json_encode($params[2]);
            exit;
        }
        $type = $type . " AND CATEGORY = '" . $params[2] . "'";
    }

    switch ($criteria) {
        case "time asc":
            $sqlfilter = 'SELECT *FROM AD ' . $f_spec . ' WHERE PRICE BETWEEN :MIN_PRICE and :MAX_PRICE' . $type . ' ORDER BY DATE_OF_POST ASC';
            break;
        case "time desc":
            $sqlfilter = 'SELECT *FROM AD ' . $f_spec . ' WHERE PRICE BETWEEN :MIN_PRICE and :MAX_PRICE ' . $type . ' ORDER BY DATE_OF_POST DESC';
            break;
        case "price desc":
            $sqlfilter = 'SELECT *FROM AD ' . $f_spec . ' WHERE PRICE BETWEEN :MIN_PRICE and :MAX_PRICE ' . $type . ' ORDER BY PRICE DESC';
            break;
        case "price asc":
            $sqlfilter = 'SELECT *FROM AD ' . $f_spec . ' WHERE PRICE BETWEEN :MIN_PRICE and :MAX_PRICE ' . $type . ' ORDER BY PRICE ASC';
            break;
        case "views asc":
            $sqlfilter = 'SELECT *FROM AD ' . $f_spec . ' NATURAL JOIN ITEM_STATS ' . $type . ' ORDER BY ITEM_STATS.VIEWS ASC';
            break;
        case "views asc":
            $sqlfilter = 'SELECT *FROM AD ' . $f_spec . ' NATURAL JOIN ITEM_STATS ' . $type . ' ORDER BY ITEM_STATS.VIEWS DESC';
            break;
        case "likes asc":
            $sqlfilter = 'SELECT *FROM AD ' . $f_spec . ' NATURAL JOIN ITEM_STATS ' . $type . ' ORDER BY ITEM_STATS.LIKES ASC';
            break;
        case "likes desc":
            $sqlfilter = 'SELECT *FROM AD ' . $f_spec . ' NATURAL JOIN ITEM_STATS ' . $type . ' ORDER BY ITEM_STATS.LIKES DESC';
            break;
    }
    // echo $sqlfilter;
    $s_filter = oci_parse($c, $sqlfilter);
    if ($criteria == 'price asc' || $criteria == 'price desc' || $criteria == 'time desc' || $criteria == 'time asc') {
        oci_bind_by_name($s_filter, ':MIN_PRICE', $_POST['MIN_PRICE']);
        oci_bind_by_name($s_filter, ':MAX_PRICE', $_POST['MAX_PRICE']);
    }
    
    //oci_bind_by_name($s_filter,':CRITERIA',$_POST['criteria']);


    $r_filter = oci_execute($s_filter);
    $product = array();

    while ($row = oci_fetch_array($s_filter, OCI_ASSOC)) {

        $row['isfavourite'] = isFavourite($row['ADID']);
        $product[] = $row;
    }

    echo json_encode($product);
}

function deleteAd($adid){
    global $c;
    $sqlremoveimage='SELECT IMAGE FROM AD_IMAGES WHERE ADID=:adid';
    $s_rm_img=oci_parse($c,$sqlremoveimage);
    oci_bind_by_name($s_rm_img,':adid',$adid);
    $r_rm_img=oci_execute($s_rm_img);
    while($row=oci_fetch_array($s_rm_img,OCI_ASSOC)){
        if(file_exists($row['IMAGE'])){
            unlink($row['IMAGE']);
        }
    }
    if($r_rm_img){
        $sqldelete='DELETE FROM AD WHERE ADID= :adid';
        $s_delete=oci_parse($c,$sqldelete);
        oci_bind_by_name($s_delete,':adid',$adid);
        $r_delete=oci_execute($s_delete);
        if($r_delete)
            echo 1;
        else echo 0;
    }
    else
        echo 0;
}

function getProfileData($username)
{
    global $c;
    $sql = 'select *from users where userid= :username';
    $s = oci_parse($c, $sql);
    if (!$s) {
        $m = oci_error($c);
        trigger_error('Could not parse statement: ' . $m['message'], E_USER_ERROR);
    }
    oci_bind_by_name($s, ':username', $username);
    $r = oci_execute($s);
    if (!$r) {
        $m = oci_error($s);
        trigger_error('Could not execute statement: ' . $m['message'], E_USER_ERROR);
    }
    $row = oci_fetch_array($s, OCI_BOTH);
    //echo "row = ".$row;
    if (!$row) {
        echo "Error, No Data Found";
    } else {
        /*$profile["name"]=$row["NAME"];
                $profile["username"]=$row["USERID"];
                $profile["sem"]=$row["SEMESTER"];
                $profile["branch"]=$row["BRANCH"];
                $profile["profile_img"]=$row["PROFILE_IMG_PATH"];
                $profile["phone"]=$row["PHONE"];
                $profile["date_of_join"]=$row["DATE_OF_JOIN"];
                $profile["last_visit"]=$row['LAS']*/
        echo json_encode($row);
    }
}


if (isset($_POST['action'])) {
    $action = $_POST['action'];
    if ($action == 'getProfileData' && isset($_POST['username'])) {
        getProfileData($_POST['username']);
    } else if ($action == 'updateProfile') {
        # code...
        $username = $_SESSION['username'];
        $name = $_POST["name"];
        $semester = $_POST["semester"];
        $branch = $_POST["branch"];
        $phone = $_POST["phone"];
        updateProfile($username, $name, $semester, $branch, $phone);
    } else if ($action == "removeFavourite") {
        removeFavourite($_POST["adid"]);
    } else if ($action == "getPublication") {

        getPublication($_POST["option"]);
    } else if ($action == 'getAdData') {
        getAdData($_POST['adid'], $_POST['category']);
        //echo 1;
    } else if ($action == 'removeAdImage') {
        removeAdImage($_POST['path']);
    } else if ($action == 'addToFavourite') {
        addToFavourite($_POST['adid']);
    }
    else if($action=='deleteAd'){
        deleteAd($_POST['adid']);
    } 
    else if ($action == 'filter-all') {
        filter($_POST['criteria']);
    } else if ($action == 'filter-search') {
        if (isset($_POST['category']))
            filter($_POST['criteria'], 'search', $_POST['title'], $_POST['category']);
        else
            filter($_POST['criteria'], 'search', $_POST['title']);
    } else {
        echo json_encode(array(["fail" => "failed"]));
    }
}
    // filter($_POST['criteria']);
