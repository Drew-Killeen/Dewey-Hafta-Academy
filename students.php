<?php require 'scripts/header.php'; ?>

<!DOCTYPE html>
<html>
<head>

<?php include_once("templates/htmlHeader.php") ?>

</head>
<body>
    
<?php 
    include_once("templates/login.php"); 
    include_once("templates/menu.php"); 
?>
    
<div id="main">
    <div class="container">
    <?php
        if($_SESSION['privilege'] != 'teacher' && $_SESSION['privilege'] != 'admin' && $_SESSION['privilege'] != 'sysop'):
    ?>
        <!--Unauthorized-->
        <p>You do not have sufficient privileges to access this page.</p>
    <?php
        else:
        
        if(!$_GET['id']) {
            echo "<h1>Teacher Portal</h1>";
            $students = mysqli_query($link, "SELECT id,usr FROM dewey_members WHERE supervisor=".$_SESSION['id']);

            if(mysqli_num_rows($students) > 0) {
                echo "<ul>";
                while($row = mysqli_fetch_assoc($students)) {
                    echo "<li><a href='students?id=".$row['id']."'>".$row['usr']."</a></li>";
                }
                echo "</ul>";
            }
            else {
                echo "<p>You currently have no students.</p>";
            }
        }
        else if(!$_GET['attempt']) {
            $currentStudent = mysqli_fetch_assoc(mysqli_query($link, "SELECT usr,enrollment FROM dewey_members WHERE id='{$_GET['id']}'"));                
            $exams = mysqli_query($link, "SELECT id,module,title FROM exams WHERE course='".$currentStudent['enrollment']."' ORDER BY  `exams`.`module` ASC ");
            
            echo "<h2>Student progress for ".$currentStudent['usr']."</h2>";
            
            if (mysqli_num_rows($exams) > 0) {
                while($row1 = mysqli_fetch_assoc($exams)) {
                    echo "<h3>Module ".$row1['module'].": ".$row1['title']."</h3>";
                    $attempts = mysqli_query($link, "SELECT id,score FROM attempts WHERE usr=".$_GET['id']." AND examNum=".$row1['id']);
                    if (mysqli_num_rows($attempts) > 0) {
                        echo "<ul>";
                        for($i = 1; $row2 = mysqli_fetch_assoc($attempts); $i++) {
                            echo "<li>Attempt ".$i.": ";
                            if($row2['score'] != -1) {
                                echo $row2['score']."% (<a href='students?id=".$_GET['id']."&attempt=".$row2['id']."'>review</a>)</li>";
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
                echo "Course not yet started.";
            }
        }
        else {
            $score = mysqli_fetch_assoc(mysqli_query($link, "SELECT usr,score FROM attempts WHERE id=".$_GET['attempt']));
            $studentID = mysqli_query($link, "SELECT * FROM dewey_members WHERE id=".$_GET['id']." AND supervisor=".$_SESSION['id']);
            if(mysqli_num_rows($studentID) < 1 && $_SESSION['privilege'] != 'admin' && $_SESSION['privilege'] != 'sysop') {
                echo "You do not have permission to view this attempt.";
            }
            else {
                $score = mysqli_fetch_assoc(mysqli_query ($link, "SELECT usr,score FROM attempts WHERE id=".$_GET['attempt']));
                if($_SESSION['id'] != $score['usr'] && ($_SESSION['privilege'] != 'admin' && $_SESSION['privilege'] != 'sysop')) {
                    echo "You do not have permission to view this attempt.";
                }
                else {
                    $answers = mysqli_query($link, "SELECT questionNum,correct,option FROM answers WHERE attemptNum=".$_GET['attempt']);
                    echo "<h4>Score: ".$score['score']."%</h4>";
                    echo "<div class='answers-list'>";
                    for($i = 1; $row = mysqli_fetch_assoc($answers); $i++) {
                        $questions = mysqli_fetch_assoc(mysqli_query($link, "SELECT question,option1,option2,option3,option4,option5 FROM questions WHERE id=".$row['questionNum']));
                        if($row['correct'] == 1) $correct = "<span class='green'>correct</span>";
                        else if($row['correct'] == 2) $correct = "<span class='red'>unanswered</span>";
                        else $correct = "<span class='red'>incorrect</span>";
                        echo "<h3>Question ".$i.": ".$correct."</h3>
                            <div class='answers-item'><b>".$questions['question']."</b><br><input type='radio' disabled ";
                        if($row['option'] == 1) echo "checked";
                            echo "/>".$questions['option1']."<br><input type='radio' disabled ";
                        if($row['option'] == 2) echo "checked";
                            echo "/>".$questions['option2']."<br>";
                            if($questions['option3']) {
                                echo "<input type='radio' disabled ";
                                if($row['option'] == 3) echo "checked";
                                echo "/>".$questions['option3']."<br>";
                            }
                            if($questions['option4']) {
                                echo "<input type='radio' disabled ";
                                if($row['option'] == 4) echo "checked";
                                echo "/>".$questions['option4']."<br>";
                            }
                            if($questions['option5']) {
                                echo "<input type='radio' disabled ";
                                if($row['option'] == 5) echo "checked";
                                echo "/>".$questions['option5']."<br>";
                            }
                        echo "</div>";
                    }
                    echo "</div>";
                }
            }
        }
        
	   endif;
    ?>
    </div>
</div>
    
<?php require 'scripts/jsload.php'; ?>

</body>
</html>
