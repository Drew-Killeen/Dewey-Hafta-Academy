<?php require '../scripts/header.php'; ?>

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
    ?>
        <!--Authorized-->
        <h1>Administrator Control Panel</h1>
        <h2>Site Statistics</h2>
        
        <?php
        $membersData = mysqli_num_rows(mysqli_query($link, "SELECT id FROM dewey_members"));
        $examsData = mysqli_num_rows(mysqli_query($link, "SELECT id FROM exams"));
        $questionsData = mysqli_num_rows(mysqli_query($link, "SELECT id FROM questions"));
        $attemptsData = mysqli_num_rows(mysqli_query($link, "SELECT id FROM attempts"));
        $answersData = mysqli_num_rows(mysqli_query($link, "SELECT id FROM answers"));
        echo "<table>
                <tr><td>Number of accounts:</td><td>".$membersData."</td></tr>
                <tr><td>Number of exams:</td><td>".$examsData."</td></tr>
                <tr><td>Number of questions:</td><td>".$questionsData."</td></tr>
                <tr><td>Number of attempts:</td><td>".$attemptsData."</td></tr>
                <tr><td>Number of answers:</td><td>".$answersData."</td></tr>
            </table>";
        ?>
        
    <?php
	   endif;
    ?>
    </div>
</div>
    
<?php require '../scripts/jsload.php'; ?>

</body>
</html>