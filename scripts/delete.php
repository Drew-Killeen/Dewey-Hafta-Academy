<?php
    require '../scripts/header.php';

    if(($_GET['question'] && $_GET['exam']) || ($_GET['question'] && $_GET['course']) || ($_GET['exam'] && $_GET['course']))
    {
        $_SESSION['msg']['err'] = "Error: You can only delete one thing at a time.";
    }
    else if($_GET['question'] && ($_SESSION['privilege'] == "administrator" || $_SESSION['privilege'] == "sysop")) 
    {
        mysql_query("DELETE FROM questions WHERE id=".$_GET['question']);
        $_SESSION['msg']['success']='Question successfully deleted.';
    }
    else if($_GET['exam'] && ($_SESSION['privilege'] == "administrator" || $_SESSION['privilege'] == "sysop")) 
    {
        mysql_query("DELETE FROM exams WHERE id=".$_GET['exam']);
        $_SESSION['msg']['success']='Exam successfully deleted.';
    }
    else if($_GET['course'] && ($_SESSION['privilege'] == "administrator" || $_SESSION['privilege'] == "sysop")) 
    {
        mysql_query("DELETE FROM courses WHERE id=".$_GET['course']);
        $_SESSION['msg']['success']='Course successfully deleted.';
    }
    else 
    {
        $_SESSION['msg']['err'] = "Error: Deletion failed.";
    }
?>