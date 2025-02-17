<?php
    session_start();
    require('config.php');

    if (!isset($_GET['id'])) {
        header("Location: bookManagement.php?status=error&message=No ID provided");
        exit();
    }

    $id = intval($_GET['id']); // Sanitize ID
    $sql = "DELETE FROM TaiLieu WHERE idTaiLieu = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $id);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: bookManagement.php?status=success&message= Xóa thành công sách!");
            exit();
        } else {
            header("Location: bookManagement.php?status=error&message= Xóa không thành công sách!");
            exit();
        }
    } else {
        header("Location: bookManagement.php?status=error&message= Lỗi truy vấn DATABASE");
        exit();
    }
?>
