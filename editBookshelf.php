<?php 
    require 'config.php';
    session_start();
    // Truy vấn thể loại từ cơ sở dữ liệu
$sqlCategories = "SELECT * FROM kesach";
$categoriesResult = mysqli_query($conn, $sqlCategories);

$id = intval($_GET['id']);

$sql = "SELECT * FROM kesach WHERE idKeSach = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$kesach = $result->fetch_assoc();

    if(isset($_SESSION['username']) && $_SESSION['username'] == "admin") {
        if(isset($_POST['idKeSach'])) {
            $idKeSach = $_POST['idKeSach'];
            $sql = "SELECT * FROM kesach WHERE idKeSach = '$idKeSach'";
            $result = $conn->query($sql);
            $kesach = $result->fetch_assoc();
        }
    }

    if(isset($_POST['tenTaiLieu'])) {
        $idKeSach = $_POST['idKeSach'];
        $tenTaiLieu = $_POST['tenTaiLieu'];
        $sttKeSach = $_POST['sttKeSach'];
        $sttCot = $_POST['sttCot'];
        $sttHang = $_POST['sttHang'];

        // Kiểm tra xem tài liệu có trùng lặp không
        $check_sql = "SELECT * FROM kesach WHERE (tenTaiLieu = ? OR (sttKeSach = ? AND sttCot = ? AND sttHang = ?)) AND idKeSach != ?";
        $stmt = $conn->prepare($check_sql);
         $stmt->bind_param("siiii", $tenTaiLieu, $sttKeSach, $sttCot, $sttHang, $idKeSach);
        $stmt->execute();
        $check_result = $stmt->get_result();
        if ($check_result->num_rows > 0) {
            $error = "Tên tài liệu hoặc vị trí kệ sách đã tồn tại. Vui lòng chọn thông tin khác.";
        } else {
            $update_sql = "UPDATE kesach SET tenTaiLieu='$tenTaiLieu', sttKeSach='$sttKeSach', sttCot='$sttCot', sttHang='$sttHang' WHERE idKeSach='$idKeSach'";
            
            if ($conn->query($update_sql) === TRUE) {
                echo "Cập nhật thành công!";
                header("Location: Bookshelf.php?status=success&message=Sửa thành công kệ sách!");
                exit();
            } else {
                echo "Cập nhật thất bại: " . $conn->error;
            }
        }
        
        
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa thông tin kệ sách</title>
    
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/bookshelf.css">
</head>
<body>
    <div class="chinh_sua">
        <h2>Chỉnh sửa thông tin sách</h2>
        <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="idKeSach" value="<?php echo $kesach['idKeSach']; ?>" >

            Tên tài liệu: 
            <input type="text" class="info disabled" name="tenTaiLieu" value="<?php echo $kesach['tenTaiLieu']; ?>" required readonly><br><br>
            
            Số thứ tự Kệ sách:
            <input type="text" class="info" name="sttKeSach" value="<?php echo $kesach['sttKeSach']; ?>" required><br><br>
            
            Số thứ tự cột: 
            <input type="number" class="info" name="sttCot" value="<?php echo $kesach['sttCot']; ?>" required><br><br>
            
            Số thứ tự hàng: 
            <input type="number" class="info" name="sttHang" value="<?php echo $kesach['sttHang']; ?>" required><br><br>
            
            <input type="submit" class="btn" value="Cập nhật">
        </form>
    </div>
</body>
</html>
