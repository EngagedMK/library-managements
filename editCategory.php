<?php
session_start();

if (!isset($_SESSION['userName']) || strtolower($_SESSION['role']) != ('admin' || 'thuthu') || strtolower($_SESSION['status']) != 'hoatdong') {
    include('logout.php');
    exit();
}

require('config.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: category.php");
    exit();
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM TheLoai WHERE idTheLoai = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: category.php");
    exit();
}

$document = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenTheLoai = $_POST['tenTheLoai'];
    $moTa = $_POST['moTa'];

    // Kiểm tra tên thể loại trùng lặp
    $checkDuplicateSql = "SELECT idTheLoai FROM TheLoai WHERE tenTheLoai = ? AND idTheLoai != ?";
    $checkStmt = $conn->prepare($checkDuplicateSql);
    $checkStmt->bind_param("si", $tenTheLoai, $id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $error = "Tên thể loại đã tồn tại. Vui lòng chọn tên khác.";
    } else {
        // Nếu không trùng, tiếp tục cập nhật
        $updateSql = "UPDATE TheLoai SET tenTheLoai = ?, moTa = ? WHERE idTheLoai = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ssi", $tenTheLoai, $moTa, $id);

        if ($updateStmt->execute()) {
            header("Location: category.php?status=success&message thể loại đã được cập nhật thành công.");
            exit();
        } else {
            $error = "Có lỗi xảy ra khi cập nhật thể loại.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa thể loại</title>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Chỉnh sửa thể loại</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="tenTheLoai">Tên thể loại</label>
            <input type="text" name="tenTheLoai" id="tenTheLoai" class="form-control" value="<?= htmlspecialchars($document['tenTheLoai']); ?>" required>
        </div>

        <div class="form-group">
            <label for="moTa">Mô tả</label>
            <input type="text" name="moTa" id="moTa" class="form-control" value="<?= htmlspecialchars($document['moTa']); ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        <a href="category.php" class="btn btn-secondary">Hủy</a>
    </form>
</div>
</body>
</html>
