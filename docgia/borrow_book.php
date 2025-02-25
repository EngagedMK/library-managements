<?php
session_start();
require('../config.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['userName']) || strtolower($_SESSION['role']) != 'docgia' || strtolower($_SESSION['status']) != 'hoatdong') {
    include('../logout.php');
    exit();
}

$idTaiLieu = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$idTaiKhoan = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['generateQR'])) {
        // Lấy dữ liệu từ form
        $ngayMuon = $_POST['ngayMuon'];
        $ngayTra = $_POST['ngayTra'];
        $tienCoc = $_POST['tienCoc'];


        if (strtotime($ngayTra) <= strtotime($ngayMuon)) {
            $error = "Ngày trả phải lớn hơn ngày mượn!";
        } else {
            if ($tienCoc > 0) {
                // Tạo URL QR thanh toán
                $bank = "MB";
                $accountName = "Nguyen Xuan Khanh";
                $accountNumber = "6688888882003";
                $note = urlencode("Muon sach: ID Tai Lieu $idTaiLieu");
                $qrUrl = "https://img.vietqr.io/image/{$bank}-{$accountNumber}-compact.png?amount={$tienCoc}&addInfo={$note}";

                // Lưu thông tin vào session
                $_SESSION['qrUrl'] = $qrUrl;
                $_SESSION['ngayMuon'] = $ngayMuon;
                $_SESSION['ngayTra'] = $ngayTra;
                $_SESSION['tienCoc'] = $tienCoc;

                // Hiển thị dialog mã QR
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var modal = new bootstrap.Modal(document.getElementById('qrModal'));
                        modal.show();
                    });
                </script>";
            } else {
                
            $sqlCheck = "SELECT soLuong FROM TaiLieu WHERE idTaiLieu = $idTaiLieu";
            $resultCheck = mysqli_query($conn, $sqlCheck);
            $book = mysqli_fetch_assoc($resultCheck);

            if ($book > 0) {
                // Thêm thông tin mượn sách vào bảng MuonTra
                $sqlInsert = "INSERT INTO MuonTra (idTaiLieu, idTaiKhoan, ngayMuon, ngayTra, tienCoc, trangThai)
                            VALUES ('$idTaiLieu', '$idTaiKhoan', '$ngayMuon', '$ngayTra', '$tienCoc', 'Đang mượn')";
                if (mysqli_query($conn, $sqlInsert)) {
                    // Trừ số lượng sách
                    $sqlUpdate = "UPDATE TaiLieu SET soLuong = soLuong - 1 WHERE idTaiLieu = $idTaiLieu";
                    mysqli_query($conn, $sqlUpdate);

                    unset($_SESSION['qrUrl'], $_SESSION['ngayMuon'], $_SESSION['ngayTra'], $_SESSION['tienCoc']);
                    header("Location: book.php?status=success&message=Mượn sách thành công!");
                    exit();
                } else {
                    $error = "Có lỗi xảy ra: " . mysqli_error($conn);
                }
            } else {
                $error = "Sách đã hết, không thể mượn!";
            }

        header("Location: book.php?status=success&message=Mượn sách thành công!");
            exit();
        }}
    } else {
        // Lấy thông tin từ session
        $ngayMuon = $_SESSION['ngayMuon'];
        $ngayTra = $_SESSION['ngayTra'];
        $tienCoc = $_SESSION['tienCoc'];

        // Kiểm tra số lượng sách
        $sqlCheck = "SELECT soLuong FROM TaiLieu WHERE idTaiLieu = $idTaiLieu";
        $resultCheck = mysqli_query($conn, $sqlCheck);
        $book = mysqli_fetch_assoc($resultCheck);

        if ($book > 0) {
            // Thêm thông tin mượn sách vào bảng MuonTra
            $sqlInsert = "INSERT INTO MuonTra (idTaiLieu, idTaiKhoan, ngayMuon, ngayTra, tienCoc, trangThai)
                          VALUES ('$idTaiLieu', '$idTaiKhoan', '$ngayMuon', '$ngayTra', '$tienCoc', 'Đang mượn')";
            if (mysqli_query($conn, $sqlInsert)) {
                // Trừ số lượng sách
                $sqlUpdate = "UPDATE TaiLieu SET soLuong = soLuong - 1 WHERE idTaiLieu = $idTaiLieu";
                mysqli_query($conn, $sqlUpdate);

                unset($_SESSION['qrUrl'], $_SESSION['ngayMuon'], $_SESSION['ngayTra'], $_SESSION['tienCoc']);
                header("Location: book.php?status=success&message=Mượn sách thành công!");
                exit();
            } else {
                $error = "Có lỗi xảy ra: " . mysqli_error($conn);
            }
        } else {
            $error = "Sách đã hết, không thể mượn!";
        }
    }
}

// Lấy thông tin sách
$sqlBook = "SELECT * FROM TaiLieu WHERE idTaiLieu = $idTaiLieu";
$resultBook = mysqli_query($conn, $sqlBook);
$book = mysqli_fetch_assoc($resultBook);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mượn sách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include("menu.php"); ?>

    <div class="container mt-5">
        <h1 class="text-center">Mượn Sách</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-danger"><?= $success ?></div>
        <?php endif; ?>

        <div class="row">
            <?php if ($book): ?>
                <div class="col-3">
                    <div class="card mb-4">
                        <img src="../<?= htmlspecialchars($book['img_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($book['tenTaiLieu']) ?>" style="max-height: 300px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($book['tenTaiLieu']) ?></h5>
                            <p class="card-text">Tác giả: <?= htmlspecialchars($book['tacGia']) ?></p>
                            <p class="card-text">Số lượng còn: <?= htmlspecialchars($book['soLuong']) ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-8">
                    <form method="POST" class="mt-4">
                        <div class="mb-3">
                            <label for="ngayMuon" class="form-label">Ngày mượn</label>
                            <input type="date" name="ngayMuon" id="ngayMuon" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="ngayTra" class="form-label">Ngày trả</label>
                            <input type="date" name="ngayTra" id="ngayTra" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="tienCoc" class="form-label">Tiền cọc</label>
                            <input type="number" step="0.01" name="tienCoc" id="tienCoc" class="form-control" value="<?= htmlspecialchars($book['tienCoc']); ?>" readonly required>
                        </div>
                        <button type="submit" name="generateQR" class="btn btn-primary"><?php echo $book['tienCoc'] > 0 ? "Hiển thị mã QR" : "Mượn sách" ?></button>
                    </form>
                </div>
            <?php else: ?>
                <p class="text-danger">Không tìm thấy thông tin sách!</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Dialog hiển thị QR -->
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrModalLabel">Thanh toán tiền cọc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="<?= $_SESSION['qrUrl'] ?>" alt="QR Code">
                </div>
                <div class="modal-footer">
                    <form method="POST">
                        <button type="submit" name="confirmPayment" class="btn btn-success">Xác nhận đã thanh toán</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<style>
    .content {
        margin-top: 48px;
    }

    .header_content {
        color: #5162CE;
        text-align: center;
    }

    .sub_content {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .d_f_c {
        display: flex;
        justify-content: center;
    }

    .sub_content_c {
        font-size: 0.9rem;
        color: #888;
    }

    .card-title {
        font-size: 1.2rem;
        color: #333;
        font-weight: bold;
    }

    .card-text {
        font-size: 0.9rem;
        color: #666;
    }

    .card-img-top {
        border-bottom: 1px solid #999;
        width: 100%;
        /* height: 80px; */
    }

    .soluong {
        display: flex;
        justify-content: end;
        align-items: center;
    }

    .modal-content {
        width: auto;
    }

</style>

