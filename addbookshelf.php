<!DOCTYPE html>
<html lang="en">
    <head>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Thêm kệ sách</title>
        <link rel="stylesheet" href="./css/bookshelf.css">
    </head>
    </head>
    <body>
    <?php 
    require 'config.php';
    $error = ""; // Khởi tạo biến lỗi

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $sttKeSach = $_POST['sttKeSach'];
        $tenTaiLieu = $_POST['tenTaiLieu'];
        $sttCot = $_POST['sttCot'];
        $sttHang = $_POST['sttHang'];
    
        // Kiểm tra trùng tên tài liệu hoặc vị trí kệ
        $check_sql = "SELECT * FROM kesach WHERE tenTaiLieu = ? OR (sttKeSach = ? AND sttCot = ? AND sttHang = ?)";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("siii", $tenTaiLieu, $sttKeSach, $sttCot, $sttHang);
        $stmt->execute();
        $check_result = $stmt->get_result();

    if ($check_result->num_rows > 0) {
        $error = "Tên tài liệu hoặc vị trí kệ sách đã tồn tại. Vui lòng chọn thông tin khác.";
    } else {
        // Chèn dữ liệu mới
        $insert_sql = "INSERT INTO kesach (sttKeSach, tenTaiLieu, sttCot, sttHang) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("isii", $sttKeSach, $tenTaiLieu, $sttCot, $sttHang);
        
        if ($conn->query($insert_sql) === TRUE) {
            echo "Thêm thành công!";
            header("Location: Bookshelf.php");
            exit();
        } else {
            echo "Thêm thất bại: " . $conn->error;
        }
    }
   
    }

    $conn->close();
?>
 
    <form action="addbookshelf.php" method="post">
            <h2>Điền thông tin kệ sách</h2>
            <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
            Số thứ tự kệ sách : <input type="text" name="sttKeSach"class="info" required><br>
            Tên tài liệu: <input type="text" name="tenTaiLieu"class="info" required><br>
            Số thứ tự cột: <input type="number" name="sttCot"class="info" required><br>
            Số thứ tự hàng: <input type="number" name="sttHang" class="info" required><br>
            
            <input type="submit" value="Thêm" class="btn">
        </form>
    </body>
</html>