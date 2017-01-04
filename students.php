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
            echo "<h2>Teacher Portal</h2>";
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
            echo "--placeholder--";
        }
        
	   endif;
    ?>
    </div>
</div>
    
<?php require 'scripts/jsload.php'; ?>

</body>
</html>
