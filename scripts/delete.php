<?php
    require '../scripts/header.php';

    if(($_GET['question'] && $_GET['exam']) || ($_GET['question'] && $_GET['course']) || ($_GET['exam'] && $_GET['course']))
    {
        $_SESSION['msg']['err'] = "Error: You can only delete one thing at a time.";
    }
    else if($_GET['question'] && ($_SESSION['privilege'] == "administrator" || $_SESSION['privilege'] == "sysop")) 
    {
        mysqli_query($link, "DELETE FROM questions WHERE id=".$_GET['question']);
        $_SESSION['msg']['success']='Question successfully deleted.';
    }
    else if($_GET['exam'] && ($_SESSION['privilege'] == "administrator" || $_SESSION['privilege'] == "sysop")) 
    {
        mysqli_query($link, "DELETE FROM exams WHERE id=".$_GET['exam']);
        $_SESSION['msg']['success']='Exam successfully deleted.';
    }
    else if($_GET['course'] && ($_SESSION['privilege'] == "administrator" || $_SESSION['privilege'] == "sysop")) 
    {
        mysqli_query($link, "DELETE FROM courses WHERE id=".$_GET['course']);
        $_SESSION['msg']['success']='Course successfully deleted.';
    }
    else if($_GET['user'] && ($_SESSION['id'] == $_GET['user'] || $_SESSION['privilege'] == "sysop" || $_SESSION['privilege'] == "administrator")) 
    {
        if($_SESSION['id'] == $_GET['user']) {
            $_SESSION = array();
            session_destroy();
        }
        mysqli_query($link, "DELETE FROM dewey_members WHERE id=".$_GET['user']);
        $_SESSION['msg']['success']='Account successfully deleted.';
    }
    else 
    {
        $_SESSION['msg']['err'] = "Error: Deletion failed.";
    }
?>