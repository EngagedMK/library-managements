<?php
session_start();

if (!isset($_SESSION['userName']) || strtolower($_SESSION['role']) != ('admin' || 'thuthu') || strtolower($_SESSION['status']) != 'hoatdong') {
    include('logout.php');
    exit();
}

require('config.php');

// Biến để lưu giá trị đã nhập
$tenDangNhap = "";
$matKhau = "";
$vaiTro = "";
$errorMessage = "";

// Xử lý khi biểu mẫu được gửi đi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenDangNhap = mysqli_real_escape_string($conn, $_POST['tenDangNhap']);
    $matKhau = mysqli_real_escape_string($conn, $_POST['matKhau']);
    $vaiTro = mysqli_real_escape_string($conn, $_POST['vaiTro']);

    // Kiểm tra các trường không được để trống
    if (!empty($tenDangNhap) && !empty($matKhau) && !empty($vaiTro)) {
        // Kiểm tra xem tên đăng nhập đã tồn tại hay chưa
        $checkSql = "SELECT * FROM TaiKhoan WHERE tenDangNhap = '$tenDangNhap'";
        $result = mysqli_query($conn, $checkSql);

        if (mysqli_num_rows($result) > 0) {
            // Nếu tên đăng nhập đã tồn tại
            $errorMessage = "Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác!";
        } else {
            // Thêm tài khoản vào cơ sở dữ liệu
            $sql = "INSERT INTO TaiKhoan (tenDangNhap, matKhau, vaiTro, trangThai) 
                    VALUES ('$tenDangNhap', '$matKhau', '$vaiTro', 'hoatdong')";

            if (mysqli_query($conn, $sql)) {
                // Chuyển hướng về trang account.php nếu thành công
                header("Location: account.php?status=success&message=Thêm thành công tài khoản!");
                exit();
            } else {
                $errorMessage = "Lỗi thêm tài khoản!";
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
    <title>Thêm Tài Khoản</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Thêm Tài Khoản</h2>
    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger">
            <?php echo $errorMessage; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="tenDangNhap">Tên Đăng Nhập</label>
            <input type="text" name="tenDangNhap" id="tenDangNhap" class="form-control" value="<?php echo htmlspecialchars($tenDangNhap); ?>" required>
        </div>

        <div class="form-group">
            <label for="matKhau">Mật Khẩu</label>
            <input type="password" name="matKhau" id="matKhau" class="form-control" value="<?php echo htmlspecialchars($matKhau); ?>" required>
        </div>

        <div class="form-group">
            <label for="vaiTro">Vai Trò</label>
            <select name="vaiTro" id="vaiTro" class="form-control" required>
                <option value="docgia" <?php echo $vaiTro === 'docgia' ? 'selected' : ''; ?>>Độc giả</option>
                <option value="thuthu" <?php echo $vaiTro === 'thuthu' ? 'selected' : ''; ?>>Thủ thư</option>
                <option value="admin" <?php echo $vaiTro === 'admin' ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Thêm Tài Khoản</button>
        <a href="account.php" class="btn btn-secondary mt-3">Quay Lại</a>
    </form>
</div>
</body>
</html>
