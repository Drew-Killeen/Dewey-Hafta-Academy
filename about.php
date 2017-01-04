<?php 

    require 'scripts/header.php'; 

    if(!$_GET['article'] && $_GET['action'] != 'new') {
        header('Location: ../about?article=1');
    }
    if($_POST['submit'] == 'Submit') {
        
        $err = array();
	
        if(strlen($_POST['title'])<1 || strlen($_POST['title'])>32) {
            $err[]='The title must be between 1 and 32 characters.';
        }
        
        if(!count($err)) {
            $input = mysqli_real_escape_string($link, $_POST['textarea']);
            $title = mysqli_real_escape_string($link, $_POST['title']);
            mysqli_query($link, "UPDATE help SET content='".$input."', public=".$_POST['public']." WHERE id=".$_GET['article']);
            $_SESSION['msg']['success']='Article succesfully updated.';
            header('Location: ?article='.$_GET['article']);
            exit();
        }
        
        if(count($err)) {
            $_SESSION['msg']['err'] = implode('<br />',$err);
            header("Location: about?article=".$_GET['article']."&action=edit");
            exit;
        }
    }
    if($_GET['action'] == 'new') {
        mysqli_query($link, "INSERT INTO help(title,public) VALUES('untitled',0)");
        $articleNum = mysqli_fetch_assoc(mysqli_query($link, "SELECT id FROM help ORDER BY id DESC LIMIT 1;"));
        header('Location: ../about?article='.$articleNum['id'].'&action=edit');
        exit();
    }
?>

<!DOCTYPE html>
<html>
<head>

<?php if($_GET['action'] != "edit") include_once("templates/htmlHeader.php");
    else echo "<script src='//cdn.tinymce.com/4/tinymce.min.js'></script>";
?>
    
<script>
  tinymce.init({
    selector: '#textarea',
    plugins: "code",
    toolbar: "code",
  });
</script>
    
</head>

<?php if($_GET['action'] != "edit"): ?>  

<body>

<?php 
    include_once("templates/login.php"); 
    include_once("templates/menu.php"); 
?>
    
<div id="main">
    <div class="container">
    <?php
        if(($_SESSION['privilege'] == 'admin' || $_SESSION['privilege'] == 'sysop') && $_GET['action'] != "edit") {
            echo "<div class='helpEdit'><input type='button' value='Edit' onclick='window.location.assign(\"../about?article=".$_GET['article']."&action=edit\")'/> <input type='button' value='New' onclick='window.location.assign(\"../about?action=new\")'/></div>";
        }
        if($_GET['article'] == 1) {
            $content = mysqli_fetch_assoc(mysqli_query($link, "SELECT title,content FROM help WHERE id=1"));
            echo "<p>".$content['content']."</p>";
        
            if($_SESSION['privilege'] == 'admin' || $_SESSION['privilege'] == 'sysop') $articles = mysqli_query($link, "SELECT id,title FROM help");
            else if($_SESSION['privilege'] == 'student' || $_SESSION['privilege'] == 'teacher') $articles = mysqli_query($link, "SELECT id,title FROM help WHERE public=1 OR public=2");
            else $articles = mysqli_query($link, "SELECT id,title FROM help WHERE public=1");
            
            echo "<ul>";
            while($row = mysqli_fetch_assoc($articles)) {
                echo "<li><a href='?article=".$row['id']."'>".$row['title']."</a></li>";
            }
            echo "</ul>";
        }
        else {
            $content = mysqli_fetch_assoc(mysqli_query($link, "SELECT title,content FROM help WHERE id=".$_GET['article']));
            echo "<p>".$content['content']."</p>";
        }
    ?>
    </div>
</div>
    
<?php require 'scripts/jsload.php'; ?>

</body>
    
<?php 
    else: 
    include_once("templates/htmlHeader.php");
?>
    
    
<body>
    
<?php include_once("templates/login.php");  ?>
    
    <div class='editor-container'>
    <?php
        if($_GET['action'] && ($_SESSION['privilege'] != "admin" && $_SESSION['privilege'] != "sysop")) {
            $_SESSION['msg']['err'] = "You do not have sufficient privileges to perform this action.";
            header('Location: ../about?article=1');
            exit();
        }
        else if($_GET['action'] == 'edit') {
            $content = mysqli_fetch_assoc(mysqli_query($link, "SELECT title,content,public FROM help WHERE id=".$_GET['article']));
            echo 
                "<form method='post'><label class='grey' for='title'>Title</label> <input type='text' name='title' value='".$content['title']."' size='23'/><br><br><textarea id='textarea' name='textarea'><p>".$content['content']."</p></textarea>";
            if($_GET['article'] != 1) {
                echo "<b>Public:</b><input type='radio' name='public' value='1' ";
                if($content['public'] == 1) echo "checked";
                echo "/><label for='1'>Yes</label><input type='radio' name='public' value='0' ";
                if($content['public'] == 0) echo "checked";
                echo "/><label for='0'>No</label><input type='radio' name='public' value='2' ";
                if($content['public'] == 2) echo "checked";
                echo "/><label for='2'>Members only</label>";
            }
            echo "<input type='submit' name='submit' class='editor-submit' value='Submit'/></form>";
                
        }
    ?>
    </div>
</body>

<?php
    require 'scripts/jsload.php';
    endif;
?>

</html>
