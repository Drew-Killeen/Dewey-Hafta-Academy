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
    
<?php include_once("../templates/subfolder_menu.php"); ?>
    
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
        <p>This part of the site is currently under construction. Feel free to explore and read any information you find.</p>
        
        <ul>
            <li><a href="permissions">Permissions</a></li>
        </ul>
    <?php
	   endif;
    ?>
    </div>
</div>
    
<?php require '../templates/jsload.php'; ?>

</body>
</html>
