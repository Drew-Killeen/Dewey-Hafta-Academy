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

$enrollment =  mysql_fetch_assoc(mysql_query("SELECT enrollment FROM dewey_members WHERE id='{$_SESSION['id']}'"));
$course = mysql_fetch_assoc(mysql_query("SELECT id,course FROM courses WHERE id=".$enrollment['enrollment']));
$examData = mysql_fetch_assoc(mysql_query("SELECT title,module,time_limit,attempts FROM exams WHERE course='".$course['id']."' AND id='".$_GET['exam']."'"));
    
if($_GET['question']) $questionNum = $_GET['question']+1;
else $questionNum = 1;

if($_POST['continue'] == 'Continue') {
    /* When continue button is pressed */
    if($_GET['question'] == $_SESSION['numQuestions']-1) {
        header("Location: exam?exam=".$_GET['exam']."&review=1");
        exit();
    }
    else {
        header("Location: exam?exam=".$_GET['exam']."&question=".$questionNum);
        exit();
    }
}

if($_POST['begin'] == 'Begin') {
    /* When exam begins */
    mysql_query("INSERT INTO attempts (examNum,courseNum,usr,score) VALUES (".$_GET['exam'].", ".$course['id'].", ".$_SESSION['id'].", -1)");
    $attempt = mysql_fetch_assoc(mysql_query("SELECT id FROM attempts WHERE usr='".$_SESSION['id']."' AND examNum='".$_GET['exam']."' ORDER BY id DESC LIMIT 1;"));
    $_SESSION['attemptNum'] = $attempt['id'];
    $questionData = mysql_query("SELECT id,question FROM questions WHERE examNum=".$_GET['exam']." AND public=1 ORDER BY RAND()");
    
    for($i = 1; $row = mysql_fetch_assoc($questionData); $i++) {
        mysql_query("INSERT INTO answers (examNum, questionNum, attemptNum, correct, usr) VALUES (".$_GET['exam'].", ".$row['id'].", ".$attempt['id'].", 2, ".$_SESSION['id'].")");
        $_SESSION['questionsID'][$i] = $row['id'];
    }
    
    $_SESSION['numQuestions'] = $i;
    
    header("Location: exam?exam=".$_GET['exam']."&question=1");
    exit();
}

if($_POST['continue_attempt'] == "Continue") {
    $attempt = mysql_fetch_assoc(mysql_query("SELECT id FROM attempts WHERE usr='".$_SESSION['id']."' AND examNum='".$_GET['exam']."' ORDER BY id DESC LIMIT 1;"));
    $_SESSION['attemptNum'] = $attempt['id'];

    $questionData = mysql_query("SELECT id,question FROM questions WHERE examNum='".$_GET['exam']."' AND public=1 ORDER BY RAND()");

    for($i = 1; $row = mysql_fetch_assoc($questionData); $i++) {
        mysql_query("INSERT INTO answers (examNum, questionNum, attemptNum, correct, usr) VALUES (".$_GET['exam'].", ".$row['id'].", ".$attempt['id'].", 2, ".$_SESSION['id'].")");
        $_SESSION['questionsID'][$i] = $row['id'];
    }

    $_SESSION['numQuestions'] = $i;
    header("Location: exam?exam=".$_GET['exam']."&question=1");
    exit();
}

if($_POST['submit'] == 'Submit') {
    /* When exam is submitted */
    $attempt = mysql_fetch_assoc(mysql_query("SELECT * FROM attempts WHERE usr='".$_SESSION['id']."' AND examNum='".$_GET['exam']."' ORDER BY id DESC LIMIT 1;"));
    $answersData = mysql_query("SELECT correct FROM answers WHERE attemptNum=".$attempt['id']." AND usr='".$_SESSION['id']."'");
    $finalScore = 0;
    while($row = mysql_fetch_assoc($answersData)) 
    {
        if($row['correct'] == 1) $finalScore++;
    }
    $finalScore = round(($finalScore/mysql_num_rows($answersData)) * 100);
    $previousBest = mysql_fetch_assoc(mysql_query("SELECT score FROM attempts WHERE usr=".$_SESSION['id']." AND examNum=".$_GET['exam']." AND primary=1 LIMIT 1;"));
    if($previousBest['score'] >= $finalScore) $bestScore = 0;
    else $bestScore = 1;
    mysql_query("UPDATE attempts SET score=".$finalScore.", primary=".$bestScore." WHERE usr='".$_SESSION['id']."' AND examNum='".$_GET['exam']."' AND score=-1");
    unset($_SESSION['questionsID']);
    header("Location: grade?attempt=".$attempt['id']);
    exit();
}

