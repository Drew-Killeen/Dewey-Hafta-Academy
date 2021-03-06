<?php 

require 'scripts/header.php'; 

if($_SESSION['id'] && $_SESSION['privilege'] != 'unapproved') {
    $enrollment =  mysqli_fetch_assoc(mysqli_query($link, "SELECT enrollment FROM dewey_members WHERE id=".$_SESSION['id']));
    if($enrollment['enrollment']) {
        $course = mysqli_fetch_assoc(mysqli_query($link, "SELECT id,course FROM courses WHERE id=".$enrollment['enrollment']));
    }
    else {
        header("Location: enroll");
        exit();
    }
}

?>

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
    <?php if(!$_SESSION['id']): ?>
        <!--Unauthorized-->
        <p>This site is for members only. Click <a href="sign_in">here</a> to login. To apply for a membership, please visit the <a href="sign_up">sign up</a> page.</p>
   <?php elseif($_SESSION['privilege'] == 'unapproved'): ?>
        <p>Your account must be approved before you can enroll in a course.</p>
    <?php
        else:
    ?>
        <!--Authorized-->
        <?php
        if($course) { 
            $exams = mysqli_query($link, "SELECT id,module,title FROM exams WHERE course=".$course['id']." ORDER BY  `exams`.`module` ASC");
            if (mysqli_num_rows($exams) > 0) 
            {
                echo "<h3>Exams available for ".$course['course']."</h3>";
                echo "<ul>";
                // output data of each row
                while($row = mysqli_fetch_assoc($exams)) {
                    $score = mysqli_fetch_assoc(mysqli_query($link, "SELECT id,score FROM attempts WHERE examNum='".$row['id']."' AND usr='".$_SESSION['id']."' ORDER BY score DESC LIMIT 1;"));
                    echo "<li><a href='exam?exam=".$row['id']."'>Module ".$row['module'].": ".$row['title']."</a>";
                    if($score && $score['score'] >= 0) echo " - <a href='grade?attempt=".$score['id']."' class='whiteLink'>".$score['score']."%</a>";
                    echo "</li>";
                }
                echo "</ul>";
            } else 
            {
                echo "0 results";
            }
        }
            
        ?>
        
    <?php
	   endif;
    ?>
    </div>
</div>

<?php require 'scripts/jsload.php'; ?>

</body>
</html>
