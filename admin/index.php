<?php require '../templates/header.php'; ?>

<!DOCTYPE html>
<html>
<head>

<?php include_once("../templates/htmlHeader.php") ?>
</head>
<body>
    
<div id="header">
    <a href="../main" id="title">Dewey Hafta Academy</a>
    <?php include_once("../templates/login.php"); ?>
</div>
    
<div id="main">
    <div class="container">
    <?php
        if(!$_SESSION['id'] || ($_SESSION['privilege'] != admin && $_SESSION['privilege'] != sysop)):
    ?>
        <!--Unauthorized-->
        <p>You do not have sufficient privileges to access this page.</p>
    <?php
        else:
    ?>
        <!--Authorized-->
        <h1>Administrator Control Panel</h1>
        <p>See the following pages to configure the site.</p>
        <ul>
            <li><a href="create_course">Create Course</a></li>
            <li><a href="create_exam">Create Exam</a></li>
            <li><a href="edit_exam">Edit Exam</a></li>
            <li><a href="users">View Users</a></li>
        </ul>
        
    <?php
	   endif;
    ?>
    </div>
</div>


</body>
</html>