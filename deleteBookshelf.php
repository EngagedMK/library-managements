<?php
session_start();
require('config.php');

if (!isset($_GET['id'])) {
    header("Location: bookshelf.php?status=error&message=No ID provided");
    exit();
}

$id = intval($_GET['id']); // Sanitize ID

// Kiểm tra xem loại tài liệu đã được sử dụng hay chưa
$checkSql = "SELECT COUNT(*) as count FROM KeSach WHERE tenTaiLieu = (SELECT tenTaiLieu FROM TaiLieu WHERE tenTaiLieu = (SELECT tenTaiLieu FROM KeSach WHERE idKeSach = ?))";
$checkStmt = mysqli_prepare($conn, $checkSql);

if ($checkStmt) {
    mysqli_stmt_bind_param($checkStmt, 'i', $id);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_bind_result($checkStmt, $count);
    mysqli_stmt_fetch($checkStmt);
    mysqli_stmt_close($checkStmt);

    if ($count > 0) {
        // Loại tài liệu đã được sử dụng
        header("Location: bookshelf.php?status=error&message=Kệ sách đã được sử dụng, không thể xóa!");
        exit();
    }
}

// Nếu không được sử dụng, tiếp tục xóa
$sql = "DELETE FROM KeSach WHERE idKeSach = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: bookshelf.php?status=success&message=Xóa thành công Kệ sách!");
        exit();
    } else {
        header("Location: bookshelf.php?status=error&message=Xóa không thành công Kệ sách!");
        exit();
    }
} else {
    header("Location: bookshelf.php?status=error&message=Lỗi truy vấn DATABASE");
    exit();
}
?>