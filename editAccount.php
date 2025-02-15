<?php
session_start();

if (!isset($_SESSION['userName']) || strtolower($_SESSION['role']) != ('admin' || 'thuthu') || strtolower($_SESSION['status']) != 'hoatdong') {
    include('logout.php');
    exit();
  }

require('config.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: account.php"); 
    exit();
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM TaiKhoan WHERE idTaiKhoan = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: account.php"); 
    exit();
}

$account = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenDangNhap = $_POST['tenDangNhap'];
    $matKhau = $_POST['matKhau'];
    $vaiTro = $_POST['vaiTro'];
    $trangThai = $_POST['trangThai'];

    // Kiểm tra tên đăng nhập trùng lặp
    $checkDuplicateSql = "SELECT idTaiKhoan FROM TaiKhoan WHERE tenDangNhap = ? AND idTaiKhoan != ?";
    $checkStmt = $conn->prepare($checkDuplicateSql);
    $checkStmt->bind_param("si", $tenDangNhap, $id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $error = "Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.";
    } else {
        // Nếu không trùng, tiếp tục cập nhật
        $updateSql = "UPDATE TaiKhoan SET tenDangNhap = ?, matKhau = ?, vaiTro = ?, trangThai = ? WHERE idTaiKhoan = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ssssi", $tenDangNhap, $matKhau, $vaiTro, $trangThai, $id);

        if ($updateStmt->execute()) {
            header("Location: account.php?status=success&message=Tài khoản đã được cập nhật thành công.");
            exit();
        } else {
            $error = "Có lỗi xảy ra khi cập nhật tài khoản.";
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa tài khoản</title>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Chỉnh sửa tài khoản</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="tenDangNhap">Tên đăng nhập</label>
            <input type="text" name="tenDangNhap" id="tenDangNhap" class="form-control" value="<?= htmlspecialchars($account['tenDangNhap']); ?>" required>
        </div>

        <div class="form-group">
            <label for="matKhau">Mật khẩu</label>
            <input type="text" name="matKhau" id="matKhau" class="form-control" value="<?= htmlspecialchars($account['matKhau']); ?>" required>
        </div>

        <div class="form-group">
            <label for="vaiTro">Vai trò</label>
            <select name="vaiTro" id="vaiTro" class="form-control" required>
                <option value="admin" <?= $account['vaiTro'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                <option value="thuthu" <?= $account['vaiTro'] === 'thuthu' ? 'selected' : ''; ?>>Thủ thư</option>
                <option value="docgia" <?= $account['vaiTro'] === 'docgia' ? 'selected' : ''; ?>>Độc giả</option>
            </select>
        </div>

        <div class="form-group">
            <label for="trangThai">Trạng thái</label>
            <select name="trangThai" id="trangThai" class="form-control" required>
                <option value="hoatdong" <?= $account['trangThai'] === 'hoatdong' ? 'selected' : ''; ?>>Hoạt Động</option>
                <option value="khoa" <?= $account['trangThai'] === 'khoa' ? 'selected' : ''; ?>>Khóa</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        <a href="account.php" class="btn btn-secondary">Hủy</a>
    </form>
</div>
</body>
</html>
