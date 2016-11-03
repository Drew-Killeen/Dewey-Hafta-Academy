<?php

require 'templates/header.php'; 

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
        <?php
        if(!$_GET['attempt']) {
            $course =  mysql_fetch_assoc(mysql_query("SELECT enrollment FROM dewey_members WHERE id='{$_SESSION['id']}'"));
            $exams = mysql_query("SELECT id,module,title FROM exams WHERE course='".$course['enrollment']."' ORDER BY  `exams`.`module` ASC ");
            if (mysql_num_rows($exams) > 0) {
                while($row1 = mysql_fetch_assoc($exams)) {
                    echo "<h3>Module ".$row1['module'].": ".$row1['title']."</h3>";
                    $attempts = mysql_query("SELECT id,score FROM attempts WHERE usr=".$_SESSION['id']." AND examNum=".$row1['id']);
                    if (mysql_num_rows($attempts) > 0) {
                        echo "<ul>";
                        for($i = 1; $row2 = mysql_fetch_assoc($attempts); $i++) {
                            echo "<li>Attempt ".$i.": ".$row2['score']."% (<a href='grade?attempt=".$row2['id']."'>review</a>)</li>";
                        }
                        echo "</ul>";
                    }
                    else {
                        echo "Not attempts yet.";
                    }
                }
            }
            else {
                echo "0 results";
            }
        }
        else {
            $answers = mysql_query("SELECT * FROM answers WHERE attemptNum=".$_GET['attempt']);
            $score = mysql_fetch_assoc(mysql_query ("SELECT score FROM attempts WHERE id=".$_GET['attempt']));
            echo "<h4>Score: ".$score['score']."%</h4>";
            echo "<ul>";
            for($i = 1; $row = mysql_fetch_assoc($answers); $i++) {
                if($row['correct'] == 1) $correct = "<span class='green'>correct</span>";
                else $correct = "<span class='red'>incorrect</span>";
                echo "<li>Question ".$i.": ".$correct."</li>";
            }
            echo "</ul>";
        }
        ?>
    <?php
	   endif;
    ?>
    </div>
</div>


</body>
</html>
