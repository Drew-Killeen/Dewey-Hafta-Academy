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
        <p>--placeholder for mail features--</p>
        
    <?php
	   endif;
    ?>
    </div>
</div>
    
<?php require '../scripts/jsload.php'; ?>

</body>
</html>