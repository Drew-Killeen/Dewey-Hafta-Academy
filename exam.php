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

if($_POST['continue'] == 'Continue') {
    /* When continue button is pressed */
    $_SESSION['question'][$_GET['question']] = $_POST['option'];
    header("Location: exam?exam=".$exam."&question=".$questionNum);
}

if($_POST['submit'] == 'Submit') {
    /* When exam is submitted */
    header("Location: grade");
}

if(!$_GET['question']) {
    $i = 1;
    // output data of each row
    while($row = mysql_fetch_assoc($_SESSION['examData'])) {
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
            }
            else {
                echo "<p>You are about to begin an exam. You have 0 attempts remaining. If there is a time limit, the countdown will begin as soon as your press continue. Blah, blah, blah.</p>";
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
