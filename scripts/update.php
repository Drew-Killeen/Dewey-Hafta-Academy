<?php
    require '../scripts/header.php';

    if($_GET['usr'] && $_GET['sass']) 
    {
        $userInfo = mysql_fetch_assoc(mysql_query("SELECT sass FROM dewey_members WHERE id='".$_GET['usr']."'"));
        if($userInfo['sass'] == 0) {$sass = 1; echo "sass1";}
        else {$sass = 0; echo "sass0";}
        mysql_query("UPDATE dewey_members SET sass='".$sass."' WHERE id='".$_GET['usr']."'");
    }
    else if($_GET['usr'] && $_GET['exam'] && $_GET['question']) {
        $attempt = mysql_fetch_assoc(mysql_query("SELECT id FROM attempts WHERE usr=".$_GET['usr']." AND examNum=".$_GET['exam']." ORDER BY id DESC LIMIT 1;"));
        if($_GET['answer']) {
            $questionCorrect = mysql_fetch_assoc(mysql_query("SELECT answer FROM questions WHERE id='".$_GET['question']."'"));
            if($_GET['answer'] == $questionCorrect['answer']) {
                $correct = 1;
            }
            else {
                $correct = 0;
            }
            mysql_query("UPDATE answers SET correct=".$correct.", option=".$_GET['answer']." WHERE questionNum=".$_GET['question']." AND attemptNum=".$attempt['id']);
        }
        if($_GET['flag']) {
            if($_GET['flag'] == 1) {
                $flag = 0;
            }
            else if($_GET['flag'] == 2) {
                $flag = 1;
            }
            mysql_query("UPDATE answers SET flag=".$flag." WHERE questionNum=".$_GET['question']." AND attemptNum=".$attempt['id']);
        }
    }
?>