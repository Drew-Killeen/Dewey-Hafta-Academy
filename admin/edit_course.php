<?php require 
    
    '../templates/header.php'; 



?>

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
        if(!$_SESSION['id'] || ($_SESSION['privilege'] != admin && $_SESSION['privilege'] != sysop)):
    ?>
        <!--Unauthorized-->
        <p>You do not have sufficient privileges to access this page.</p>
    <?php
        else:
    ?>
        <!--Authorized-->
        <h1>Administrator Control Panel</h1>
            <?php
                if(!$_GET['course']) {
                    echo "<ul>";
                    $courses = mysql_query ("SELECT course FROM courses");
                    if (mysql_num_rows($courses) > 0) 
                    {
                        // output data of each row
                        while($row = mysql_fetch_assoc($courses)) {
                           echo "<li><a href='edit_course?course=".$row['course']."'>".$row['course']."</a></li>";
                    }
                    } else 
                    {
                        echo "<li>0 results</li>";
                    }
                    echo "</ul>";
                } else
                {
                    echo "<h3><a href='edit_course'>&larr;</a> Editing ".$_GET['course']."</h3>";
                }
            ?>
    <?php
	   endif;
    ?>
    </div>
</div>


</body>
</html>