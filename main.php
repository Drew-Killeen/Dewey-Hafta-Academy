<?php require 'templates/header.php'; ?>

<!DOCTYPE html>
<html>
<head>

<?php include_once("templates/htmlHeader.php") ?>

</head>
<body>
    
<div id="header">
    <a href="main" id="title">Dewey Hafta Academy</a>
    
    <?php include_once("templates/login.php"); ?>
</div>
    
<?php include_once("templates/menu.php"); ?>
    
<div id="main">
    <div class="container">
    <?php
        if(!$_SESSION['id']):
    ?>
        <!--Unauthorized-->
        <p>This site is for members only. Click <a href="sign_in">here</a> to login. To apply for a membership, please visit the <a href="sign_up">sign up</a> page.</p>
    <?php
        else:
    ?>
        <!--Authorized-->
        <p>This site is currently under construction. We'll notify you when things are up and running. In the meantime, if you'd like to update your account info, please visit your <a href="registered">preferences</a>, or go to <a href="enroll">enroll</a> to manage your enrollments. Thanks for visiting!</p>
        
    <?php
	   endif;
    ?>
    </div>
</div>


</body>
</html>
