<?php 

require 'templates/header.php'; 

if(!$_GET['exam']) {
    header("Location: course");
}

$course =  mysql_fetch_assoc(mysql_query("SELECT enrollment FROM dewey_members WHERE id='{$_SESSION['id']}'"));
$exam = $_GET['exam'];
$temp = mysql_fetch_assoc(mysql_query("SELECT id,title,module FROM exams WHERE course='".$course['enrollment']."' AND id='".$exam."'"));
$examNum = $temp["id"];
$examModule = $temp['module'];
$examTitle = $temp["title"];

$attempt = mysql_fetch_assoc(mysql_query("SELECT * FROM attempts WHERE usr='".$_SESSION['usr']."' AND examNum='".$exam."' ORDER BY id DESC LIMIT 1;"));
    
if($_GET['question']) $questionNum = $_GET['question']+1;
else $questionNum = 1;

if(!$_SESSION['examData']) $_SESSION['examData'] = mysql_query ("SELECT * FROM questions WHERE examNum='".$examNum."' AND public=1 ORDER BY RAND()");

if($_POST['continue'] == 'Continue') {
    /* When continue button is pressed */
    $_SESSION['question'][$_GET['question']] = $_POST['option'];
    $questionCorrect = mysql_fetch_assoc(mysql_query("SELECT id,option1 FROM questions WHERE question='".$_SESSION['questions'][$_GET['question']]."'"));
    if($_POST['option'] == $questionCorrect['option1']) $correct = 1;
    else $correct = 0;
    $attemptNum = $attempt['id'] + 1;
    mysql_query("UPDATE answers SET correct=".$correct." WHERE questionNum=".$questionCorrect['id']." AND attemptNum=".$attempt['id']);
    header("Location: exam?exam=".$exam."&question=".$questionNum);
}

if($_POST['begin'] == 'Begin') {
    /* When exam begins */
    mysql_query("INSERT INTO attempts (examNum,usr,score) VALUES (".$_GET['exam'].", '".$_SESSION['usr']."', -1)");
    $_SESSION['attemptNum'] = mysql_fetch_assoc(mysql_query("SELECT id FROM attempts WHERE usr='".$_SESSION['usr']."' AND examNum=".$_GET['exam']." ORDER BY id DESC LIMIT 1;"));
    $i = 1;
    // output data of each row
    while($row = mysql_fetch_assoc($_SESSION['examData'])) {
        $attemptNum = $attempt['id'] + 1;
        mysql_query("INSERT INTO answers (questionNum, attemptNum, correct, usr) VALUES (".$row['id'].", ".$attemptNum.", 2, '".$_SESSION['usr']."')");
        $_SESSION['options'][$i] = array();
        $_SESSION['questions'][$i] = $row['question'];
        if($row['option1']) array_push($_SESSION['options'][$i], $row['option1']);
        if($row['option2']) array_push($_SESSION['options'][$i], $row['option2']);
        if($row['option3']) array_push($_SESSION['options'][$i], $row['option3']);
        if($row['option4']) array_push($_SESSION['options'][$i], $row['option4']);
        if($row['option5']) array_push($_SESSION['options'][$i], $row['option5']);
        shuffle($_SESSION['options'][$i]);
        $i++;
    }
    header("Location: exam?exam=".$exam."&question=1");
}

if($_POST['submit'] == 'Submit') {
    /* When exam is submitted */
    mysql_query("UPDATE attempts SET score=0 WHERE usr='".$_SESSION['usr']."' AND examNum='".$exam."' AND score=-1");
    unset($_SESSION['questions']);
    unset($_SESSION['question']);
    unset($_SESSION['options']);
    unset($_SESSION['examData']);
    header("Location: grade?attempt=".$attempt['id']);
}
?>

<!DOCTYPE html>
<html>
<head>

<?php include_once("templates/htmlHeader.php") ?>

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
        else:
    ?>
        <!--Authorized-->
        <?php echo "<h2>Module ".$examModule.": ".$examTitle."</h2>"; ?>
        <form action="" method="post">
        <?php
            if($_GET['question']) {
                echo "<b>".$_SESSION['questions'][$_GET['question']]."</b><br>";
                if($_SESSION['options'][$_GET['question']][0]) { 
                    echo "<input type='radio' name='option' value='".$_SESSION['options'][$_GET['question']][0]."' "; 
                    if($_SESSION['question'][$_GET['question']] == $_SESSION['options'][$_GET['question']][0]) {
                        echo 'checked';
                    } 
                    echo "/><label for='".$_SESSION['options'][$_GET['question']][0]."'>".$_SESSION['options'][$_GET['question']][0]."</label><br>";
                }
                if($_SESSION['options'][$_GET['question']][1]) { 
                    echo "<input type='radio' name='option' value='".$_SESSION['options'][$_GET['question']][1]."' "; 
                    if($_SESSION['question'][$_GET['question']] == $_SESSION['options'][$_GET['question']][1]) {
                        echo 'checked';
                    } 
                    echo "/><label for='".$_SESSION['options'][$_GET['question']][1]."'>".$_SESSION['options'][$_GET['question']][1]."</label><br>";
                }
                if($_SESSION['options'][$_GET['question']][2]) { 
                    echo "<input type='radio' name='option' value='".$_SESSION['options'][$_GET['question']][2]."' "; 
                    if($_SESSION['question'][$_GET['question']] == $_SESSION['options'][$_GET['question']][2]) {
                        echo 'checked';
                    } 
                    echo "/><label for='".$_SESSION['options'][$_GET['question']][2]."'>".$_SESSION['options'][$_GET['question']][2]."</label><br>";
                }
                if($_SESSION['options'][$_GET['question']][3]) { 
                    echo "<input type='radio' name='option' value='".$_SESSION['options'][$_GET['question']][3]."' "; 
                    if($_SESSION['question'][$_GET['question']] == $_SESSION['options'][$_GET['question']][3]) {
                        echo 'checked';
                    } echo "/><label for='".$_SESSION['options'][$_GET['question']][3]."'>".$_SESSION['options'][$_GET['question']][3]."</label><br>";
                }
                if($_SESSION['options'][$_GET['question']][4]) { 
                    echo "<input type='radio' name='option' value='".$_SESSION['options'][$_GET['question']][4]."' "; 
                    if($_SESSION['question'][$_GET['question']] == $_SESSION['options'][$_GET['question']][4]) {
                        echo 'checked';
                    } 
                    echo "/><label for='".$_SESSION['options'][$_GET['question']][4]."'>".$_SESSION['options'][$_GET['question']][4]."</label><br>";
                }
            } else if($attempt['score'] == -1) 
            {
                echo "<p>Your previous attempt on this exam is incomplete. Click below to continue your current attempt.</p> <br> <input type='submit' name='continue' value='Continue' />";
            } else 
            {
                echo "<p>You are about to begin an exam. You have 0 attempts remaining. If there is a time limit, the countdown will begin as soon as your press continue. Blah, blah, blah.</p> <br> <input type='submit' name='begin' value='Begin' />";
            }
        ?>
            <?php if($_GET['question']) echo '<input type="submit" name="continue" value="Continue" /> <input type="submit" name="submit" value="Submit" />'; ?>
        </form>
        
    <?php
	   endif;
    ?>
    </div>
</div>


</body>
</html>
