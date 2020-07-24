<?php
    require_once 'config.php';
    $query=$_POST['data'];
    $category=$_POST['category'];
    if($category=='ALL')
    $sql='SELECT distinct TITLE FROM AD WHERE LOWER(TITLE) LIKE '."'%".$query."%'";
    else
    $sql="SELECT DISTINCT TITLE FROM AD WHERE LOWER(TITLE) LIKE '%".$query."%' AND CATEGORY = '".$category."'";
    $s=oci_parse($c,$sql);
    oci_execute($s);
    $ans=array();
    while($row=oci_fetch_array($s,OCI_ASSOC))
        $ans[]=$row;
    
    echo json_encode($ans);
?>
