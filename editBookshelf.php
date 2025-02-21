<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Chỉnh sửa thông tin kệ sách</title>
        <link rel="stylesheet" href="./css/bookshelf.css">
    </head>
    <body>
        <?php 
            require 'config.php';
            session_start();
            if(isset($_SESSION['username']) && $_SESSION['username']=="admin"){
                $idKeSach = $_POST['idKeSach']; 
                $tenTaiLieu = $_POST['tenTaiLieu'];
                $sttKeSach = $_POST['sttKeSach'];
                $sttCot = $_POST['sttCot'];
                $sttHang = $_POST['sttHang'];
               
                $update_sql = "UPDATE kesach  SET idKeSach ='$idKeSach', tenTaiLieu = '$tenTaiLieu', sttCot = '$sttCot', sttHang = $sttHang WHERE idKeSach = $idKeSach";
                if ($conn->query($update_sql) === TRUE) {
                    echo "Cập nhật thành công!";
                    header("location: Bookshelf.php");
                } else {
                    echo "Cập nhật thất bại: " . $conn->error;
                }
            }
            
        ?>
        
    </body>
</html>