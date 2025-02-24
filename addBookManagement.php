<?php
require('config.php');

// Biến để lưu giá trị đã nhập
$tenTaiLieu = "";
$tacGia = "";
$tienCoc = "";
$loaiTaiLieu = "";
$soLuong = "";
$errorMessage = "";
$successMessage = "";

// Truy vấn thể loại từ cơ sở dữ liệu
$sqlCategories = "SELECT idTheLoai, tenTheLoai FROM TheLoai";
$categoriesResult = mysqli_query($conn, $sqlCategories);

// Xử lý khi biểu mẫu được gửi đi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenTaiLieu = mysqli_real_escape_string($conn, $_POST['tenTaiLieu']);
    $tacGia = mysqli_real_escape_string($conn, $_POST['tacGia']);
    $soLuong = mysqli_real_escape_string($conn, $_POST['soLuong']);
    $loaiTaiLieu = mysqli_real_escape_string($conn, $_POST['loaiTaiLieu']);
    $tienCoc = mysqli_real_escape_string($conn, $_POST['tienCoc']);


    // Kiểm tra xem file hình ảnh có được tải lên không
    if (isset($_FILES['img_file']) && $_FILES['img_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/'; // Thư mục lưu file
        $fileTmpPath = $_FILES['img_file']['tmp_name'];
        $fileName = basename($_FILES['img_file']['name']);
        $fileSize = $_FILES['img_file']['size'];
        $fileType = $_FILES['img_file']['type'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Danh sách các định dạng ảnh được phép
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif','webp'];

        if (in_array($fileExtension, $allowedExtensions)) {
            // Đặt tên file duy nhất để tránh ghi đè
            $newFileName = uniqid() . '.' . $fileExtension;
            $uploadFilePath = $uploadDir . $newFileName;

            // Di chuyển file từ thư mục tạm vào thư mục đích
            if (move_uploaded_file($fileTmpPath, $uploadFilePath)) {
                // Lưu vào cơ sở dữ liệu
                $checkSql = "SELECT * FROM TaiLieu WHERE tenTaiLieu = '$tenTaiLieu'";
                $result = mysqli_query($conn, $checkSql);

                if (mysqli_num_rows($result) > 0) {
                    $errorMessage = "Tên sách đã tồn tại. Vui lòng chọn tên khác!";
                } else {
                    $sql = "INSERT INTO TaiLieu (tenTaiLieu, tacGia, soLuong, loaiTaiLieu, img_url, tienCoc) 
                    VALUES ('$tenTaiLieu', '$tacGia', '$soLuong', '$loaiTaiLieu', '$uploadFilePath', '$tienCoc')";

                    if (mysqli_query($conn, $sql)) {
                        header("Location: bookManagement.php?status=success&message=Thêm thành công!");
                        exit();
                        // Reset các trường nhập liệu
                        $tenTaiLieu = $tacGia = $soLuong = $loaiTaiLieu = "";
                    } else {
                        $errorMessage = "Lỗi thêm tài liệu!";
                    }
                }
            } else {
                $errorMessage = "Không thể di chuyển file tải lên.";
            }
        } else {
            $errorMessage = "Chỉ chấp nhận các định dạng JPG, JPEG, PNG, GIF.";
        }
    } else {
        $errorMessage = "Vui lòng tải lên một file hình ảnh.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Tài Liệu</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Thêm Tài Liệu</h2>
    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger">
            <?php echo $errorMessage; ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success">
            <?php echo $successMessage; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="tenTaiLieu">Tên Sách</label>
            <input type="text" name="tenTaiLieu" id="tenTaiLieu" class="form-control" value="<?php echo htmlspecialchars($tenTaiLieu); ?>" required>
        </div>

        <div class="form-group">
            <label for="tacGia">Tác giả</label>
            <input type="text" name="tacGia" id="tacGia" class="form-control" value="<?php echo htmlspecialchars($tacGia); ?>" required>
        </div>

        <div class="form-group">
            <label for="loaiTaiLieu">Thể Loại</label>
            <select name="loaiTaiLieu" id="loaiTaiLieu" class="form-control" required>
                <option value="">Chọn thể loại</option>
                <?php while ($row = mysqli_fetch_assoc($categoriesResult)): ?>
                    <option value="<?php echo $row['tenTheLoai']; ?>" 
                        <?php echo $loaiTaiLieu == $row['idTheLoai'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($row['tenTheLoai']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="soLuong">Số lượng</label>
            <input type="text" name="soLuong" id="soLuong" class="form-control" value="<?php echo htmlspecialchars($soLuong); ?>" required>
        </div>

        <div class="form-group">
            <label for="tienCoc">Tiền Cọc</label>
            <input type="text" name="tienCoc" id="tienCoc" class="form-control" 
                value="<?php echo htmlspecialchars($tienCoc ?? ''); ?>" required>
        </div>


        <div class="form-group">
            <label for="img_file">Hình Ảnh</label>
            <input type="file" name="img_file" id="img_file" class="form-control-file" required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Thêm Sách</button>
        <a href="bookManagement.php" class="btn btn-secondary mt-3">Quay Lại</a>
    </form>
</div>
</body>
</html>
