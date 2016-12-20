<?php
    require '../scripts/header.php';

    if($_GET['usr'] && $_GET['sass']) 
    {
        $userInfo = mysql_fetch_assoc(mysql_query("SELECT sass FROM dewey_members WHERE id='".$_GET['usr']."'"));
        if($userInfo['sass'] == 0) {$sass = 1; echo "sass1";}
        else {$sass = 0; echo "sass0";}
        mysql_query("UPDATE dewey_members SET sass='".$sass."' WHERE id='".$_GET['usr']."'");
    }
    else if($_GET['usr'] && $_GET['exam'] && $_GET['question'] && $_GET['answer']) {
        $attempt = mysql_fetch_assoc(mysql_query("SELECT id FROM attempts WHERE usr=".$_GET['usr']." AND examNum=".$_GET['exam']." ORDER BY id DESC LIMIT 1;"));
        $questionCorrect = mysql_fetch_assoc(mysql_query("SELECT id,option1 FROM questions WHERE question='".$_GET['question']."'"));
        if($_GET['answer'] == $questionCorrect['option1']) $correct = 1;
        else $correct = 0;
        mysql_query("UPDATE answers SET correct=".$correct." WHERE questionNum=".$questionCorrect['id']." AND attemptNum=".$attempt['id']);
    }
?>