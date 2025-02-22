<?php
    session_start();

    if (!isset($_SESSION['userName']) || strtolower($_SESSION['role']) != 'docgia' || strtolower($_SESSION['status']) != 'hoatdong') {
        include('logout.php');
        exit();
      }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php include("menu.php");  ?>
    
    <?php include("slider.php");  ?>

    <?php include("content.php");  ?>

    <?php include("footer.php");  ?>

</body>
</html>