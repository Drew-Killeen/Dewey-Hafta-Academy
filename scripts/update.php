<?php
    require '../templates/header.php';

    if($_GET['usr'] && $_GET['sass']) 
    {
        $userInfo = mysql_fetch_assoc(mysql_query("SELECT sass FROM dewey_members WHERE id='".$_GET['usr']."'"));
        if($userInfo['sass'] == 0) {$sass = 1; echo "sass1";}
        else {$sass = 0; echo "sass0";}
        mysql_query("UPDATE dewey_members SET sass='".$sass."' WHERE id='".$_GET['usr']."'");
    }
?>