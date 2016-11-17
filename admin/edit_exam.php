<?php require 
    
    '../templates/header.php'; 

if($_POST['add']=='New')
{   
    mysql_query("INSERT INTO questions( examNum, question, option1, option2, option3, option4, option5, createdby ) 
                VALUES ( ".$_GET['exam'].", '',  '',  '',  '',  '',  '',  '".$_SESSION['usr']."' )");
    $newQuestion = mysql_fetch_assoc(mysql_query("SELECT id FROM questions ORDER BY id DESC LIMIT 1;"));
    header("Location: edit_exam?exam=".$_GET['exam']."&question=".$newQuestion['id']);
}

if($_POST['update']=='Update')
{   
    $_POST['question'] = mysql_real_escape_string($_POST['question']);
    $_POST['option1'] = mysql_real_escape_string($_POST['option1']);
    $_POST['option2'] = mysql_real_escape_string($_POST['option2']);
    $_POST['option3'] = mysql_real_escape_string($_POST['option3']);
    $_POST['option4'] = mysql_real_escape_string($_POST['option4']);
    $_POST['option5'] = mysql_real_escape_string($_POST['option5']);
    mysql_query("UPDATE questions SET question='".$_POST['question']."', option1='".$_POST['option1']."', option2='".$_POST['option2']."', option3='".$_POST['option3']."', option4='".$_POST['option4']."', option5='".$_POST['option5']."', public=".$_POST['public']." WHERE id='".$_GET['question']."'");
    header("Location: edit_exam?exam=".$_GET['exam']);
}

if($_POST['updateExam']=='Update')
{   
    mysql_query("UPDATE exams SET public=".$_POST['public']." WHERE id='".$_GET['exam']."'");
    header("Location: edit_exam?exam=".$_GET['exam']);
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
                            $questionsNum = mysql_query("SELECT id FROM questions WHERE examNum=".$rowExam['id']);
                            echo "<li><a href='?exam=".$rowExam['id']."'>Module ".$rowExam['module'].": ".$rowExam['title']."</a>";
                            if(mysql_num_rows($questionsNum) > 0) echo "<i> - ".mysql_num_rows($questionsNum)." questions</i>";
                            echo "</li>";
                        }
                        echo "</ul>";

                }
                } else 
                {
                    echo "0 results";
                }
            } else if(!$_GET['question'])
            {
                $examName = mysql_fetch_assoc(mysql_query("SELECT module,title,public FROM exams WHERE id='".$_GET['exam']."'"));
                echo "<h2><a href='edit_exam' class='black'>&larr;</a> Module ".$examName['module'].": ".$examName['title']."</h2>";
                echo "<form action='' method='post'><b>Public</b> <input type='radio' name='public' value='1' ";
                if($examName['public'] == 1) {echo "checked ";} 
                echo "><label>Yes</label></input> <input type='radio' name='public' value='0' ";
                if($examName['public'] == 0) {echo "checked ";} 
                echo "><label>No</label></input><br><input type='submit' name='updateExam' value='Update' /><h3>Questions</h3>";
                $examQuestions = mysql_query("SELECT id,question,public FROM questions WHERE examNum='".$_GET['exam']."'");
                if (mysql_num_rows($examQuestions) > 0) 
                {
                    // output data of each row
                    while($row = mysql_fetch_assoc($examQuestions)) {
                        echo "<b>Question:</b> ".$row['question']." (<a href='?exam=".$_GET['exam']."&question=".$row['id']."'>edit</a>)";
                        if($row['public'] == 0) echo " <i>... not public</i>";
                        echo "<br>";
                    }
                } else 
                {
                    echo "0 results";
                }
                echo "<br><input type='submit' name='add' value='New' /></form>";
            } else
            {
                $examName = mysql_fetch_assoc(mysql_query("SELECT module,title FROM exams WHERE id='".$_GET['exam']."'"));
                echo "<h2><a href='edit_exam?exam=".$_GET['exam']."' class='black'>&larr;</a> Module ".$examName['module'].": ".$examName['title']."</h2>";
                $examQuestions = mysql_fetch_assoc(mysql_query("SELECT * FROM questions WHERE id='".$_GET['question']."'"));

                echo 
                    "<form action='' method='post'>
                    <b>Question:</b> <input class='editExam' type='field' name='question' value='".$examQuestions['question']."'/><br>
                    <b>Option 1:</b> <input class='editExam' type='field' name='option1' value='".$examQuestions['option1']."'/><br>
                    <b>Option 2:</b> <input class='editExam' type='field' name='option2' value='".$examQuestions['option2']."'/><br>
                    <b>Option 3:</b> <input class='editExam' type='field' name='option3' value='".$examQuestions['option3']."'/><br>
                    <b>Option 4:</b> <input class='editExam' type='field' name='option4' value='".$examQuestions['option4']."'/><br>
                    <b>Option 5:</b> <input class='editExam' type='field' name='option5' value='".$examQuestions['option5']."'/><br>
                    <p><b>Public?</b> <input type='radio' name='public' value='1' ";
                if($examQuestions['public'] == 1) {echo "checked ";} 
                echo "><label>Yes</label></input> <input type='radio' name='public' value='0' ";
                if($examQuestions['public'] == 0) {echo "checked ";} 
                echo "><label>No</label></input><br></p><p><input type='submit' name='update' value='Update' /> <input type='submit' name='delete' value='Delete' /></p>
                    </form>";
            }
            ?>
        
    <?php
	   endif;
    ?>
    </div>
</div>

<?php require '../templates/jsload.php'; ?>
    
</body>
</html>