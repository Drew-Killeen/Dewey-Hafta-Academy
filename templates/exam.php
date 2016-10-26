<?php 

require 'templates/header.php'; 

$course =  mysql_fetch_assoc(mysql_query("SELECT enrollment FROM dewey_members WHERE id='{$_SESSION['id']}'"));
$exam = $_GET['exam'];
$temp = mysql_fetch_assoc(mysql_query("SELECT id,title FROM exams WHERE course='".$course['enrollment']."' AND module='".$exam."'"));
$examNum = $temp["id"];
$examTitle = $temp["title"];
if($_GET['question']) $questionNum = $_GET['question']+1;
else $questionNum = 1;

$_SESSION['examData'] = mysql_query ("SELECT * FROM questions WHERE examNum='".$examNum."' ORDER BY RAND()");

$_SESSION['msg']['questions'] = "<p>You are about to begin an exam. You have 0 attempts remaining. If there is a time limit, the countdown will begin as soon as your press continue. Blah, blah, blah.</p>";


if($_POST['continue'] == 'Continue') {
    $_SESSION['question'.$_GET['question']] = $_POST['option'];
    header("Location: exam?exam=".$exam."&question=".$questionNum);
}

if($_POST['submit'] == 'Submit') {
    unset($_SESSION['questions']);
}

if(!$_GET['question']) {
    $i = 1;
    // output data of each row
    while($row = mysql_fetch_assoc($_SESSION['examData'])) {
        $_SESSION['questions'][$i] = $row['question'];
        if($row['option1']) $_SESSION['option1'][$i] = $row['option1'];
        if($row['option2']) $_SESSION['option2'][$i] = $row['option2'];
        if($row['option3']) $_SESSION['option3'][$i] = $row['option3'];
        if($row['option4']) $_SESSION['option4'][$i] = $row['option4'];
        if($row['option5']) $_SESSION['option5'][$i] = $row['option5'];
        $i++;
    }
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
        <?php echo "<h2>Module ".$examNum.": ".$examTitle."</h2>"; ?>
        <form action="" method="post">
        <?php
            if($_GET['question']) {
            echo "<b>".$_SESSION['questions'][$_GET['question']]."</b><br>
                <input type='radio' name='option' value='".$_SESSION['option1'][$_GET['question']]."'/><label for='".$_SESSION['option1'][$_GET['question']]."'>".$_SESSION['option1'][$_GET['question']]."</label><br>
                <input type='radio' name='option' value='".$_SESSION['option2'][$_GET['question']]."'/><label for='".$_SESSION['option2'][$_GET['question']]."'>".$_SESSION['option2'][$_GET['question']]."</label><br>
                <input type='radio' name='option' value='".$_SESSION['option3'][$_GET['question']]."'/><label for='".$_SESSION['option3'][$_GET['question']]."'>".$_SESSION['option3'][$_GET['question']]."</label><br>
                <input type='radio' name='option' value='".$_SESSION['option4'][$_GET['question']]."'/><label for='".$_SESSION['option4'][$_GET['question']]."'>".$_SESSION['option4'][$_GET['question']]."</label><br>
                <input type='radio' name='option' value='".$_SESSION['option5'][$_GET['question']]."'/><label for='".$_SESSION['option5'][$_GET['question']]."'>".$_SESSION['option5'][$_GET['question']]."</label><br>";
            }
            else {
                echo $_SESSION['msg']['questions'];
            }
        ?>
        
            <input type="submit" name="continue" value="Continue" />
            <?php if($_GET['question']) echo '<input type="submit" name="submit" value="Submit" />'; ?>
        </form>
        
    <?php
	   endif;
    ?>
    </div>
</div>


</body>
</html>
