<?php require 'scripts/header.php'; ?>

<!DOCTYPE html>
<html>
<head>

<?php include_once("templates/htmlHeader.php") ?>

</head>
<body>
    
<?php
    if($_GET['action'] != 'print') {
        include_once("templates/login.php"); 
        include_once("templates/menu.php"); 
    }
    else {
        echo "<h1>Dewey Hafta Academy</h1>";
    }
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
        <?php
        if(!$_GET['attempt']) {
            $course =  mysqli_fetch_assoc(mysqli_query($link, "SELECT enrollment FROM dewey_members WHERE id='{$_SESSION['id']}'"));
            if(!$course['enrollment']) { header("Location: enroll"); exit(); }
            $exams = mysqli_query($link, "SELECT id,module,title FROM exams WHERE course='".$course['enrollment']."' ORDER BY  `exams`.`module` ASC ");
            if (mysqli_num_rows($exams) > 0) {
                while($row1 = mysqli_fetch_assoc($exams)) {
                    echo "<h3>Module ".$row1['module'].": ".$row1['title']."</h3>";
                    $attempts = mysqli_query($link, "SELECT id,score FROM attempts WHERE usr=".$_SESSION['id']." AND examNum=".$row1['id']);
                    if (mysqli_num_rows($attempts) > 0) {
                        echo "<ul>";
                        for($i = 1; $row2 = mysqli_fetch_assoc($attempts); $i++) {
                            echo "<li>Attempt ".$i.": ";
                            if($row2['score'] != -1) {
                                echo $row2['score']."% (<a href='grade?attempt=".$row2['id']."'>review</a>)</li>";
                            }
                            else {
                                echo "<i>incomplete</i>";
                            }
                        }
                        echo "</ul>";
                    }
                    else {
                        echo "No attempts yet.";
                    }
                }
            }
            else {
                echo "0 results";
            }
        }
        else {
            $score = mysqli_fetch_assoc(mysqli_query ($link, "SELECT examNum,usr,score FROM attempts WHERE id=".$_GET['attempt']));
            if($_SESSION['id'] != $score['usr'] && ($_SESSION['privilege'] != 'admin' && $_SESSION['privilege'] != 'sysop')) {
                echo "You do not have permission to view this attempt.";
            }
            else {
                $answers = mysqli_query($link, "SELECT questionNum,correct,option FROM answers WHERE attemptNum=".$_GET['attempt']);
                $correctNum = mysqli_query($link, "SELECT id FROM answers WHERE attemptNum=".$_GET['attempt']." AND correct=1");
                $examInfo = mysqli_fetch_assoc(mysqli_query($link, "SELECT module,title FROM exams WHERE id=".$score['examNum']));
                echo "<h1>Attempt for Module ".$examInfo['module'].": ".$examInfo['title']."</h1><span class='attempt-score'><b>Score:</b> ".mysqli_num_rows($correctNum)." out of ".mysqli_num_rows($answers)." - ".$score['score']."%</span><span class='attempt-time'>--time--</span><hr>";
                echo "<div class='answers-list'>";
                for($i = 1; $row = mysqli_fetch_assoc($answers); $i++) {
                    $questions = mysqli_fetch_assoc(mysqli_query($link, "SELECT question,option1,option2,option3,option4,option5 FROM questions WHERE id=".$row['questionNum']));
                    if($row['correct'] == 1) $correct = "<span class='green'>correct</span>";
                    else if($row['correct'] == 2) $correct = "<span class='red'>unanswered</span>";
                    else $correct = "<span class='red'>incorrect</span>";
                    echo "<h3>Question ".$i.": ".$correct."</h3>
                        <div class='answers-item'><b>".$questions['question']."</b><br><label><input type='radio' disabled ";
                    if($row['option'] == 1) echo "checked";
                        echo "/>".$questions['option1']."</label><br><label><input type='radio' disabled ";
                    if($row['option'] == 2) echo "checked";
                        echo "/>".$questions['option2']."</label><br>";
                        if($questions['option3']) {
                            echo "<label><input type='radio' disabled ";
                            if($row['option'] == 3) echo "checked";
                            echo "/>".$questions['option3']."</label><br>";
                        }
                        if($questions['option4']) {
                            echo "<label><input type='radio' disabled ";
                            if($row['option'] == 4) echo "checked";
                            echo "/>".$questions['option4']."</label><br>";
                        }
                        if($questions['option5']) {
                            echo "<label><input type='radio' disabled ";
                            if($row['option'] == 5) echo "checked";
                            echo "/>".$questions['option5']."</label><br>";
                        }
                    echo "</div>";
                }
                echo "</div>";
            }
        }
        ?>
    <?php
	   endif;
    ?>
    </div>
</div>

<?php 
    require 'scripts/jsload.php'; 
    
    if($_GET['action'] == 'print') {
        echo "<script>window.print();</script>";
    }
?>
    
</body>
</html>
