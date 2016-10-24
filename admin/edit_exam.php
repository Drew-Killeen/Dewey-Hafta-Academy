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
            <?php
            if(!$_GET['exam']) {
                echo "<h2>Current Exams</h2>";
                $courses = mysql_query("SELECT course FROM courses");
                if (mysql_num_rows($courses) > 0) 
                {
                    // output data of each row
                    while($rowCourse = mysql_fetch_assoc($courses)) {
                        echo "<h4>".$rowCourse['course']."</h4><ul>";
                        $exams = mysql_query("SELECT id,module,title FROM exams WHERE course='".$rowCourse['course']."' ORDER BY module ASC");
                        while($rowExam = mysql_fetch_assoc($exams)) {
                            echo "<li><a href='?exam=".$rowExam['id']."'>Module ".$rowExam['module'].": ".$rowExam['title']."</a></li>";
                        }
                        echo "</ul>";

                }
                } else 
                {
                    echo "0 results";
                }
            } else
            {
                $examName = mysql_fetch_assoc(mysql_query("SELECT module,title FROM exams WHERE id='".$_GET['exam']."'"));
                echo "<h2>Module ".$examName['module'].": ".$examName['title']."</h2>";
                $examQuestions = mysql_query("SELECT * FROM questions WHERE examNum='".$_GET['exam']."'");
                if (mysql_num_rows($examQuestions) > 0) 
                {
                    // output data of each row
                    while($row = mysql_fetch_assoc($examQuestions)) {
                        echo 
                            "<form action='' method='post'>
                            <input type='field' name='question' value='".$row['question']."'/><br>
                            <input type='field' name='option1' value='".$row['option1']."'/><br>
                            <input type='field' name='option2' value='".$row['option2']."'/><br>
                            <input type='field' name='option3' value='".$row['option3']."'/><br>
                            <input type='field' name='option4' value='".$row['option4']."'/><br>
                            <input type='field' name='option5' value='".$row['option5']."'/><br>
                            <input type='submit' name='update' value='update' />
                            </form><br>";
                    }
                } else 
                {
                    echo "0 results";
                }
            }
            ?>
        
    <?php
	   endif;
    ?>
    </div>
</div>


</body>
</html>