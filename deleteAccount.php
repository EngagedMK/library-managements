<?php
    session_start();
    require('config.php');

    if (!isset($_GET['id'])) {
        header("Location: account.php?status=error&message=No ID provided");
        exit();
    }

    $id = intval($_GET['id']); // Sanitize ID
    $sql = "DELETE FROM TaiKhoan WHERE idTaiKhoan = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $id);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: account.php?status=success&message= Xóa thành công tài khoản!");
            exit();
        } else {
            header("Location: account.php?status=error&message= Xóa không thành công tài khoản!");
            exit();
        }
    } else {
        header("Location: account.php?status=error&message= Lỗi truy vấn DATABASE");
        exit();
    }
?>
