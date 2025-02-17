<?php
session_start();

if (!isset($_SESSION['userName']) || strtolower($_SESSION['role']) != ('admin' || 'thuthu') || strtolower($_SESSION['status']) != 'hoatdong') {
    include('logout.php');
    exit();
}

require('config.php');

// Biến để lưu giá trị đã nhập
$tenTheLoai = "";
$moTa = "";

// Xử lý khi biểu mẫu được gửi đi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenTheLoai = mysqli_real_escape_string($conn, $_POST['tenTheLoai']);
    $moTa = mysqli_real_escape_string($conn, $_POST['moTa']);

    // Kiểm tra các trường không được để trống
    if (!empty($tenTheLoai) && !empty($moTa) ) {
        // Kiểm tra xem tên đăng nhập đã tồn tại hay chưa
        $checkSql = "SELECT * FROM TheLoai WHERE tenTheLoai = '$tenTheLoai'";
        $result = mysqli_query($conn, $checkSql);

        if (mysqli_num_rows($result) > 0) {
            // Nếu tên đăng nhập đã tồn tại
            $errorMessage = "Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác!";
        } else {
            // Thêm thể loại vào cơ sở dữ liệu
            $sql = "INSERT INTO TheLoai (tenTheLoai, moTa) 
                    VALUES ('$tenTheLoai', '$moTa')";

            if (mysqli_query($conn, $sql)) {
                // Chuyển hướng về trang category.php nếu thành công
                header("Location: category.php?status=success&message=Thêm thành công thể loại!");
                exit();
            } else {
                $errorMessage = "Lỗi thêm thể loại!";
            }
        }
    } else {
        $errorMessage = "Vui lòng điền đầy đủ thông tin!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm thể loại</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Thêm thể loại</h2>
    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger">
            <?php echo $errorMessage; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="tenTheLoai">Tên Thể Loại</label>
            <input type="text" name="tenTheLoai" id="tenTheLoai" class="form-control" value="<?php echo htmlspecialchars($tenTheLoai); ?>" required>
        </div>

        <div class="form-group">
            <label for="moTa">Mô tả</label>
            <input type="text" name="moTa" id="moTa" class="form-control" value="<?php echo htmlspecialchars($moTa); ?>" required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Thêm thể loại</button>
        <a href="category.php" class="btn btn-secondary mt-3">Quay Lại</a>
    </form>
</div>
</body>
</html>
