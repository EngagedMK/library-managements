<?php
session_start();

if (!isset($_SESSION['userName']) || strtolower($_SESSION['role']) != ('admin' || 'thuthu') || strtolower($_SESSION['status']) != 'hoatdong') {
    include('logout.php');
    exit();
}

require('config.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: bookManagement.php");
    exit();
}

// Truy vấn thể loại từ cơ sở dữ liệu
$sqlCategories = "SELECT idTheLoai, tenTheLoai FROM TheLoai";
$categoriesResult = mysqli_query($conn, $sqlCategories);

$id = intval($_GET['id']);

$sql = "SELECT * FROM TaiLieu WHERE idTaiLieu = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: bookManagement.php");
    exit();
}

$document = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenTaiLieu = $_POST['tenTaiLieu'];
    $tacGia = $_POST['tacGia'];
    $loaiTaiLieu = ($_POST['loaiTaiLieu']);
    $soLuong = intval($_POST['soLuong']);

    // Kiểm tra tên tài liệu trùng lặp
    $checkDuplicateSql = "SELECT idTaiLieu FROM TaiLieu WHERE tenTaiLieu = ? AND idTaiLieu != ?";
    $checkStmt = $conn->prepare($checkDuplicateSql);
    $checkStmt->bind_param("si", $tenTaiLieu, $id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $error = "Tên tài liệu đã tồn tại. Vui lòng chọn tên khác.";
    } else {
        // Cập nhật tài liệu
        $updateSql = "UPDATE TaiLieu SET tenTaiLieu = ?, tacGia = ?, loaiTaiLieu = ?, soLuong = ? WHERE idTaiLieu = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("sssii", $tenTaiLieu, $tacGia, $loaiTaiLieu, $soLuong, $id);

        if ($updateStmt->execute()) {
            header("Location: bookManagement.php?status=success&message=Tài liệu đã được cập nhật thành công.");
            exit();
        } else {
            $error = "Có lỗi xảy ra khi cập nhật tài liệu.";
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa tài liệu</title>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Chỉnh sửa tài liệu</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="tenTaiLieu">Tên tài liệu</label>
            <input type="text" name="tenTaiLieu" id="tenTaiLieu" class="form-control" value="<?= htmlspecialchars($document['tenTaiLieu']); ?>" required>
        </div>

        <div class="form-group">
            <label for="tacGia">Tác giả</label>
            <input type="text" name="tacGia" id="tacGia" class="form-control" value="<?= htmlspecialchars($document['tacGia']); ?>" required>
        </div>

        <div class="form-group">
            <label for="loaiTaiLieu">Thể Loại</label>
            <select name="loaiTaiLieu" id="loaiTaiLieu" class="form-control" required>
                <option value="<?= htmlspecialchars($document['loaiTaiLieu']); ?>" selected><?= htmlspecialchars($document['loaiTaiLieu']); ?></option>
                <?php while ($row = mysqli_fetch_assoc($categoriesResult)): ?>
                    <?php if ($document['loaiTaiLieu'] != $row['tenTheLoai']):  ?>
                        <option value="<?php echo $row['tenTheLoai']; ?>">
                            <?php echo htmlspecialchars($row['tenTheLoai']); ?>
                        </option>
                    <?php endif; ?>
                <?php endwhile; ?>
            </select>
        </div>




        <div class="form-group">
            <label for="soLuong">Số lượng</label>
            <input type="number" name="soLuong" id="soLuong" class="form-control" value="<?= htmlspecialchars($document['soLuong']); ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        <a href="bookManagement.php" class="btn btn-secondary">Hủy</a>
    </form>
</div>
</body>
</html>
