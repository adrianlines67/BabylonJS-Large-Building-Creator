<?php
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if(!$isAjax) {die("Not Allowed");}

require ("dynamicCNF.php");
$x = mysqli_real_escape_string($userdb, $_POST["cellX"]);
$z = mysqli_real_escape_string($userdb, $_POST["cellZ"]);
$y = mysqli_real_escape_string($userdb, $_POST["cellY"]);

$building = mysqli_real_escape_string($userdb, $_POST["building"]);
$celltype = mysqli_real_escape_string($userdb, $_POST["celltype"]);

$error = "none";
$status = "complete";
    
if (!$cells = $userdb->query("SELECT * FROM `cells` "
        . "WHERE `building`='$building' "
        . "AND `cellY`='$y' AND `cellZ`='$z' AND `cellX`='$x' ")){$error.=mysqli_error($userdb);}
if (!mysqli_num_rows($cells)){
    if (!$insert = $userdb->query("INSERT INTO `cells` (`building`,`cellX`,`cellZ`,`cellY`,`celltype`) "
            . "VALUES ('$building','$x','$z','$y','$celltype') ")){$error.=mysqli_error($userdb);}
} else {
    $cell = mysqli_fetch_assoc($cells);
    $cellsID = $cell["cellsID"];
    if (!$update = $userdb->query("UPDATE `cells` SET `celltype`='$celltype' WHERE `cellsID`='$cellsID' ")){$error.=mysqli_error($userdb);}
} 

$result ["x"] = $x;
$result ["z"] = $z;
$result ["y"] = $y;

$result ["type"] = $celltype;

$result["error"] = $error;
$result["result"] = $status;

echo json_encode($result);