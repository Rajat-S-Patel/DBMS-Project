<?php

require_once 'config.php';
session_start();
$request = $_POST['request'];

function postAdData()
{
    global $c;
    $userid = $_SESSION['username'];
    $adid = $_POST['adid'];

    if ($_POST['request'] == 4) {
        $sql_delete = 'DELETE FROM AD_IMAGES WHERE ADID=:adid';
        $s_delete = oci_parse($c, $sql_delete);
        oci_bind_by_name($s_delete, ':adid', $adid);
        oci_execute($s_delete,OCI_NO_AUTO_COMMIT);
    }
    
    $cover_img = array_values($_SESSION["images"])[0];
    // echo json_encode($cover_img);
    

    if ($_POST['request'] == 3) {
        // $cover_img = array_values($_SESSION["images"])[0];

        $sql = "insert into ad values(:adid,:title,:price,:cover_img,:description,sysdate,:category,:userid)";
        // $sql='insert into books values(:adid,:title,:price,:cover_img,:description,:author,:publication)';

        if ($_POST['category'] == 'BOOKS')
            $sql2 = 'insert into books values(:adid,:author,:publication)';
        else if ($_POST['category'] == 'LAPTOP')
            $sql2 = 'INSERT INTO LAPTOP VALUES(:adid,:brand,:model)';
        else if ($_POST['category'] == 'PG_HOSTEL')
            $sql2 = 'INSERT INTO PG_HOSTEL VALUES(:adid,:area,:roommates)';
        else if($_POST['category']=='OTHER')
            $sql2='INSERT INTO OTHER VALUES(:adid,:info)';
    } else if ($_POST['request'] == 4) {
        $sql = 'UPDATE AD SET TITLE=:title,PRICE=:price,COVER_IMG=:cover_img,DESCRIPTION=:description WHERE ADID=:adid';
        if ($_POST['category'] == 'BOOKS')
            $sql2 = 'UPDATE BOOKS SET AUTHOR=:author,PUBLICATION=:publication WHERE ADID=:adid';
        else if ($_POST['category'] == 'LAPTOP')
            $sql2 = 'UPDATE LAPTOP SET BRAND=:brand,MODEL_NAME=:model WHERE ADID:adid';
        else if ($_POST['category'] == 'PG_HOSTEL')
            $sql2 = 'UPDATE PG_HOSTEL SET AREA=:area,ROOMMATES=:roommates WHERE ADID=:adid';
        else if($_POST['category']=='OTHER')
            $sql2='UPDATE OTHER SET INFO=:info WHERE ADID=:adid';
    }
    $s = oci_parse($c, $sql);
    $s2 = oci_parse($c, $sql2);


    if ($_POST['request'] == 3) {
        oci_bind_by_name($s, ':category', $_POST['category']);
        oci_bind_by_name($s, ':userid', $userid);
    }


    // $cover_img='123';
    oci_bind_by_name($s, ':adid', $adid);
    oci_bind_by_name($s, ':title', $_POST['title']);
    oci_bind_by_name($s, ':price', $_POST['price']);
    oci_bind_by_name($s, ':cover_img', $cover_img);
    oci_bind_by_name($s, ':description', $_POST['description']);
    oci_bind_by_name($s2, ':adid', $adid);


    if ($_POST['category'] == 'BOOKS') {
        oci_bind_by_name($s2, ':author', $_POST['author']);
        oci_bind_by_name($s2, ':publication', $_POST['publication']);
    } else if ($_POST['category'] == 'LAPTOP') {
        oci_bind_by_name($s2, ':brand', $_POST['brand']);
        oci_bind_by_name($s2, ':model', $_POST['model']);
    } else if ($_POST['category'] == 'PG_HOSTEL') {
        oci_bind_by_name($s2, ':area', $_POST['area']);
        oci_bind_by_name($s2, ':roommates', $_POST['roommates']);
    }
    else if($_POST['category']=='OTHER')
        oci_bind_by_name($s2,':info',$_POST['info']);

    $r = oci_execute($s,OCI_NO_AUTO_COMMIT);
    $r2 = oci_execute($s2,OCI_NO_AUTO_COMMIT);

    foreach ($_SESSION['images'] as $key => $value) {
        $sqlimage = 'INSERT INTO AD_IMAGES VALUES(:adid,:image,:seq)';
        $s_image = oci_parse($c, $sqlimage);
        oci_bind_by_name($s_image, ':adid', $adid);
        oci_bind_by_name($s_image, ':image', $value);
        oci_bind_by_name($s_image, ':seq', $key);
        oci_execute($s_image,OCI_NO_AUTO_COMMIT);
        // echo true;
    }

    if ($_POST['request'] == 3) {
        $sqlAdStats = "INSERT INTO ITEM_STATS(ADID,USERID) VALUES(:adid,:userid)";
        $s_stats = oci_parse($c, $sqlAdStats);
        oci_bind_by_name($s_stats, ':adid', $adid);
        oci_bind_by_name($s_stats, ':userid', $userid);
        oci_execute($s_stats,OCI_NO_AUTO_COMMIT);
    }
    oci_commit($c);
    unset($_SESSION["images"]);
    echo 1;
}

if ($_POST['request'] == 3 || $_POST['request'] == 4)
    postAdData();

#session_start();
function storeInDatabase($seq, $path, $adid)
{

    global $c;
    if (!isset($_SESSION['images']))
        $_SESSION['images'] = array();

    $_SESSION['images'][$seq] = $path;

    return true;
}
function removeAdImage($path)
{      // this is used while editing present ad

    global $c;
    $sql = 'DELETE FROM TEMP WHERE IMAGE=:image';
    $s = oci_parse($c, $sql);
    oci_bind_by_name($s, ':image', $path);
    $r = oci_execute($s, OCI_COMMIT_ON_SUCCESS);
    if ($r)
        echo true;
    else
        echo false;
}
function deleteImage($path)
{        // Used to delete image during first time post
    global $c;

    foreach ($_SESSION['images'] as $key => $value) {
        if ($path == $value) {
            
            unset($_SESSION['images'][$key]);
        }
    }

    $_SESSION["total_images"] -= 1;
    return true;
}


if ($request == 1) {
    $countFiles = count($_FILES['files']['name']);
    $category = "mobile";
    $upload_location = "product_img/";

    $files_arr = array();
    $username = $_SESSION['username'];
    
    $adid = $_POST['adid'];
    $seq=$_SESSION['total_images'];
    for ($i = 1; $i <= $countFiles; $i++) {
        $filename = $_FILES['files']['name'][$i - 1];

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $extensions = array("jpg", "jpeg", "png", "webp");

        if (in_array($ext, $extensions)) {
            $newname = uniqid() . "." . $ext;
            $path = $upload_location . $newname;
            if (move_uploaded_file($_FILES['files']['tmp_name'][$i - 1], $path)) {
                $seq++;
                // $seq = $i + $_SESSION["total_images"];
                $success = storeInDatabase($seq, $path, $adid);
                if ($success == true) {
                    $files_arr[] = $path;
                } else
                    echo false;
            }
        }
    }
    $_SESSION["total_images"] = $seq;
    echo json_encode($files_arr);
} else if ($request == 2) {
    $path = $_POST['location'];
    $return_text = 0;

    if (file_exists($path)) {
        unlink($path);
        if (deleteImage($path)) {
            $return_text = 1;
        } else {
            $return_text = 0;
        }
    } else {
        $return_text = 0;
    }
    echo $return_text;
} else if ($request == 'removeAdImage') {
    removeAdImage($_POST['path']);
}
