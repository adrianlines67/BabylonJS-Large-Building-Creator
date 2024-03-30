<?php
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if(!$isAjax) {die("Not Allowed");}

require ("dynamicCNF.php");

$building = mysqli_real_escape_string($userdb, $_POST["building"]);
$style    = mysqli_real_escape_string($userdb, $_POST["style"]);

if ($style==1){
if (!$delete = $userdb->query("DELETE FROM `cells` WHERE `building`='$building' ")){die(mysqli_error($userdb));}
else {echo "success";}
}
else if ($style==2){
    if (!$cells = $userdb->query("SELECT * FROM `cells` WHERE `building`='$building' ")){die(mysqli_error($userdb));}
    while ($cell = mysqli_fetch_array($cells)){
        $cellsID = $cell["cellsID"];
        if (!$update = $userdb->query("UPDATE `cells` SET `celltype`='00' WHERE `cellsID`='$cellsID' ")){die(mysqli_error($userdb));}
    }
    echo "success";
}
else {echo"Oops";}