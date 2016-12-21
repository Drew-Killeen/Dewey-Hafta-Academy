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
        $questionCorrect = mysql_fetch_assoc(mysql_query("SELECT * FROM questions WHERE id='".$_GET['question']."'"));
        if($_GET['answer'] == $questionCorrect['option1']) {
            $correct = 1;
            $answerNum = 1;
        }
        else {
            $correct = 0;
        }
        if($_GET['answer'] == $questionCorrect['option2']) {
            $answerNum = 2;
        }
        else if($_GET['answer'] == $questionCorrect['option3']) {
            $answerNum = 3;
        }
        else if($_GET['answer'] == $questionCorrect['option4']) {
            $answerNum = 4;
        }
        else if($_GET['answer'] == $questionCorrect['option5']) {
            $answerNum = 5;
        }
        mysql_query("UPDATE answers SET correct=".$correct.", option=".$answerNum." WHERE questionNum=".$questionCorrect['id']." AND attemptNum=".$attempt['id']);
    }
?>