<?php require 
    
    '../scripts/header.php'; 

if($_POST['update']=='Update')
{   
    mysql_query("UPDATE courses SET public=".$_POST['public']." WHERE id='".$_GET['course']."'");
    $_SESSION['msg']['success']='Course updated';
}

?>

<!DOCTYPE html>
<html>
<head>

<?php include_once("../templates/htmlHeader.php") ?>
    
<script>    
    function deleteCourse() {
        $confirm = confirm("Are you sure you want to delete this course? This cannot be undone.");
            if($confirm === true) {
            var xhttp = new XMLHttpRequest();
            <?php echo 'xhttp.open("GET", "../scripts/delete.php?course='.$_GET['course'].'", true);'; ?>
            xhttp.send();
            window.location.assign('http://www.deweyhaftaacademy.x10host.com/admin/edit_course');
        }
    }
</script>
    
</head>
<body>
    
<div id="header">
    <a href="../main" id="title">Dewey Hafta Academy</a>
    <?php include_once("../templates/login.php"); ?>
</div>
    
<?php include_once("../templates/menu.php"); ?>
    
<div id="main">
    <div class="container">
    <?php
        if(!$_SESSION['id'] || ($_SESSION['privilege'] != admin && $_SESSION['privilege'] != sysop)):
    ?>
        <!--Unauthorized-->
        <p>You do not have sufficient privileges to access this page.</p>
    <?php
        else:
    ?>
        <!--Authorized-->
        <h1>Administrator Control Panel</h1>
            <?php
                if(!$_GET['course']) {
                    echo "<ul>";
                    $courses = mysql_query ("SELECT id,course FROM courses");
                    if (mysql_num_rows($courses) > 0) 
                    {
                        // output data of each row
                        while($row = mysql_fetch_assoc($courses)) {
                           echo "<li><a href='edit_course?course=".$row['id']."'>".$row['course']."</a></li>";
                    }
                    } else 
                    {
                        echo "<li>0 results</li>";
                    }
                    echo "</ul><br><input type='button' onclick='location.href=\"create_course\";' value='New'/>";
                } else
                {
                    $courseData = mysql_fetch_assoc(mysql_query("SELECT * FROM courses WHERE id='".$_GET['course']."'"));
                    echo "<h3><a href='edit_course'>&larr;</a> Editing ".$courseData['course']."</h3>
                    <form action='' method='post'><p><b>Public?</b> <input type='radio' name='public' value='1' ";
                    if($courseData['public'] == 1) {echo "checked ";} 
                    echo "><label>Yes</label></input> <input type='radio' name='public' value='0' ";
                    if($courseData['public'] == 0) {echo "checked ";} 
                    echo "><label>No</label></input><br><h3>Exams available</h3><ul>";
                        
                    $exams = mysql_query("SELECT id,module,title FROM exams WHERE course='".$courseData['course']."' ORDER BY module ASC");
                    while($row = mysql_fetch_assoc($exams)) {
                        $questionsNum = mysql_query("SELECT id FROM questions WHERE examNum=".$row['id']);
                        echo "<li><a href='edit_exam?exam=".$row['id']."&source=edit_course&get=".$_GET['course']."'>Module ".$row['module'].": ".$row['title']."</a>";
                        if(mysql_num_rows($questionsNum) > 0) echo "<i> - ".mysql_num_rows($questionsNum)." questions</i>";
                        echo "</li>";
                    }
                    
                    echo "</ul></p><p><input type='submit' name='update' value='Update' /> <input type='submit' onclick='deleteCourse();' name='delete' value='Delete' /></p></form>";
                }
            ?>
    <?php
	   endif;
    ?>
    </div>
</div>

<?php require '../scripts/jsload.php'; ?>
    
</body>
</html>