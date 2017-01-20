<?php
    require '../scripts/header.php';

    $key=$_GET['key'];
    $array = array();
    $query=mysqli_query($link, "SELECT * FROM dewey_members WHERE usr LIKE '%{$key}%' AND (privilege='teacher' OR privilege='admin' OR privilege='sysop')");
    while($row=mysqli_fetch_assoc($query))
    {
      $array[] = $row['usr'];
    }
    echo json_encode($array);
?>
