<?php
    session_start();

    if (!isset($_SESSION['userName']) || strtolower($_SESSION['role']) != ('admin' || 'thuthu') || strtolower($_SESSION['status']) != 'hoatdong') {
        include('logout.php');
        exit();
      }

      if (strtolower($_SESSION['role']) === 'docgia' ) {
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
    <h1 style="font-weight: 500;margin-bottom: 16px">My Dashboard</h1>
    <div class="row">
      <div class="col box_db"> 
        <div class='bx bxs-book-bookmark' style="background-color: #AC39F4;box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;height: 40px;width: 40px;padding: 12px 12px;color: #fff; border-radius: 8px">
        </div>

        <div style="margin-left: 16px">
        <h6 class="text-muted font-semibold">Profile Views</h6>
        <h6 class="font-extrabold mb-0">112.000</h6>
        </div>
      </div>
      <div class="col box_db"> 
        <div class='bx bxs-book-bookmark' style="background-color: #AC39F4;box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;height: 40px;width: 40px;padding: 12px 12px;color: #fff; border-radius: 8px">
        </div>

        <div style="margin-left: 16px">
        <h6 class="text-muted font-semibold">Profile Views</h6>
        <h6 class="font-extrabold mb-0">112.000</h6>
        </div>
      </div>
      <div class="col box_db"> 
        <div class='bx bxs-book-bookmark' style="background-color: #AC39F4;box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;height: 40px;width: 40px;padding: 12px 12px;color: #fff; border-radius: 8px">
        </div>

        <div style="margin-left: 16px">
        <h6 class="text-muted font-semibold">Profile Views</h6>
        <h6 class="font-extrabold mb-0">112.000</h6>
        </div>
      </div>
      <div class="col box_db"> 
        <div class='bx bxs-book-bookmark' style="background-color: #AC39F4;box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;height: 40px;width: 40px;padding: 12px 12px;color: #fff; border-radius: 8px">
        </div>

        <div style="margin-left: 16px">
        <h6 class="text-muted font-semibold">Profile Views</h6>
        <h6 class="font-extrabold mb-0">112.000</h6>
        </div>
      </div>
    </div>
    <p class="copyright">
      &copy; 2024 - <span>Nhóm 2</span> All Rights Reserved.
    </p>
  </main>
</div>

</html>