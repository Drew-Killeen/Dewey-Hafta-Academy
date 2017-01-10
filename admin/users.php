<?php 

require '../scripts/header.php'; 
require '../scripts/functions.php';

if($_POST['update']=='Update')
{
    // If the form has been submitted
    
    mysqli_query($link, "UPDATE dewey_members SET privilege='".$_POST['privilege']."' WHERE usr='".$_GET['user']."'");
    
    $userData = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM dewey_members WHERE usr='".$_GET['user']."'"));
    
    if($userData['privilege'] == 'unapproved') {    
    send_mail('noreply@deweyhaftaacademy.x10host.com',
              $userData['email'],
              'Registration System - Your Password',
              "Your account has been approved. You can now use your account to enroll in a course."
             );
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<?php include_once("../templates/htmlHeader.php") ?>
</head>
<body>
    
<?php 
    include_once("../templates/login.php"); 
    include_once("../templates/menu.php"); 
?>
    
<div id="main">
    <div class="container">
    <?php
        if(!$_SESSION['id'] || ($_SESSION['privilege'] != admin && $_SESSION['privilege'] != sysop)):
    ?>
        <!--Unauthorized-->
        <p>You do not have sufficient privileges to access this page.</p>
    <?php
        else:
            echo "<h1>Administrator Control Panel</h1>";
            if(!$_GET['user']):
    ?>
        <!--Authorized-->
        <h3>Unapproved Users</h3>
        <?php
        $unapproved_users = mysqli_query($link, "SELECT id,usr FROM dewey_members WHERE privilege='unapproved'");
        if (mysqli_num_rows($unapproved_users) > 0) 
        {
            echo "<ul>";
            // output data of each row
            while($row = mysqli_fetch_assoc($unapproved_users)) {
                echo "<li><a href='users?user=".$row['id']."'>".$row['usr']."</a></li>";
            }
            echo "</ul>";
        } else 
        {
            echo "0 results";
        }
        ?>
        <h3>Students</h3>
        <?php
        $students = mysqli_query($link, "SELECT id,usr FROM dewey_members WHERE privilege='student'");
        if (mysqli_num_rows($students) > 0) 
        {
            echo "<ul>";
            // output data of each row
            while($row = mysqli_fetch_assoc($students)) {
                echo "<li><a href='users?user=".$row['id']."'>".$row['usr']."</a></li>";
            }
            echo "</ul>";
        } else 
        {
            echo "0 results";
        }
        ?>
        <h3>Teachers</h3>
        <?php
        $teachers = mysqli_query($link, "SELECT id,usr FROM dewey_members WHERE privilege='teacher'");
        if (mysqli_num_rows($teachers) > 0) 
        {
            echo "<ul>";
            // output data of each row
            while($row = mysqli_fetch_assoc($teachers)) {
                echo "<li><a href='users?user=".$row['id']."'>".$row['usr']."</a></li>";
            }
            echo "</ul>";
        } else 
        {
            echo "0 results";
        }
        ?>
        <h3>Administrators</h3>
        <?php
        $admins = mysqli_query($link, "SELECT id,usr FROM dewey_members WHERE privilege='admin'");
        if (mysqli_num_rows($admins) > 0) 
        {
            echo "<ul>";
            // output data of each row
            while($row = mysqli_fetch_assoc($admins)) {
                echo "<li><a href='users?user=".$row['id']."'>".$row['usr']."</a></li>";
            }
            echo "</ul>";
        } else 
        {
            echo "0 results";
        }
        ?>
        <h3>Sysops</h3>
        <?php
        $sysops = mysqli_query($link, "SELECT id,usr FROM dewey_members WHERE privilege='sysop'");
        if (mysqli_num_rows($sysops) > 0) 
        {
            echo "<ul>";
            // output data of each row
            while($row = mysqli_fetch_assoc($sysops)) {
                echo "<li><a href='users?user=".$row['id']."'>".$row['usr']."</a></li>";
            }
            echo "</ul>";
        } else 
        {
            echo "0 results";
        }
        
            else:
                $user = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM dewey_members WHERE id=".$_GET['user']));
                if($user['enrollment'] != 0) {$enrollment = mysqli_fetch_assoc(mysqli_query($link, "SELECT course FROM courses WHERE id=".$user['enrollment']));}
                echo 
                    "<h3><a href='users'>&larr;</a> Editing user: ".$user['usr']."</h3>
                    <ul>
                        <li>Member since ".date("F jS, Y", strtotime($user['dt']))."</li>";
                        if($user['enrollment'] != 0) {echo "<li>Currently enrolled in ".$enrollment['course']."</li>";}
                        echo "<li>Email address: ".$user['email']."</li>
                        <li><a href='../students?id=".$_GET['user']."'>View grades</a></li>
                    </ul>
                    <h4>Permissions</h4>
                    <form action='' method='post'>
                        <p>
                            <input type='radio' name='privilege' value='unapproved'><label for='unapproved'>Unapproved</label></input><br>
                            <input type='radio' name='privilege' value='student'><label for='student'>Student</label></input><br>
                            <input type='radio' name='privilege' value='teacher'><label for='teacher'>Teacher</label></input><br>
                            <input type='radio' name='privilege' value='admin'><label for='admin'>Administrator</label></input><br>
                            <input type='radio' name='privilege' value='sysop'><label for='sysop'>Sysop</label></input><br>
                        </p>
                        <input type='submit' name='update' value='Update' />
                    </form>";
            endif;
        endif; 
    ?>
    </div>
</div>

<?php require '../scripts/jsload.php'; ?>
    
</body>
</html>