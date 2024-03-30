<?php
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if(!$isAjax) {die("Not Allowed");}

require ("dynamicCNF.php");

$building = mysqli_real_escape_string($userdb, $_POST["building"]);

if (!$nexts = $userdb->query("SELECT * FROM `cells_backup` GROUP BY `building` ORDER BY `building` DESC LIMIT 1")){die(mysqli_error($userdb));}
else {$next = mysqli_fetch_assoc($nexts);}
$backup = $next["building"]+1;

$cells = $userdb->query("SELECT * FROM `cells` WHERE `building`='$building' ");
while ($cell = mysqli_fetch_assoc($cells)){
    if (!$insert = $userdb->query("INSERT INTO cells_backup (`building`,`cellX`,`cellZ`,`cellY`,`celltype`) "
            . "VALUES ('$backup','".$cell["cellX"]."','".$cell["cellZ"]."','".$cell["cellY"]."','".$cell["celltype"]."') ")){die(mysqli_error($userdb));}
}
echo "success";