<?php require 
    
    '../scripts/header.php'; 

if($_POST['add']=='New')
{   
    mysql_query("INSERT INTO questions( examNum, question, option1, option2, option3, option4, option5, createdby ) 
                VALUES ( ".$_GET['exam'].", '',  '',  '',  '',  '',  '',  '".$_SESSION['usr']."' )");
    $newQuestion = mysql_fetch_assoc(mysql_query("SELECT id FROM questions ORDER BY id DESC LIMIT 1;"));
    header("Location: edit_exam?exam=".$_GET['exam']."&question=".$newQuestion['id']);
}

if($_POST['update']=='Update')
{   
    mysql_query("UPDATE questions SET type='".$_POST['type']."' WHERE id='".$_GET['question']."'");
    
    $_POST['question'] = mysql_real_escape_string($_POST['question']);
    mysql_query("UPDATE questions SET question='".$_POST['question']."', public=".$_POST['public']." WHERE id='".$_GET['question']."'");
    
    if($_POST['option1']) {
        $_POST['option1'] = mysql_real_escape_string($_POST['option1']);
        $_POST['option2'] = mysql_real_escape_string($_POST['option2']);
        $_POST['option3'] = mysql_real_escape_string($_POST['option3']);
        $_POST['option4'] = mysql_real_escape_string($_POST['option4']);
        $_POST['option5'] = mysql_real_escape_string($_POST['option5']);
        mysql_query("UPDATE questions SET option1='".$_POST['option1']."', option2='".$_POST['option2']."', option3='".$_POST['option3']."', option4='".$_POST['option4']."', option5='".$_POST['option5']."' WHERE id='".$_GET['question']."'");
    }
    $_SESSION['msg']['success']='Question updated';
}

if($_POST['updateExam']=='Update')
{   
    if($_POST['time_limit']) {
        $time_limit = mysql_real_escape_string($_POST['time_limit']);
    } else {
        $time_limit = 60;
    }
    
    if($_POST['attempts']) {
        $attempts = mysql_real_escape_string($_POST['attempts']);
    } else {
        $attempts = 1;
    }
    
    mysql_query("UPDATE exams SET public=".$_POST['public'].", time_limit=".$time_limit.", attempts=".$attempts." WHERE id='".$_GET['exam']."'");
    $_SESSION['msg']['success']='Exam updated';
}

if($_POST['shuffle']=='Shuffle') {
    mysql_query("UPDATE questions SET type='".$_POST['type']."' WHERE id='".$_GET['question']."'");
    
    $_POST['question'] = mysql_real_escape_string($_POST['question']);
    mysql_query("UPDATE questions SET question='".$_POST['question']."', public=".$_POST['public']." WHERE id='".$_GET['question']."'");
    
    if($_POST['option1']) {
        $_POST['option1'] = mysql_real_escape_string($_POST['option1']);
        $_POST['option2'] = mysql_real_escape_string($_POST['option2']);
        $_POST['option3'] = mysql_real_escape_string($_POST['option3']);
        $_POST['option4'] = mysql_real_escape_string($_POST['option4']);
        $_POST['option5'] = mysql_real_escape_string($_POST['option5']);
        
        $optionList = array();
        if($_POST['option1']) array_push($optionList, $_POST['option1']);
        if($_POST['option2']) array_push($optionList, $_POST['option2']);
        if($_POST['option3']) array_push($optionList, $_POST['option3']);
        if($_POST['option4']) array_push($optionList, $_POST['option4']);
        if($_POST['option5']) array_push($optionList, $_POST['option5']);
        shuffle($optionList);
        
        if($optionList[0]) $_POST['option1'] = $optionList[0];
        if($optionList[1]) $_POST['option2'] = $optionList[1];
        if($optionList[2]) $_POST['option3'] = $optionList[2];
        if($optionList[3]) $_POST['option4'] = $optionList[3];
        if($optionList[4]) $_POST['option5'] = $optionList[4];
        
        mysql_query("UPDATE questions SET option1='".$_POST['option1']."', option2='".$_POST['option2']."', option3='".$_POST['option3']."', option4='".$_POST['option4']."', option5='".$_POST['option5']."' WHERE id='".$_GET['question']."'");
    }
    $_SESSION['msg']['success']='Answers shuffled';
}
?>

<!DOCTYPE html>
<html>
<head>

<?php include_once("../templates/htmlHeader.php") ?>
    
<script>    
    function deleteQuestion() {
        $confirm = confirm("Are you sure you want to delete this question? This cannot be undone.");
            if($confirm === true) {
            var xhttp = new XMLHttpRequest();
            <?php echo 'xhttp.open("GET", "../scripts/delete.php?question='.$_GET['question'].'", true);'; ?>
            xhttp.send();
            window.location.assign(<?php echo '"http://www.deweyhaftaacademy.x10host.com/admin/edit_exam?exam='.$_GET['exam'].'"'; ?>);
        }
    }
    
    function deleteExam() {
        $confirm = confirm("Are you sure you want to delete this exam? This cannot be undone.");
            if($confirm === true) {
            var xhttp = new XMLHttpRequest();
            <?php echo 'xhttp.open("GET", "../scripts/delete.php?exam='.$_GET['exam'].'", true);'; ?>
            xhttp.send();
            window.location.assign('http://www.deweyhaftaacademy.x10host.com/admin/edit_exam');
        }
    }

</script>
</head>
<body>
    
<div id="header">
    <a href="../main" id="title">Dewey Hafta Academy</a>
    <?php include_once("../templates/login.php"); ?>
</div>
    
<?php include_once("../templates/menu.php"); ?>
    
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
                $courses = mysql_query("SELECT id,course FROM courses ORDER BY course ASC");
                if (mysql_num_rows($courses) > 0) 
                {
                    // output data of each row
                    while($rowCourse = mysql_fetch_assoc($courses)) {
                        echo "<h4>".$rowCourse['course']."</h4><ul>";
                        $exams = mysql_query("SELECT id,module,title FROM exams WHERE course='".$rowCourse['id']."' ORDER BY module ASC");
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
                echo "<input type='button' onclick='location.href=\"create_exam\";' value='New'/>";
            } else if(!$_GET['question'])
            {
                $examName = mysql_fetch_assoc(mysql_query("SELECT module,title,public,time_limit,attempts FROM exams WHERE id='".$_GET['exam']."'"));
                if(!$_GET['source']) echo "<h2><a href='edit_exam' ";
                else echo "<h2><a href='".$_GET['source']."?course=".$_GET['get']."' ";
                echo "class='black'>&larr;</a> Module ".$examName['module'].": ".$examName['title']."</h2>";
                echo "<form action='' method='post'><p style='line-height:1.8;'><table class='examInput'><tr><td><b>Public:</b></td><td><input type='radio' name='public' value='1' ";
                if($examName['public'] == 1) {echo "checked ";} 
                echo "><label>Yes</label></input> <input type='radio' name='public' value='0' ";
                if($examName['public'] == 0) {echo "checked ";} 
                echo "><label>No</label></input></td></tr>
                    <tr><td><b>Time limit:</b></td> <td><input class='editExam num' type='number' name='time_limit' value='".$examName['time_limit']."'/></td></tr>
                    <tr><td><b>Attempts:</b></td> <td><input class='editExam num' type='number' name='attempts' value='".$examName['attempts']."'/></td></tr></table></p>
                    <input type='submit' name='updateExam' value='Update' /><h3>Questions</h3>";                
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
                echo "<br><input type='submit' name='add' value='New' /> <input type='button' onclick='deleteExam();' name='delete' value='Delete' /></form>";
            } else
            {
                $examName = mysql_fetch_assoc(mysql_query("SELECT module,title FROM exams WHERE id='".$_GET['exam']."'"));
                if(!$_GET['source']) echo "<h2><a href='edit_exam?exam=".$_GET['exam']."' ";
                else "<h2><a href='".$_GET['source']."' ";
                echo "class='black'>&larr;</a> Module ".$examName['module'].": ".$examName['title']."</h2>";
                $examQuestions = mysql_fetch_assoc(mysql_query("SELECT * FROM questions WHERE id='".$_GET['question']."'"));

                echo "<form action='' method='post'><p><b>Question Type</b><br><input type='radio' name='type' value='0' ";
                if($examQuestions['type'] == 0) echo "checked ";
                echo "><label>Multiple Choice (one choice)</label></input><br><input type='radio' name='type' value='1' ";
                if($examQuestions['type'] == 1) echo "checked ";
                echo "><label>Multiple Choice (multiple choices)</label></input><br><input type='radio' name='type' value='2' ";
                if($examQuestions['type'] == 2) echo "checked ";
                echo "><label>True/False</label></input><br><input type='radio' name='type' value='3' ";
                if($examQuestions['type'] == 3) echo "checked ";
                echo "><label>Free Response</label></input><br>
                    <br></p>";
                if($examQuestions['type'] == 0) {
                    echo "<p><b>Question:</b> <input class='editExam' type='field' name='question' value='".$examQuestions['question']."'/><br>
                    <input type='radio' name='answer' value='option1'/><label for='option1'><b>Option 1:</b> <input class='editExam' type='field' name='option1' value='".$examQuestions['option1']."'/></label><br>
                    <input type='radio' name='answer' value='option2'/><label for='option2'><b>Option 2:</b> <input class='editExam' type='field' name='option2' value='".$examQuestions['option2']."'/></label><br>
                    <input type='radio' name='answer' value='option3'/><label for='option3'><b>Option 3:</b> <input class='editExam' type='field' name='option3' value='".$examQuestions['option3']."'/></label><br>
                    <input type='radio' name='answer' value='option4'/><label for='option4'><b>Option 4:</b> <input class='editExam' type='field' name='option4' value='".$examQuestions['option4']."'/></label><br>
                    <input type='radio' name='answer' value='option5'/><label for='option5'><b>Option 5:</b> <input class='editExam' type='field' name='option5' value='".$examQuestions['option5']."'/></label><br></p>";
                }
                else if($examQuestions['type'] == 1) {
                    echo "<p><b>Question:</b> <input class='editExam' type='field' name='question' value='".$examQuestions['question']."'/><br>
                    <input type='checkbox' name='answer' value='option1'/><label for='option1'><b>Option 1:</b> <input class='editExam' type='field' name='option1' value='".$examQuestions['option1']."'/></label><br>
                    <input type='checkbox' name='answer' value='option2'/><label for='option2'><b>Option 2:</b> <input class='editExam' type='field' name='option2' value='".$examQuestions['option2']."'/></label><br>
                    <input type='checkbox' name='answer' value='option3'/><label for='option3'><b>Option 3:</b> <input class='editExam' type='field' name='option3' value='".$examQuestions['option3']."'/></label><br>
                    <input type='checkbox' name='answer' value='option4'/><label for='option4'><b>Option 4:</b> <input class='editExam' type='field' name='option4' value='".$examQuestions['option4']."'/></label><br>
                    <input type='checkbox' name='answer' value='option5'/><label for='option5'><b>Option 5:</b> <input class='editExam' type='field' name='option5' value='".$examQuestions['option5']."'/></label><br></p>";
                }
                else if($examQuestions['type'] == 2) {
                    echo "<p><b>Question:</b> <input class='editExam' type='field' name='question' value='".$examQuestions['question']."'/><br>
                    <input type='radio' name='answer' value='option1'/><label for='option1'><b>Option 1:</b> <span class='editExam' type='field' name='option1'>True</span></label><br>
                    <input type='radio' name='answer' value='option1'/><label for='option1'><b>Option 2:</b> <span class='editExam' type='field' name='option2'>False</span></label><br></p>";  
                }
                else if($examQuestions['type'] == 3) {
                    echo "<p><b>Question:</b> <input class='editExam' type='field' name='question' value='".$examQuestions['question']."'/><br><span style='font-size:86%;'>Question will be graded by supervisor.</span><br></p>";
                }
                echo "<p><b>Public?</b> <input type='radio' name='public' value='1' ";
                if($examQuestions['public'] == 1) {echo "checked ";} 
                echo "><label>Yes</label></input> <input type='radio' name='public' value='0' ";
                if($examQuestions['public'] == 0) {echo "checked ";} 
                echo "><label>No</label></input><br></p><p><input type='submit' name='update' value='Update' /> ";
                if($examQuestions['type'] != 3 && $examQuestions['type'] != 2) echo "<input type='submit' name='shuffle' value='Shuffle' /> ";
                echo "<input type='button' onclick='deleteQuestion();' name='delete' value='Delete' /></p>
                    </form>";
            }
            ?>
        
    <?php
	   endif;
    ?>
    </div>
</div>

<?php require '../scripts/jsload.php'; ?>
    
</body>
</html>