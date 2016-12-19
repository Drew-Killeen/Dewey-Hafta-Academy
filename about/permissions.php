<?php require '../scripts/header.php'; ?>

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
    
<?php include_once("../templates/menu.php"); ?>
    
<div id="main">
    <div class="container">
    <?php
        if(!$_SESSION['id']):
    ?>
        <!--Unauthorized-->
        <p>This site is for members only. Click <a href="../sign_in">here</a> to login. To apply for a membership, please visit the <a href="../sign_up">sign up</a> page.</p>
    <?php
        else:
    ?>
        <!--Authorized-->
        <h1>Permissions</h1>
        <p>This page outlines some of the differences between permissions (or "user privileges") on this site.</p>
        <ol>
            <li><b>Unapproved</b> users have access to their account preferences and the list of courses available. They do not have the ability to enroll in any of the courses.</li>
            <li><b>Students</b> have the ability to enroll in courses, take exams, and view their grades.</li>
            <li><b>Teachers</b> have the ability to send mail to their students view their grades, as well as create exams and edit content that they created.</li>
            <li><b>Administrators</b> have the ability to edit all courses, exams, and questions, as well as send mail to anyone on the site, approve users, and edit user privileges below their level.</li>
            <li><b>Sysops</b> have access to the back end of the site, and can edit any user privileges.</li>
        </ol>
    <?php
	   endif;
    ?>
    </div>
</div>
    
<?php require '../scripts/jsload.php'; ?>

</body>
</html>
