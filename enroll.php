<?php 

require 'scripts/header.php'; 

if($_POST['submit']=='Submit')
{
    // If the form has been submitted
    
    mysqli_query($link, "UPDATE dewey_members SET enrollment='".$_POST['course']."' WHERE id='".$_SESSION['id']."'");

    $_SESSION['msg']['update-success']='Enrollment successfully updated.';
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
        <?php if($_GET['past_enroll'] != 1): ?>
            <h2>Course Enrollment</h2>
            <?php
                $current_data = mysqli_fetch_assoc(mysqli_query($link, "SELECT enrollment FROM dewey_members WHERE id='{$_SESSION['id']}'"));
                if(empty($current_data['enrollment']))
                {
                    echo "<p>You are not currently enrolled in any courses. Select one below.</p>";
                } else 
                {
                    $enrollmentName = mysqli_fetch_assoc(mysqli_query($link, "SELECT course FROM courses WHERE id=".$current_data['enrollment']));
                    echo "<p>You are currently enrolled in ".$enrollmentName['course'].".</p>";
                }
            ?>

            <p>Choose a course to update your enrollment. You can only be enrolled in one course at a time. To see past enrollments, click <a href='?past_enroll=1'>here</a>.</p>
            <form action="" method="post"><p>
                <?php                    
                    $courses = mysqli_query($link, "SELECT id,course FROM courses WHERE public=1");
                    if (mysqli_num_rows($courses) > 0) 
                    {
                        // output data of each row
                        while($row = mysqli_fetch_assoc($courses)) {
                           echo "<input class='field' type='radio' name='course' value='".$row['id']."'/><label class='grey' for='".$row["course"]."'>".$row["course"]."</label><br>";
                    }
                    } else 
                    {
                        echo "0 results";
                    }
                ?>
                </p><input type="submit" name="submit" value="Submit"/>
            </form>
        
        <?php 
            else: 
        ?>
        
            <h2>Past Enrollments</h2>
            <?php 
                $pastCourses = mysqli_query($link, "SELECT course FROM scores WHERE usr=".$_SESSION['id']);
            ?>
        
        <?php 
            endif; 
        ?>
    <?php
	   endif;
    ?>
    </div>
</div>

    <?php require 'scripts/jsload.php'; ?>

</body>
</html>
