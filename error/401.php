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
    
<?php include_once("../templates/subfolder_menu.php"); ?>
    
<div id="main">
    <div class="container">
        <h2>401 Error</h2>
        <p>You are not authorized to access this page.</p>
    </div>
</div>


</body>
</html>