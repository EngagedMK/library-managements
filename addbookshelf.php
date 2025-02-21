<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Kệ sách</title>
    </head>
    <body>
        <?php 

            require 'config.php';
            $idKeSach = $_POST['idKeSach'];
            $sttKeSach = $_POST['sttKeSach'];
            $tenTaiLieu = $_POST["tenTaiLieu"];
            $sttCot = $_POST['sttCot'];
            $sttHang = $_POST['sttHang'];
            
            $sql1 = "SELECT idKeSach from tailieu where idKeSach = '$idKeSach'";
            $result = $conn->query($sql1);
            if($result->num_rows != 0 ){
                echo "<script>
                        alert('Đã tồn tại');
                        window.location.href = 'account.php';
                    </script>";
               
                
            }
            $sql = "INSERT INTO kesach (idKeSach,sttKeSach,tenTaiLieu,sttCot,sttHang) 
                    VALUES ('$idKeSach','$sttKeSach','$tenTaiLieu',$sttCot,$sttHang)";
            if ($conn->query($sql) === TRUE) {
                header("location: Bookshelf.php");
            } else {
                echo "Lỗi: " . $sql . "<br>" . $conn->error;
            }

            $conn->close();

        ?>
    </body>
</html>