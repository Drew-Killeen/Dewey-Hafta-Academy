<?php 

require 'scripts/header.php'; 

if(!$_GET['exam']) {
    header("Location: course");
    exit();
}
else if($_SESSION['numQuestions'] && ($_GET['question'] >= $_SESSION['numQuestions'])) {
    header("Location: exam?exam=".$_GET['exam']."&review=1");
    exit();
}

$enrollment =  mysqli_fetch_assoc(mysqli_query($link, "SELECT enrollment FROM dewey_members WHERE id='{$_SESSION['id']}'"));
$course = mysqli_fetch_assoc(mysqli_query($link, "SELECT id,course FROM courses WHERE id=".$enrollment['enrollment']));
$examData = mysqli_fetch_assoc(mysqli_query($link, "SELECT title,module,time_limit,attempts FROM exams WHERE course='".$course['id']."' AND id='".$_GET['exam']."'"));
    
if($_GET['question']) $questionNum = $_GET['question']+1;
else $questionNum = 1;

if($_POST['begin'] == 'Begin') {
    /* When exam begins */
    $examNum = mysqli_real_escape_string($link, $_GET['exam']);
    mysqli_query($link, "INSERT INTO attempts (examNum,courseNum,usr,score) VALUES (".$examNum.", ".$course['id'].", ".$_SESSION['id'].", -1)");
    $attempt = mysqli_fetch_assoc(mysqli_query($link, "SELECT id,dt FROM attempts WHERE usr=".$_SESSION['id']." AND examNum=".$_GET['exam']." ORDER BY id DESC LIMIT 1;"));
    $_SESSION['attemptNum'] = $attempt['id'];
    $_SESSION['begindt'] = new DateTime($attempt['dt']);
    $questionData = mysqli_query($link, "SELECT id,question FROM questions WHERE examNum=".$examNum." AND public=1 ORDER BY RAND()");
    
    for($i = 1; $row = mysqli_fetch_assoc($questionData); $i++) {
        mysqli_query($link, "INSERT INTO answers (examNum, questionNum, attemptNum, correct, usr) VALUES (".$examNum.", ".$row['id'].", ".$attempt['id'].", 2, ".$_SESSION['id'].")");
        $_SESSION['questionsID'][$i] = $row['id'];
    }
    
    $_SESSION['numQuestions'] = $i;
    
    header("Location: exam?exam=".$_GET['exam']."&question=1");
    exit();
}

if($_POST['continue_attempt'] == "Continue") {
    $examNum = mysqli_real_escape_string($link, $_GET['exam']);
    $attempt = mysqli_fetch_assoc(mysqli_query($link, "SELECT id,dt FROM attempts WHERE usr=".$_SESSION['id']." AND examNum=".$examNum." ORDER BY id DESC LIMIT 1;"));
    $_SESSION['attemptNum'] = $attempt['id'];
    $_SESSION['begindt'] = new DateTime($attempt['dt']);
    $tempAnswers = mysqli_query($link, "SELECT * FROM answers WHERE attemptNum=".$attempt['id']." ORDER BY id ASC");

    for($i = 1; $row = mysqli_fetch_assoc($tempAnswers); $i++) {
        $tempQuestion = mysqli_fetch_assoc(mysqli_query($link, "SELECT id FROM questions WHERE examNum=".$examNum." AND id=".$row['questionNum']));
        $_SESSION['questionsID'][$i] = $tempQuestion['id'];
    }

    $_SESSION['numQuestions'] = $i;
    header("Location: exam?exam=".$_GET['exam']."&question=1");
    exit();
}