// FUNCTION NOT YET FUNCTIONAL
function updateCourse() {
    $courseScore = mysql_query("SELECT score FROM attempts WHERE usr=".$_SESSSION['id']." AND examNum=".$_GET['exam']);
    $scoreTotal;
    while($row = mysql_fetch_assoc($courseScore)) 
    {
        $scoreTotal += $row['score'];
    }
    $scoreTotal = $scoreTotal/(100 * mysql_num_rows($courseScore));
    if(mysql_query("SELECT * FROM scores WHERE course=".$course['course'])) {
        mysql_query("UPDATE scores SET score=".$scoreTotal." WHERE usr=".$_SESSION['id']." AND course=".$course['course']);
    }
    else {
        mysql_query("INSERT INTO scores (course,usr,score) VALUES (".$course['course'].", ".$_SESSION['id'].", ".$scoreTotal.")");
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<?php include_once("templates/htmlHeader.php") ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
</head>
<body>
    
<div id="header">
    <a href="main" id="title">Dewey Hafta Academy</a>
    
    <?php include_once("templates/login.php"); ?>
</div>
    
<?php include_once("templates/menu.php"); ?>
    
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
        <form action="" method="post">
        <?php
            if($_GET['question']) {
                
                $questionData = mysql_fetch_assoc(mysql_query("SELECT question,option1,option2,option3,option4,option5 FROM questions WHERE examNum=".$_GET['exam']." AND id=".$_SESSION['questionsID'][$_GET['question']]." AND public=1 ORDER BY RAND()"));
                $optionNum = mysql_fetch_assoc(mysql_query("SELECT option FROM answers WHERE usr=".$_SESSION['id']." AND questionNum=".$_SESSION['questionsID'][$_GET['question']]." AND attemptNum=".$_SESSION['attemptNum']));
                
                echo "<p>".$questionData['question']."</p><p><span id='option'>";
                if($questionData['option1']) { 
                    echo "<input type='radio' name='option' value='".$questionData['option1']."' "; 
                    if($optionNum['option'] == 1) {
                        echo 'checked';
                    } 
                    echo "/><label for='".$questionData['option1']."'>".$questionData['option1']."</label><br>";
                }
                if($questionData['option2']) { 
                    echo "<input type='radio' name='option' value='".$questionData['option2']."' "; 
                    if($optionNum['option'] == 2) {
                        echo 'checked';
                    } 
                    echo "/><label for='".$questionData['option2']."'>".$questionData['option2']."</label><br>";
                }
                if($questionData['option3']) { 
                    echo "<input type='radio' name='option' value='".$questionData['option3']."' "; 
                    if($optionNum['option'] == 3) {
                        echo 'checked';
                    } 
                    echo "/><label for='".$questionData['option3']."'>".$questionData['option3']."</label><br>";
                }
                if($questionData['option4']) { 
                    echo "<input type='radio' name='option' value='".$questionData['option4']."' "; 
                    if($optionNum['option'] == 4) {
                        echo 'checked';
                    } echo "/><label for='".$questionData['option4']."'>".$questionData['option4']."</label><br>";
                }
                if($questionData['option5']) { 
                    echo "<input type='radio' name='option' value='".$questionData['option5']."' "; 
                    if($optionNum['option'] == 5) {
                        echo 'checked';
                    } 
                    echo "/><label for='".$questionData['option5']."'>".$questionData['option5']."</label><br>";
                }
                echo "</span></p>";
            } 
            else if($_GET['review'] == 1) 
            {
                echo "<p>This is a review.</p><input type='submit' name='submit' value='Submit' />";
            } 
            else if($attempt['score'] == -1) 
            {
                echo "<p>Your previous attempt on this exam is incomplete. Click below to continue your current attempt.</p> <br> <input type='submit' name='continue_attempt' value='Continue' />";
            } 
            else 
            {
                echo "<p>You are about to begin an exam.</p>";
                if($examData['attempts'] != 0) echo $examData['attempts']." attempts remaining.<br>";
                if($examData['time_limit'] != 0) echo "Time limit: ".$examData['time_limit']." minutes.<br>";
                else echo "No time limit.<br>";
                echo "<br><input type='submit' name='begin' value='Begin' />";
            }
        ?>
            <?php if($_GET['question']) echo '<input type="submit" name="continue" value="Continue" /> <input type="submit" name="submit" value="Submit" />'; ?>
        </form>
        
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
</script>
    
</body>
</html>
