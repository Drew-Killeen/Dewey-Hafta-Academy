<?php require 
    
    '../templates/header.php'; 

if($_POST['add']=='New')
{   
    mysql_query("INSERT INTO questions( examNum, question, option1, option2, option3, option4, option5, createdby ) 
                VALUES ( ".$_GET['exam'].", '',  '',  '',  '',  '',  '',  '".$_SESSION['usr']."' )");
    header("Location: edit_exam?exam=".$_GET['exam']);
}

if($_POST['update']=='Update')
{   
    mysql_query("UPDATE questions SET question='".$_POST['question']."', option1='".$_POST['option1']."', option2='".$_POST['option2']."', option3='".$_POST['option3']."', option4='".$_POST['option4']."', option5='".$_POST['option5']."' WHERE id='".$_GET['question']."'");
    header("Location: edit_exam?exam=".$_GET['exam']."&question=".$_GET['question']);
}

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
            if(!$_GET['exam']) {
                echo "<h2>Current Exams</h2>";
                $courses = mysql_query("SELECT course FROM courses ORDER BY course ASC");
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
            } else if(!$_GET['question'])
            {
                $examName = mysql_fetch_assoc(mysql_query("SELECT module,title FROM exams WHERE id='".$_GET['exam']."'"));
                echo "<h2><a href='edit_exam'>&larr;</a> Module ".$examName['module'].": ".$examName['title']."</h2>";
                $examQuestions = mysql_query("SELECT id,question FROM questions WHERE examNum='".$_GET['exam']."'");
                if (mysql_num_rows($examQuestions) > 0) 
                {
                    // output data of each row
                    while($row = mysql_fetch_assoc($examQuestions)) {
                        echo 
                            "<b>Question:</b> ".$row['question']." (<a href='?exam=".$_GET['exam']."&question=".$row['id']."'>edit</a>)<br>";
                    }
                    echo "<form action='' method='post'><input type='submit' name='add' value='New' /></form>";
                } else 
                {
                    echo "0 results";
                }
            } else
            {
                $examName = mysql_fetch_assoc(mysql_query("SELECT module,title FROM exams WHERE id='".$_GET['exam']."'"));
                echo "<h2><a href='edit_exam?exam=".$_GET['exam']."'>&larr;</a> Module ".$examName['module'].": ".$examName['title']."</h2>";
                $examQuestions = mysql_fetch_assoc(mysql_query("SELECT * FROM questions WHERE id='".$_GET['question']."'"));

                echo 
                    "<form action='' method='post'>
                    <b>Question:</b> <input class='editExam' type='field' name='question' value='".$examQuestions['question']."'/><br>
                    <b>Option 1:</b> <input class='editExam' type='field' name='option1' value='".$examQuestions['option1']."'/><br>
                    <b>Option 2:</b> <input class='editExam' type='field' name='option2' value='".$examQuestions['option2']."'/><br>
                    <b>Option 3:</b> <input class='editExam' type='field' name='option3' value='".$examQuestions['option3']."'/><br>
                    <b>Option 4:</b> <input class='editExam' type='field' name='option4' value='".$examQuestions['option4']."'/><br>
                    <b>Option 5:</b> <input class='editExam' type='field' name='option5' value='".$examQuestions['option5']."'/><br>
                    <input type='submit' name='update' value='Update' />
                    </form><br>";
            }
            ?>
        
    <?php
	   endif;
    ?>
    </div>
</div>


</body>
</html>