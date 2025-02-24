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

// Đường dẫn thư mục lưu ảnh
$uploadDir = 'uploads/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenTaiLieu = $_POST['tenTaiLieu'];
    $tacGia = $_POST['tacGia'];
    $loaiTaiLieu = $_POST['loaiTaiLieu'];
    $soLuong = intval($_POST['soLuong']);
    $tienCoc = floatval($_POST['tienCoc']);
    $img_url = $document['img_url']; // Giữ URL hình ảnh cũ nếu không tải lên ảnh mới

    // Kiểm tra nếu có file ảnh được tải lên
    if (isset($_FILES['img_file']) && $_FILES['img_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['img_file']['tmp_name'];
        $fileName = $_FILES['img_file']['name'];
        $fileSize = $_FILES['img_file']['size'];
        $fileType = $_FILES['img_file']['type'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Danh sách các định dạng ảnh hợp lệ
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif','webp'];

        if (in_array($fileExtension, $allowedExtensions)) {
            // Tạo tên file duy nhất
            $newFileName = uniqid('img_', true) . '.' . $fileExtension;

            // Đường dẫn đầy đủ để lưu file
            $destPath = $uploadDir . $newFileName;

            // Di chuyển file vào thư mục uploads
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $img_url = $destPath; // Cập nhật URL hình ảnh mới
            } else {
                $error = "Không thể tải lên file ảnh.";
            }
        } else {
            $error = "Định dạng file không hợp lệ. Vui lòng chọn file JPG, PNG hoặc GIF.";
        }
    }

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
        $updateSql = "UPDATE TaiLieu SET tenTaiLieu = ?, tacGia = ?, loaiTaiLieu = ?, soLuong = ?, tienCoc = ? , img_url = ? WHERE idTaiLieu = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("sssiisi", $tenTaiLieu, $tacGia, $loaiTaiLieu, $soLuong, $tienCoc, $img_url, $id);

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

    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="tenTaiLieu">Tên tài liệu</label>
            <input type="text" name="tenTaiLieu" id="tenTaiLieu" class="form-control" 
                   value="<?= htmlspecialchars($document['tenTaiLieu']); ?>" required>
        </div>

        <div class="form-group">
            <label for="tacGia">Tác giả</label>
            <input type="text" name="tacGia" id="tacGia" class="form-control" 
                   value="<?= htmlspecialchars($document['tacGia']); ?>" required>
        </div>

        <div class="form-group">
            <label for="loaiTaiLieu">Thể loại</label>
            <select name="loaiTaiLieu" id="loaiTaiLieu" class="form-control" required>
                <option value="<?= htmlspecialchars($document['loaiTaiLieu']); ?>" selected>
                    <?= htmlspecialchars($document['loaiTaiLieu']); ?>
                </option>
                <?php while ($row = mysqli_fetch_assoc($categoriesResult)): ?>
                    <?php if ($document['loaiTaiLieu'] != $row['tenTheLoai']): ?>
                        <option value="<?= htmlspecialchars($row['tenTheLoai']); ?>">
                            <?= htmlspecialchars($row['tenTheLoai']); ?>
                        </option>
                    <?php endif; ?>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="soLuong">Số lượng</label>
            <input type="number" name="soLuong" id="soLuong" class="form-control" 
                   value="<?= htmlspecialchars($document['soLuong']); ?>" required>
        </div>

        <div class="form-group">
            <label for="tienCoc">Tiền cọc</label>
            <input type="number" step="0.01" name="tienCoc" id="tienCoc" class="form-control" 
                value="<?= htmlspecialchars($document['tienCoc']); ?>" required>
        </div>

        <!-- Tải ảnh -->
        <div class="form-group">
            <label for="img_file">Tải lên hình ảnh mới</label>
            <input type="file" name="img_file" id="img_file" class="form-control-file">
        </div>

        <!-- Xem hình ảnh hiện tại -->
        <div class="form-group">
            <label>Hình ảnh hiện tại:</label><br>
            <?php if (!empty($document['img_url'])): ?>
                <img src="<?= htmlspecialchars($document['img_url']); ?>" alt="Hình ảnh tài liệu" 
                     style="width: 100px; height: auto;">
            <?php else: ?>
                <p>Chưa có hình ảnh.</p>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        <a href="bookManagement.php" class="btn btn-secondary">Hủy</a>
    </form>
</div>
</body>
</html>
