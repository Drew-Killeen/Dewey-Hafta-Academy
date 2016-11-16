<?php 

require 'templates/header.php'; 

$course =  mysql_fetch_assoc(mysql_query("SELECT enrollment FROM dewey_members WHERE id='{$_SESSION['id']}'"));

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
        elseif($_SESSION['privilege'] == 'unapproved'):
    ?>
        <p>Your account must be approved before you can enroll in a course.</p>
    <?php
        else:
    ?>
        <!--Authorized-->
        <?php
        
        echo "<h3>Exams available for ".$course['enrollment']."</h3>";

        $exams = mysql_query("SELECT id,module,title FROM exams WHERE course='".$course['enrollment']."' ORDER BY  `exams`.`module` ASC ");
        if (mysql_num_rows($exams) > 0) 
        {
            echo "<ul>";
            // output data of each row
            while($row = mysql_fetch_assoc($exams)) {
                $score = mysql_fetch_assoc(mysql_query ("SELECT id,score FROM attempts WHERE examNum='".$row['id']."' AND usr='".$_SESSION['id']."' ORDER BY score DESC LIMIT 1;"));
                echo "<li><a href='exam?exam=".$row['id']."'>Module ".$row['module'].": ".$row['title']."</a>";
                if($score) echo " - <a href='grade?attempt=".$score['id']."' class='whiteLink'>".$score['score']."%</a>";
                echo "</li>";
            }
            echo "</ul>";
        } else 
        {
            echo "0 results";
        }
            
        ?>
        
    <?php
	   endif;
    ?>
    </div>
</div>


</body>
</html>
