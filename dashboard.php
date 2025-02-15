<?php
    session_start();

    if (!isset($_SESSION['userName']) || strtolower($_SESSION['role']) != ('admin' || 'thuthu') || strtolower($_SESSION['status']) != 'hoatdong') {
        include('logout.php');
        exit();
      }
    
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sidebar Menu</title>
  <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="css/style.css">
</head>

<div class="body_div">
  <?php include("menu.php");  ?>


  <main>
    <?php include("components/toast.php");  ?>
    <h1>My Dashboard</h1>
    <p class="text">
      Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur animi voluptatibus cum maxime distinctio
      iste quod deleniti eius, autem voluptates cumque suscipit iure quasi eligendi ullam. Sapiente eligendi porro
      reprehenderit corrupti error facilis quo, fugiat fugit? Maiores aliquam ad, molestiae iste nihil, commodi
      doloremque tempore excepturi aut id ducimus unde?
    </p>
    <p class="copyright">
      &copy; 2024 - <span>Nhóm 2</span> All Rights Reserved.
    </p>
  </main>
</div>

</html>