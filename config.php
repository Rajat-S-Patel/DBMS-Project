<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');
 
$dbaname = "system";                  // Use your username
$dbapassword = "Rajat2042000";             // and your password
$database = "orcl";   // and the connect string to connect to your database

//$query = "select password from users where username = '$name'";


 
$c = oci_connect($dbaname, $dbapassword, $database);
if (!$c) {
    $m = oci_error();
    trigger_error('Could not connect to database: '. $m['message'], E_USER_ERROR);
}
 
/*$s = oci_parse($c, $query);
if (!$s) {
    $m = oci_error($c);
    trigger_error('Could not parse statement: '. $m['message'], E_USER_ERROR);
}   
$r = oci_execute($s);
if (!$r) {
    $m = oci_error($s);
    trigger_error('Could not execute statement: '. $m['message'], E_USER_ERROR);
}
while (($row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
    echo "<tr>\n";
    foreach ($row as $item) {
        echo "<td>";
        $item= $item!==null?htmlspecialchars($item, ENT_QUOTES|ENT_SUBSTITUTE):"&nbsp;";
        echo "</td>\n";
        if($item==$pass)
            echo "login successfully";
        else
            echo "Access denied";
    }
    echo "</tr>\n";
}
*/
?>