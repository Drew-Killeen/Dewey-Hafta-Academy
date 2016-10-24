<?php 

require 'templates/header.php'; 

$course =  mysql_fetch_assoc(mysql_query("SELECT enrollment FROM dewey_members WHERE id='{$_SESSION['id']}'"));
$exam = $_GET['exam'];
$temp = mysql_fetch_assoc(mysql_query("SELECT id,title FROM exams WHERE course='".$course['enrollment']."' AND module='".$exam."'"));
$examNum = $temp["id"];
$examTitle = $temp["title"];

        $questions = mysql_query ("SELECT * FROM questions WHERE examNum='".$examNum."' ORDER BY RAND()");
        if (mysql_num_rows($questions) > 0) 
        {
            // output data of each row
            while($row = mysql_fetch_assoc($questions)) {
               echo $questions['question'];
                if($questions['option1']) echo $questions['option1'];
                if($questions['option2']) echo $questions['option2'];
                if($questions['option3']) echo $questions['option3'];
                if($questions['option4']) echo $questions['option4'];
                if($questions['option5']) echo $questions['option5'];
            }
        } else 
        {
            echo "0 results";
        }

$questions = mysql_fetch_assoc(mysql_query("SELECT * FROM questions WHERE examNum='.$examNum.'"));

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
        
        <p>You are about to begin an exam. You have 0 attempts remaining. If there is a time limit, the countdown will begin as soon as your press continue. Blah, blah, blah.</p>
        
        <form action="" method="post">
            <input type="submit" name="continue" value="Continue" />
        </form>
        
    <?php
	   endif;
    ?>
    </div>
</div>


</body>
</html>
