<?php 
require 'templates/header.php'; 

unset($_SESSION['questions']);
unset($_SESSION['options']);

mysql_query
for($i = 0; i < count($_SESSION['questions']); $i++) {
mysql_query("INSERT INTO answers (questionNum, attemptNum, correct, usr)
            VALUES (
            1,
            1,
            0,
            '".$_SESSION['usr']."'
            )");
}


?>

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
        
    <?php
	   endif;
    ?>
    </div>
</div>


</body>
</html>
