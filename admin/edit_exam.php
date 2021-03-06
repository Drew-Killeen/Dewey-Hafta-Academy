<?php require 
    
    '../scripts/header.php'; 

if($_POST['add']=='New')
{   
    mysqli_query($link, "INSERT INTO questions( examNum, question, option1, option2, option3, option4, option5, createdby ) 
                VALUES ( ".$_GET['exam'].", '',  '',  '',  '',  '',  '',  '".$_SESSION['usr']."' )");
    $newQuestion = mysqli_fetch_assoc(mysqli_query($link, "SELECT id FROM questions ORDER BY id DESC LIMIT 1;"));
    header("Location: edit_exam?exam=".$_GET['exam']."&question=".$newQuestion['id']);
}

if($_POST['update']=='Update')
{       
    $_POST['question'] = mysqli_real_escape_string($link, $_POST['question']);
    
    if($_POST['answer'] == 'option1') $answer = 1;
    else if($_POST['answer'] == 'option2') $answer = 2;
    else if($_POST['answer'] == 'option3') $answer = 3;
    else if($_POST['answer'] == 'option4') $answer = 4;
    else if($_POST['answer'] == 'option5') $answer = 5;
    else $answer = 0;
    
    mysqli_query($link, "UPDATE questions SET question='".$_POST['question']."', public=".$_POST['public'].", type='".$_POST['type']."', answer=".$answer." WHERE id='".$_GET['question']."'");
    
    if($_POST['type'] == 2) {
        mysqli_query($link, "UPDATE questions SET option1='True', option2='False' WHERE id='".$_GET['question']."'");
    }
    else if($_POST['option1']) {
        $option1 = mysqli_real_escape_string($link, $_POST['option1']);
        $option2 = mysqli_real_escape_string($link, $_POST['option2']);
        $option3 = mysqli_real_escape_string($link, $_POST['option3']);
        $option4 = mysqli_real_escape_string($link, $_POST['option4']);
        $option5 = mysqli_real_escape_string($link, $_POST['option5']);
        mysqli_query($link, "UPDATE questions SET option1='".$option1."', option2='".$option2."', option3='".$option3."', option4='".$option4."', option5='".$option5."' WHERE id='".$_GET['question']."'");
    }
    $_SESSION['msg']['success']='Question updated';
}

if($_POST['updateExam']=='Update')
{   
    if(isset($_POST['time_limit'])) {
        $time_limit = mysqli_real_escape_string($link, $_POST['time_limit']);
    } else {
        $time_limit = 60;
    }
    
    if(isset($_POST['attempts'])) {
        $attempts = mysqli_real_escape_string($link, $_POST['attempts']);
    } else {
        $attempts = 1;
    }
    
    if(isset($_POST['questions'])) {
        $questions = mysqli_real_escape_string($link, $_POST['questions']);
    } else {
        $questions = 20;
    }
    
    mysqli_query($link, "UPDATE exams SET public=".$_POST['public'].", time_limit=".$time_limit.", attempts=".$attempts." WHERE id='".$_GET['exam']."'");
    $_SESSION['msg']['success']='Exam updated';
}

if($_POST['shuffle']=='Shuffle') {    
    $_POST['question'] = mysqli_real_escape_string($link, $_POST['question']);
    mysqli_query($link, "UPDATE questions SET question='".$_POST['question']."', public=".$_POST['public'].", type='".$_POST['type']."', answer=0 WHERE id='".$_GET['question']."'");
    
    if($_POST['option1']) {
        $option1 = mysqli_real_escape_string($link, $_POST['option1']);
        $option2 = mysqli_real_escape_string($link, $_POST['option2']);
        $option3 = mysqli_real_escape_string($link, $_POST['option3']);
        $option4 = mysqli_real_escape_string($link, $_POST['option4']);
        $option5 = mysqli_real_escape_string($link, $_POST['option5']);
        
        $optionList = array();
        if($_POST['option1']) array_push($optionList, $option1);
        if($_POST['option2']) array_push($optionList, $option2);
        if($_POST['option3']) array_push($optionList, $option3);
        if($_POST['option4']) array_push($optionList, $option4);
        if($_POST['option5']) array_push($optionList, $option5);
        shuffle($optionList);
        
        if($optionList[0]) $option1 = $optionList[0];
        if($optionList[1]) $option2 = $optionList[1];
        if($optionList[2]) $option3 = $optionList[2];
        if($optionList[3]) $option4 = $optionList[3];
        if($optionList[4]) $option5 = $optionList[4];
        
        mysqli_query($link, "UPDATE questions SET option1='".$option1."', option2='".$option2."', option3='".$option3."', option4='".$option4."', option5='".$option5."' WHERE id='".$_GET['question']."'");
    }
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
            <?php
            if(!$_GET['exam']) {
                echo "<h2>Current Exams</h2>";
                $courses = mysqli_query($link, "SELECT id,course FROM courses ORDER BY course ASC");
                if (mysqli_num_rows($courses) > 0) {
                    // output data of each row
                    while($rowCourse = mysqli_fetch_assoc($courses)) {
                        echo "<h4>".$rowCourse['course']."</h4><ul>";
                        $exams = mysqli_query($link, "SELECT id,module,title FROM exams WHERE course='".$rowCourse['id']."' ORDER BY module ASC");
                        while($rowExam = mysqli_fetch_assoc($exams)) {
                            $questionsNum = mysqli_query($link, "SELECT id FROM questions WHERE examNum=".$rowExam['id']);
                            echo "<li><a href='?exam=".$rowExam['id']."'>Module ".$rowExam['module'].": ".$rowExam['title']."</a>";
                            if(mysqli_num_rows($questionsNum) > 0) echo "<i> - ".mysqli_num_rows($questionsNum)." questions</i>";
                            echo "</li>";
                        }
                        echo "</ul>";

                    }
                } 
                else  {
                    echo "<p>0 results</p>";
                }
                echo "<input type='button' onclick='location.href=\"create_exam\";' value='New'/>";
            } 
            else if(!$_GET['question'] && !$_GET['action']) {
                $examName = mysqli_fetch_assoc(mysqli_query($link, "SELECT module,title,public,time_limit,attempts,questions FROM exams WHERE id='".$_GET['exam']."'"));
                if(!$_GET['source']) echo "<h2><a href='edit_exam' ";
                else echo "<h2><a href='".$_GET['source']."?course=".$_GET['get']."' ";
                echo "class='black'>&larr;</a> Module ".$examName['module'].": ".$examName['title']." <small><i>(<a href='edit_exam?exam=".$_GET['exam']."&action=statistics'>stats</a>)</i></small></h2>";
                echo "<form action='' method='post'><p style='line-height:1.8;'><table class='examInput'><tr><td><b>Public:</b></td><td><input type='radio' name='public' value='1' ";
                if($examName['public'] == 1) {echo "checked ";} 
                echo "><label>Yes</label></input> <input type='radio' name='public' value='0' ";
                if($examName['public'] == 0) {echo "checked ";} 
                echo "><label>No</label></input></td></tr>
                    <tr><td><b>Time limit:</b></td> <td><input class='editExam num' type='number' name='time_limit' value='".$examName['time_limit']."'/></td></tr>
                    <tr><td><b>Attempts:</b></td> <td><input class='editExam num' type='number' name='attempts' value='".$examName['attempts']."'/></td></tr>
                    <tr><td><b>Questions:</b></td> <td><input class='editExam num' type='number' name='questions' value='".$examName['questions']."'/></td></tr></table></p>
                    <input type='submit' name='updateExam' value='Update' /><h3>Questions</h3>";                
                $examQuestions = mysqli_query($link, "SELECT id,question,public FROM questions WHERE examNum=".$_GET['exam']);
                $publicQuestions = mysqli_num_rows(mysqli_query($link, "SELECT * FROM questions WHERE examNum=".$_GET['exam']." AND public=1"));
                $notPublicQuestions = mysqli_num_rows(mysqli_query($link, "SELECT * FROM questions WHERE examNum=".$_GET['exam']." AND public=0"));
                if (mysqli_num_rows($examQuestions) > 0) {
                    echo "<i>".$publicQuestions." public questions, ".$notPublicQuestions." not public</i><table id='table' class='table table-hover table-mc-light-blue'>
                        <thead>
                            <tr>
                              <th>ID</th>
                              <th>Question</th>
                              <th>Edit</th>
                              <th>Status</th>
                            </tr>
                        </thead>";
                    $i = 1;
                    while($row = mysqli_fetch_assoc($examQuestions)) {
                        echo "<tr><td>".$i.".</td><td> ".$row['question']." </td><td>(<a href='?exam=".$_GET['exam']."&question=".$row['id']."'>edit</a>)</td>";
                        if($row['public'] == 0) echo "<td><i>not public</i></td>";
                        else echo "<td><i>public</i></td>";
                        echo "</tr>";
                        $i++;
                    }
                    echo "</table>";
                } 
                else {
                    echo "<p>0 results</p>";
                }
                echo "<br><input type='submit' name='add' value='New' /> <input type='button' onclick='deleteExam();' name='delete' value='Delete' /></form>";
            }
            else if(!$_GET['question'] && $_GET['action'] == 'statistics') {
                $examName = mysqli_fetch_assoc(mysqli_query($link, "SELECT course,module,title,public,time_limit,attempts,questions FROM exams WHERE id='".$_GET['exam']."'"));
                $studentsEnrolled = mysqli_num_rows(mysqli_query($link, "SELECT id FROM dewey_members WHERE enrollment=".$examName['course']));
                $attemptsData = mysqli_query($link, "SELECT score FROM attempts WHERE examNum=".$_GET['exam']." AND score<>-1");
                $numAttempts = mysqli_num_rows($attemptsData);
                $averageAttempts = $numAttempts / $studentsEnrolled;
                $i=0;
                while($row = mysqli_fetch_assoc($attemptsData)) {
                    $averageScore = $averageScore + $row['score'];
                    $i++;
                }
                $averageScore = round($averageScore / $i);
                echo "<h2><a href='edit_exam?exam=".$_GET['exam']."' class='black'>&larr;</a> Statistics for Module ".$examName['module'].": ".$examName['title']."</h2>";
                
                echo "<table>
                        <tr><td>Students enrolled in course:</td><td>".$studentsEnrolled."</td></tr>
                        <tr><td>Number of attempts:</td><td>".$numAttempts."</td></tr>
                        <tr><td>Average attempts per student:</td><td>".$averageAttempts."</td></tr>
                        <tr><td>Average score:</td><td>".$averageScore."%</td></tr>
                    </table>";
                
            }
            else if($_GET['question'] && !$_GET['action']) {
                $examName = mysqli_fetch_assoc(mysqli_query($link, "SELECT module,title FROM exams WHERE id='".$_GET['exam']."'"));
                if(!$_GET['source']) echo "<h2><a href='edit_exam?exam=".$_GET['exam']."' ";
                else "<h2><a href='".$_GET['source']."' ";
                echo "class='black'>&larr;</a> Module ".$examName['module'].": ".$examName['title']." <small><i>(<a href='edit_exam?exam=".$_GET['exam']."&question=".$_GET['question']."&action=statistics'>stats</a>)</i></small></h2>";
                $examQuestions = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM questions WHERE id='".$_GET['question']."'"));

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
                    echo "<p><b>Question:</b> <input class='editExam' type='field' name='question' value='".htmlspecialchars($examQuestions['question'], ENT_QUOTES)."'/><br>
                    <input type='radio' name='answer' value='option1'";
                    if($examQuestions['answer'] == 1) echo "checked";
                    echo "/><label for='option1'><b>Option 1:</b> <input class='editExam' type='field' name='option1' value='".htmlspecialchars($examQuestions['option1'], ENT_QUOTES)."'/></label><br>
                    <input type='radio' name='answer' value='option2'";
                    if($examQuestions['answer'] == 2) echo "checked";
                    echo "/><label for='option2'><b>Option 2:</b> <input class='editExam' type='field' name='option2' value='".htmlspecialchars($examQuestions['option2'], ENT_QUOTES)."'/></label><br>
                    <input type='radio' name='answer' value='option3'";
                    if($examQuestions['answer'] == 3) echo "checked";
                    echo "/><label for='option3'><b>Option 3:</b> <input class='editExam' type='field' name='option3' value='".htmlspecialchars($examQuestions['option3'], ENT_QUOTES)."'/></label><br>
                    <input type='radio' name='answer' value='option4'";
                    if($examQuestions['answer'] == 4) echo "checked";
                    echo "/><label for='option4'><b>Option 4:</b> <input class='editExam' type='field' name='option4' value='".htmlspecialchars($examQuestions['option4'], ENT_QUOTES)."'/></label><br>
                    <input type='radio' name='answer' value='option5'";
                    if($examQuestions['answer'] == 5) echo "checked";
                    echo "/><label for='option5'><b>Option 5:</b> <input class='editExam' type='field' name='option5' value='".htmlspecialchars($examQuestions['option5'], ENT_QUOTES)."'/></label><br></p>";
                }
                else if($examQuestions['type'] == 1) {
                    echo "<p><b>Question:</b> <input class='editExam' type='field' name='question' value='".htmlspecialchars($examQuestions['question'], ENT_QUOTES)."'/><br>
                    <input type='checkbox' name='answer' value='option1'/><label for='option1'><b>Option 1:</b> <input class='editExam' type='field' name='option1' value='".htmlspecialchars($examQuestions['option1'], ENT_QUOTES)."'/></label><br>
                    <input type='checkbox' name='answer' value='option2'/><label for='option2'><b>Option 2:</b> <input class='editExam' type='field' name='option2' value='".htmlspecialchars($examQuestions['option2'], ENT_QUOTES)."'/></label><br>
                    <input type='checkbox' name='answer' value='option3'/><label for='option3'><b>Option 3:</b> <input class='editExam' type='field' name='option3' value='".htmlspecialchars($examQuestions['option3'], ENT_QUOTES)."'/></label><br>
                    <input type='checkbox' name='answer' value='option4'/><label for='option4'><b>Option 4:</b> <input class='editExam' type='field' name='option4' value='".htmlspecialchars($examQuestions['option4'], ENT_QUOTES)."'/></label><br>
                    <input type='checkbox' name='answer' value='option5'/><label for='option5'><b>Option 5:</b> <input class='editExam' type='field' name='option5' value='".htmlspecialchars($examQuestions['option5'], ENT_QUOTES)."'/></label><br></p>";
                }
                else if($examQuestions['type'] == 2) {
                    echo "<p><b>Question:</b> <input class='editExam' type='field' name='question' value='".htmlspecialchars($examQuestions['question'], ENT_QUOTES)."'/><br>
                    <input type='radio' name='answer' value='option1'";
                    if($examQuestions['answer'] == 1) echo "checked";
                    echo "/><label for='option1'><b>Option 1:</b> <span class='editExam' type='field' name='option1'>True</span></label><br>
                    <input type='radio' name='answer' value='option1'";
                    if($examQuestions['answer'] == 2) echo "checked";
                    echo "/><label for='option1'><b>Option 2:</b> <span class='editExam' type='field' name='option2'>False</span></label><br></p>";  
                }
                else if($examQuestions['type'] == 3) {
                    echo "<p><b>Question:</b> <input class='editExam' type='field' name='question' value='".htmlspecialchars($examQuestions['question'], ENT_QUOTES)."'/><br><span style='font-size:86%;'>Question will be graded by supervisor.</span><br><span class='red'>Free response questions are not currently supported.</span><br></p>";
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
            else if($_GET['action'] == 'statistics') {
                $examName = mysqli_fetch_assoc(mysqli_query($link, "SELECT module,title,public,time_limit,attempts,questions FROM exams WHERE id='".$_GET['exam']."'"));
                echo "<h2><a href='edit_exam?exam=".$_GET['exam']."&question=".$_GET['question']."' class='black'>&larr;</a> Statistics for Module ".$examName['module'].": ".$examName['title']."</h2>";
                $answersData = mysqli_query($link, "SELECT id,correct,option FROM answers WHERE questionNum=".$_GET['question']." AND option<>0");
                $option1 = 0;
                $option2 = 0;
                $option3 = 0;
                $option4 = 0;
                $option5 = 0;
                $correct = 0;
        
                while($row = mysqli_fetch_assoc($answersData)) {
                    if($row['option'] == 1) $option1++;
                    if($row['option'] == 2) $option2++;
                    if($row['option'] == 3) $option3++;
                    if($row['option'] == 4) $option4++;
                    if($row['option'] == 5) $option5++;
                    if($row['correct'] == 1) $correct++;
                }
                $numAnswers = mysqli_num_rows($answersData);
                $averageScore = round(($correct / $numAnswers) * 100);
                $option1 = round(($option1 / $numAnswers) * 100);
                $option2 = round(($option2 / $numAnswers) * 100);
                $option3 = round(($option3 / $numAnswers) * 100);
                $option4 = round(($option4 / $numAnswers) * 100);
                $option5 = round(($option5 / $numAnswers) * 100);

                echo "<table>
                        <tr><td>Number of times answered</td><td>".$numAnswers."</td></tr>
                        <tr><td>Average score</td><td>".$averageScore."%</td></tr>
                        <tr><td>Option 1</td><td>".$option1."%</td></tr>
                        <tr><td>Option 2</td><td>".$option2."%</td></tr>
                        <tr><td>Option 3</td><td>".$option3."%</td></tr>
                        <tr><td>Option 4</td><td>".$option4."%</td></tr>
                        <tr><td>Option 5</td><td>".$option5."%</td></tr>
                    </table>";
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