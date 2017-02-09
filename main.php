<?php require 'scripts/header.php'; ?>

<!DOCTYPE html>
<html>
<head>

<?php include_once("templates/htmlHeader.php") ?>

</head>
<body>
    
<?php 
    include_once("templates/login.php"); 
    include_once("templates/menu.php"); 
?>
    
<div id="main">
    <div class="container">
    <?php
        if(!$_SESSION['id']):
    ?>
        <!--Unauthorized-->
        <p>This site is for members only. Please <a href='sign_in'>login</a> to view this page.</p>
    <?php
        else:
    ?>
        <!--Authorized-->
        <p>This site is currently under construction. We'll notify you when things are up and running. In the meantime, feel free to mess around with whatever you can find. Don't worry, you won't break anything.</p>
        
    <?php
	   endif;
    ?>
    </div>
</div>
    
<?php require 'scripts/jsload.php'; ?>

</body>
</html>
