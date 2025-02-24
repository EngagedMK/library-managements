<?php
    session_start();
    require('../config.php');

    if (!isset($_SESSION['userName']) || strtolower($_SESSION['role']) != 'docgia' || strtolower($_SESSION['status']) != 'hoatdong') {
        include('../logout.php');
        exit();
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include("menu.php"); ?>

    <div class="container text-center">
    <div class="row">
        <div class="col-5 d_f_c " >
            <div class="d_f my-2">
                <div class="d_f" style="width: 150px">
                    <i class='bx bxs-map' style="color: #F94AFF;font-size: 24px"></i>
                    <span style="margin-right: 4px">Địa chỉ:</span>
                </div>
                <span>Trường đại học Mỏ - Địa chất</span>
            </div>

            <div class="d_f my-2">
                <div class="d_f" style="width: 150px">
                    <i class='bx bxs-phone-call' style="color: #734AFF;font-size: 24px"></i>
                    <span style="margin-right: 4px">Số điện thoại:</span>
                </div>
                <span>09123456789</span>
            </div>

            <div class="d_f my-2">
                <div class="d_f" style="width: 150px">
                    <i class='bx bxs-envelope' style="color: #4AD4FF;font-size: 24px"></i>
                    <span style="margin-right: 4px">Email:</span>
                </div>
                <span>khongco245@gmail.com</span>
            </div>
        </div>
        <div class="col-7 d_f_c_c">
            <img src="../assets/img/contact.jpg" width="100%" />
        </div>
    </div>
</div>

    <?php include("footer.php"); ?>
</body>
</html>


<style>

    .d_f {
        display: flex;
        align-items: center;
        /* justify-content: center; */
        /* text-align: center; */
    }

    .d_f_c {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .d_f_c_c {
        display: flex;
        justify-content: center;
    }

</style>