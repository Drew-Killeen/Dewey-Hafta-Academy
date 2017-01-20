<?php

    require '../scripts/header.php';

    if(($_GET['question'] && $_GET['exam']) || ($_GET['question'] && $_GET['course']) || ($_GET['exam'] && $_GET['course'])) {
        $_SESSION['msg']['err'] = "Error: You can only delete one thing at a time.";
    }
    else if($_GET['question'] && ($_SESSION['privilege'] == "admin" || $_SESSION['privilege'] == "sysop")) {
        mysqli_query($link, "DELETE FROM questions WHERE id=".$_GET['question']);
        $_SESSION['msg']['success']='Question deleted.';
    }
    else if($_GET['exam'] && ($_SESSION['privilege'] == "admin" || $_SESSION['privilege'] == "sysop")) {
        mysqli_query($link, "DELETE FROM exams WHERE id=".$_GET['exam']);
        $_SESSION['msg']['success']='Exam deleted.';
    }
    else if($_GET['course'] && ($_SESSION['privilege'] == "admin" || $_SESSION['privilege'] == "sysop")) {
        mysqli_query($link, "DELETE FROM courses WHERE id=".$_GET['course']);
        $_SESSION['msg']['success']='Course deleted.';
    }
    else if($_GET['user'] && ($_SESSION['id'] == $_GET['user'] || $_SESSION['privilege'] == "sysop" || $_SESSION['privilege'] == "admin")) {
        if($_SESSION['id'] == $_GET['user']) {
            $_SESSION = array();
            session_destroy();
        }
        mysqli_query($link, "DELETE FROM dewey_members WHERE id=".$_GET['user']);
        $_SESSION['msg']['success']='Account deleted.';
    }
    else if($_GET['attempt'] && ($_SESSION['privilege'] == "teacher" || $_SESSION['privilege'] == "admin" || $_SESSION['privilege'] == "sysop")) {
        mysqli_query($link, "DELETE FROM attempts WHERE id=".$_GET['attempt']);
        mysqli_query($link, "DELETE FROM answers WHERE attemptNum=".$_GET['attempt']);
        $_SESSION['msg']['success']='Attempt deleted.';
    }
    else {
        $_SESSION['msg']['err'] = "Error: Deletion failed.";
    }
?>