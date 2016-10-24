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
        <h2>Current Exams</h2>
        <?php
            $courses = mysql_query("SELECT course FROM courses");
            if (mysql_num_rows($courses) > 0) 
            {
                // output data of each row
                while($rowCourse = mysql_fetch_assoc($courses)) {
                    echo "<h4>".$rowCourse['course']."</h4><ul>";
                    $exams = mysql_query("SELECT module,title FROM exams WHERE course='".$rowCourse['course']."'");
                    while($rowExam = mysql_fetch_assoc($exams)) {
                        echo "<li>Module ".$rowExam['module'].": ".$rowExam['title']."</li>";
                    }
                    echo "</ul>";
                    
            }
            } else 
            {
                echo "0 results";
            }
        ?>
        
    <?php
	   endif;
    ?>
    </div>
</div>


</body>
</html>