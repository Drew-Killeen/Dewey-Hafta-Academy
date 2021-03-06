<?php require '../scripts/header.php'; ?>

<!DOCTYPE html>
<html>
<head>

<?php include_once("../templates/htmlHeader.php") ?>
</head>
<body>
    
<?php 
    include_once("../templates/login.php"); 
    include_once("../templates/menu.php"); 
?>
    
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
            <li><a href="edit_course">Edit Course</a></li>
            <li><a href="create_exam">Create Exam</a></li>
            <li><a href="edit_exam">Edit Exam</a></li>
            <li><a href="users">View Users</a></li>
            <li><a href="mail">Send Mail</a></li>
            <li><a href="statistics">Statistics</a></li>
        </ul>
        
    <?php
	   endif;
    ?>
    </div>
</div>
    
<?php require '../scripts/jsload.php'; ?>

</body>
</html>