if($_POST['submit'] == 'Submit') {
    /* When exam is submitted */
    $examNum = mysqli_real_escape_string($link, $_GET['exam']);
    $attempt = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM attempts WHERE usr='".$_SESSION['id']."' AND examNum='".$examNum."' ORDER BY id DESC LIMIT 1;"));
    $answersData = mysqli_query($link, "SELECT correct FROM answers WHERE attemptNum=".$attempt['id']." AND usr='".$_SESSION['id']."'");
    $finalScore = 0;
    while($row = mysqli_fetch_assoc($answersData)) {
        if($row['correct'] == 1) $finalScore++;
    }
    $finalScore = round(($finalScore/mysqli_num_rows($answersData)) * 100);
    mysqli_query($link, "UPDATE attempts SET score=".$finalScore." WHERE usr='".$_SESSION['id']."' AND examNum='".$examNum."' AND score=-1");
    unset($_SESSION['questionsID']);
    
    $examData = mysqli_fetch_assoc(mysqli_query($link, "SELECT course FROM exams WHERE id=".$_GET['exam']));
    $previousScore = mysqli_query($link, "SELECT score FROM attempts WHERE usr=".$_SESSION['id']." AND examNum=".$examNum." ORDER BY score ASC LIMIT 2;");
    $courseScore = mysqli_query($link, "SELECT score,weight FROM scores WHERE course=".$examData['course']." AND usr=".$_SESSION['id']);
    if(mysqli_num_rows($courseScore) > 0) {
        $courseScore = mysqli_fetch_assoc($courseScore);
        if(mysqli_num_rows($previousScore) > 1) {
            $previousScore = mysqli_fetch_assoc($previousScore);
            if($finalScore > $previousScore['score']) {
                $newCourseScore = ($courseScore['score'] * $courseScore['weight'] + $finalScore - $previousScore['score']) / $courseScore['weight'];
                mysqli_query($link, "UPDATE scores SET score=".$newCourseScore." WHERE usr=".$_SESSION['id']." AND course=".$examData['course']);
            }
        }
        else {
            $newCourseWeight = $courseScore['weight'] + 1;
            $newCourseScore = ($courseScore['score'] * $courseScore['weight'] + $finalScore) / $newCourseWeight;
            mysqli_query($link, "UPDATE scores SET score=".$newCourseScore.", weight=".$newCourseWeight." WHERE usr=".$_SESSION['id']." AND course=".$examData['course']);
        }
    }
    else {
        mysqli_query($link, "INSERT INTO scores (course,usr,score,weight) VALUES (".$enrollment['enrollment'].", ".$_SESSION['id'].", ".$finalScore.", 1)");
    } 
    
    
    header("Location: grade?attempt=".$attempt['id']);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>

<?php include_once("templates/htmlHeader.php") ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
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
        <p>This site is for members only. Click <a href="sign_in">here</a> to login. To apply for a membership, please visit the <a href="sign_up">sign up</a> page.</p>
    <?php
        elseif($_SESSION['privilege'] == 'unapproved'):
    ?>
        <p>Your account must be approved before you can enroll in a course.</p>
    <?php
        else:
    ?>
        <!--Authorized-->
        <?php echo "<h2>Module ".$examData['module'].": ".$examData['title']."</h2>"; ?>
        <?php
        $previousExams = mysqli_query($link, "SELECT * FROM attempts WHERE usr=".$_SESSION['id']." AND score<>-1 AND examNum=".$_GET['exam']);
            if(mysqli_num_rows($previousExams) >= $examData['attempts'] && $examData['attempts'] != 0) {
                echo "<p>You have already used all of your attempts for this exam. If you would like to try again, ask your teacher to delete one of your current attempts.</p>";
            }
            else if($_GET['question']) {
                $date1=date_create($_SESSION['begindt']->format('Y-m-d H:i:s'));
                $date2=date_create(date('Y-m-d H:i:s', time()));
                $diff=date_diff($date2,$date1);
                echo '<div class="timer">Time taken: '.$diff->format("%M:%S").'</div><div class="exam-container"><div class="review-container">';
                $answerData = mysqli_query($link, "SELECT * FROM answers WHERE attemptNum=".$_SESSION['attemptNum']." ORDER BY id ASC");
                
                echo "<p>";
                for($i = 1; $row = mysqli_fetch_assoc($answerData); $i++) {
                    if($row['option'] != 0) echo "<span class='check-mark'>&#10003;</span>";
                    else echo "<span class='question-mark'>?</span>";
                    echo "<a href='?exam=".$_GET['exam']."&question=".$i."' class='whiteLink'>Question ".$i."</a>";
                    if($row['flag'] == 1) echo "<span id='small-flag'></span>";
                    echo "<br>";
                }
                echo "</p>";
                echo '</div><div class="question-container"><form action="" method="post">';
                $questionData = mysqli_fetch_assoc(mysqli_query($link, "SELECT question,option1,option2,option3,option4,option5 FROM questions WHERE examNum=".$_GET['exam']." AND id=".$_SESSION['questionsID'][$_GET['question']]." AND public=1 ORDER BY RAND()"));
                $optionNum = mysqli_fetch_assoc(mysqli_query($link, "SELECT option,flag FROM answers WHERE usr=".$_SESSION['id']." AND questionNum=".$_SESSION['questionsID'][$_GET['question']]." AND attemptNum=".$_SESSION['attemptNum']));
                
                echo "<span id='flag'";
                if($optionNum['flag'] == 1) echo " class='red-flag'";
                echo "></span><p>".$questionData['question']."</p><p><span id='option'>";
                if($questionData['option1']) {
                    echo "<label><input type='radio' name='option' value='1' "; 
                    if($optionNum['option'] == 1) {
                        echo 'checked';
                    } 
                    echo "/>".$questionData['option1']."</label><br>";
                }
                if($questionData['option2']) {
                    echo "<label><input type='radio' name='option' value='2' "; 
                    if($optionNum['option'] == 2) {
                        echo 'checked';
                    } 
                    echo "/>".$questionData['option2']."</label><br>";
                }
                if($questionData['option3']) {
                    echo "<label><input type='radio' name='option' value='3' "; 
                    if($optionNum['option'] == 3) {
                        echo 'checked';
                    } 
                    echo "/>".$questionData['option3']."</label><br>";
                }
                if($questionData['option4']) {
                    echo "<label><input type='radio' name='option' value='4' "; 
                    if($optionNum['option'] == 4) {
                        echo 'checked';
                    } echo "/>".$questionData['option4']."</label><br>";
                }
                if($questionData['option5']) {
                    echo "<label><input type='radio' name='option' value='5' "; 
                    if($optionNum['option'] == 5) {
                        echo 'checked';
                    } 
                    echo "/>".$questionData['option5']."</label><br>";
                }
                echo "</span></p></form></div></div>";
            } 
            else if($_GET['review'] == 1) 
            {
                $answerData = mysqli_query($link, "SELECT * FROM answers WHERE attemptNum=".$_SESSION['attemptNum']." ORDER BY id ASC");
                
                echo "<p>";
                for($i = 1; $row = mysqli_fetch_assoc($answerData); $i++) {
                    if($row['option'] != 0) echo "<span class='check-mark'>&#10003;</span>";
                    else echo "<span class='question-mark'>?</span>";
                    echo "<a href='?exam=".$_GET['exam']."&question=".$i."' class='whiteLink'>Question ".$i."</a>";
                    echo "<br>";
                }
                echo "</p>";
            }
            else {
                $attempt = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM attempts WHERE usr='".$_SESSION['id']."' AND examNum='".$_GET['exam']."' ORDER BY id DESC LIMIT 1;"));
                if($attempt['score'] == -1) 
                {
                    echo "<p>Your previous attempt on this exam is incomplete. Click below to continue your current attempt.</p> <br> <form method='post' action=''><input type='submit' name='continue_attempt' value='Continue' /></form>";
                } 
                else 
                {
                    echo "<p>You are about to begin an exam.</p>";
                    if($examData['attempts'] != 0) echo $examData['attempts']." attempts remaining.<br>";
                    if($examData['time_limit'] != 0) echo "Time limit: ".$examData['time_limit']." minutes.<br>";
                    else echo "No time limit.<br>";
                    echo "<br><form method='post' action=''><input type='submit' name='begin' value='Begin' /></form>";
                }
            }
        ?>
            <?php 
                echo "<div class='float-right;'>";
                if($_GET['question'] || $_GET['review']) {
                    echo ' <form action="" method="post"><input type="submit" name="submit" value="Submit" class="float-right"/></form> '; 
                }
                if($_GET['question'] && !$_GET['review']) {
                    echo ' <input type="button" name="review" onclick="window.location.assign(\'?exam='.$_GET['exam'].'&review=1\');" value="Review"    style="float:right;margin-right:4px;"/> '; 
                }
                echo "</div><div class='exam-buttons-container'>";
                if($_GET['question'] > 1 || $_GET['review']) {
                    if($_GET['review']) {
                        echo '<input type="button" name="back" onclick="window.history.back()"; value="Back" />'; 
                    } 
                    else { 
                        $newQuestionNum = $_GET['question'] - 1; 
                        echo '<input type="button" name="back" onclick="window.location.assign(\'?exam='.$_GET['exam'].'&question='.$newQuestionNum.'\');" value="Back" /> ';
                    }
                }
                if(!$_GET['review'] && $_GET['question']) {
                    echo '<input type="button" name="next" onclick="window.location.assign(\'?exam='.$_GET['exam'];
                    if($_GET['question'] == $_SESSION['numQuestions']-1) { echo '&review=1'; } 
                    else { $newQuestionNum = $_GET['question'] + 1; echo '&question='.$newQuestionNum; }
                    echo '\');" style="float:right;" value="Next" /> ';
                }
                echo "</div>";
            ?>
        
    <?php
	   endif;
    ?>
    </div>
</div>

<?php require 'scripts/jsload.php'; ?>
    
<script>
    document.getElementById("option").addEventListener("click", function() {
        var xhttp = new XMLHttpRequest();
        <?php echo 'xhttp.open("GET", "../scripts/update.php?usr='.$_SESSION['id'].'&exam='.$_GET['exam'].'&question='.
        $_SESSION['questionsID'][$_GET['question']].'&answer="+$(\'input[name="option"]:checked\').val(), true);'; ?>
        xhttp.send();
    });
    
    document.getElementById("flag").addEventListener("click", function() {
        if($(".red-flag").length > 0) {
            $("#flag").removeClass("red-flag");
            
            var xhttp0 = new XMLHttpRequest();
            <?php echo 'xhttp0.open("GET", "../scripts/update.php?usr='.$_SESSION['id'].'&exam='.$_GET['exam'].'&question='.
            $_SESSION['questionsID'][$_GET['question']].'&flag=1", true);'; ?>
            xhttp0.send();
        }
        else {
            $("#flag").addClass("red-flag");
            
            var xhttp1 = new XMLHttpRequest();
            <?php echo 'xhttp1.open("GET", "../scripts/update.php?usr='.$_SESSION['id'].'&exam='.$_GET['exam'].'&question='.
            $_SESSION['questionsID'][$_GET['question']].'&flag=2", true);'; ?>
            xhttp1.send();
        }
    });
</script>
    
</body>
</html>
