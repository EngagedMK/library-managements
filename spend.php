<?php
session_start();
require('config.php');

// Lấy trang hiện tại từ query string, mặc định là trang 1
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

// Số bản ghi trên mỗi trang
$itemsPerPage = 10;

// Tính toán giá trị offset
$offset = ($currentPage - 1) * $itemsPerPage;

// Tính tổng số bản ghi và số trang
$totalCountSql = "
    SELECT COUNT(*) AS totalCount
    FROM MuonTra ms
    JOIN TaiKhoan nd ON ms.idTaiKhoan = nd.idTaiKhoan
    JOIN TaiLieu tl ON ms.idTaiLieu = tl.idTaiLieu
    WHERE ms.trangThai = 'Đã trả'
      AND tl.tenTaiLieu LIKE CONCAT('%', ?, '%')
";
$totalCountStmt = $conn->prepare($totalCountSql);
$totalCountStmt->bind_param("s", $searchTerm);
$totalCountStmt->execute();
$totalCountResult = $totalCountStmt->get_result();
$totalCount = $totalCountResult->fetch_assoc()['totalCount'] ?? 0;

$totalPages = ceil($totalCount / $itemsPerPage);

// Lấy danh sách mượn sách theo trạng thái "đang mượn"
$sql = "
    SELECT 
        ms.idMuonTra,
        tl.idTaiLieu,
        nd.tenDangNhap, 
        tl.tenTaiLieu, 
        ms.ngayMuon, 
        ms.ngayTra, 
        ms.tienCoc, 
        ms.tienPhat, 
        DATEDIFF(CURDATE(), ms.ngayTra) AS daysLate -- Tính số ngày trễ
    FROM 
        MuonTra ms
    JOIN 
        TaiKhoan nd ON ms.idTaiKhoan = nd.idTaiKhoan
    JOIN 
        TaiLieu tl ON ms.idTaiLieu = tl.idTaiLieu
    WHERE 
        ms.trangThai = 'Đã trả'
        AND tl.tenTaiLieu LIKE CONCAT('%', ?, '%')
    ORDER BY 
        ms.idMuonTra DESC
    LIMIT ?, ?
";

$tongThuSql = "SELECT SUM(tienCoc) AS totalRevenue FROM MuonTra WHERE trangThai = 'Đã trả'";
$resultTotal = $conn->query($tongThuSql);

if ($resultTotal && $row = $resultTotal->fetch_assoc()) {
    $totalRevenue = $row['totalRevenue'] ?? 0; 
} else {
    $totalRevenue = 0;  
}


$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $searchTerm, $offset, $itemsPerPage);
$stmt->execute();
$result = $stmt->get_result();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách mượn sách</title>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.4.1/css/mdb.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="body_div">
        <?php include("menu.php"); ?>

        <main>
            <div class="container mt-3">
                <?php include("components/toast.php"); ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="pt-3 pb-4 text-center font-bold font-up deep-purple-text">Danh sách Chi</h2>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tên tài khoản</th>
                                        <th>Tên tài liệu</th>
                                        <th>Ngày mượn</th>
                                        <th>Ngày trả</th>
                                        <th>Tiền cọc</th>
                                        <!-- <th>Tiền phạt</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php $stt = 1; ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $stt; ?></td>
                                                <td><?= htmlspecialchars($row['tenDangNhap']); ?></td>
                                                <td><?= htmlspecialchars($row['tenTaiLieu']); ?></td>
                                                <td><?= htmlspecialchars($row['ngayMuon']); ?></td>
                                                <td><?= htmlspecialchars($row['ngayTra']); ?></td>
                                                <td><?= number_format($row['tienCoc']); ?> đồng</td>
                                                <!-- <td><?= number_format($row['tienPhat']); ?> đồng</td> -->
                                                <!-- <td>
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="idMuonTra" value="<?= $row['idMuonTra']; ?>">
                                                        <input type="hidden" name="idTaiLieu" value="<?= $row['idTaiLieu']; ?>">
                                                        <input type="hidden" name="daysLate" value="<?= $row['daysLate']; ?>">
                                                        <input type="hidden" name="tienCoc" value="<?= $row['tienCoc']; ?>">
                                                        <input type="hidden" name="tienCoc" value="<?= $row['tienCoc']; ?>">
                                                        <button type="submit" name="returnBook" class="btn btn-primary">Trả sách</button>
                                                    </form>
                                                </td> -->
                                            </tr>
                                            <?php $stt++; ?>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Không có tài liệu nào đang được mượn.</td>
                                        </tr>
                                    <?php endif; ?>

                                    <!-- Hàng tổng tiền của tất cả dữ liệu -->
                                    <tr>
                                        <td colspan="5" class="text-right font-weight-bold">Tổng tiền tất cả:</td>
                                        <td class="font-weight-bold"><?= number_format((float) $totalRevenue, 0); ?> đồng</td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Pagination -->
        
                        <ul class="pagination justify-content-center">
                            <!-- Nút Trước -->
                            <li class="page-item <?php echo $currentPage == 1 ? 'disabled' : ''; ?>" >
                                <a class="page-link" href="?search=<?php echo urlencode($searchTerm); ?>&page=<?php echo $currentPage - 1; ?>" style="margin-right: 12px">
                                    Trước
                                </a>
                            </li>

                            <!-- Các trang -->
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo $i == $currentPage ? 'active_pagination' : ''; ?>">
                                    <a class="page-link <?php echo $i == $currentPage ? 'active_text' : ''; ?>" href="?search=<?php echo urlencode($searchTerm); ?>&page=<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <!-- Nút Sau -->
                            <li class="page-item <?php echo $currentPage == $totalPages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?search=<?php echo urlencode($searchTerm); ?>&page=<?php echo $currentPage + 1; ?>" style="margin-left: 12px">
                                    Sau
                                </a>
                            </li>
                        </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="js/menu.js"></script>
</body>
</html>

<style>

    .d_f_c {
        display: flex;
        justify-content: center;
        align-items: center;
    }


    .alert {
        margin: 50px auto;
        max-width: 600px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        padding: 20px;
    }
    .alert img {
        border: 2px solid #ccc;
        border-radius: 10px;
    }
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5); 
        backdrop-filter: blur(5px);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000; 
    }

    .qr-modal {
        background: #fff; 
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        position: relative;
        width: 300px;
    }

    .qr-modal img {
        width: 200px;
        height: 200px;
        margin: 20px auto;
        border: 2px solid #ccc;
        border-radius: 10px;
    }

    .close-btn {
        background: #ff5e57;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 16px;
    }

    .close-btn:hover {
        background: #ff2e2b;
    }

    .alert-success {
        position: fixed; 
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%); 
        margin: 0; 
        max-width: 600px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        padding: 20px;
        z-index: 1000;
        background: #dff0d8; 
        color: #3c763d;
        text-align: center;
    }

    .overlay-success {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5); 
        backdrop-filter: blur(5px); 
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 999; 
    }

    .alert-success {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        margin: 0;
        max-width: 600px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        padding: 20px;
        z-index: 1000;
        background: #dff0d8;
        color: #3c763d;
        text-align: center;
    }

    .close-btn {
        background: #ff5e57;
        color: #fff;
        border: none;
        padding: 4px 4px;
        border-radius: 5px;
        cursor: pointer;
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 16px;
    }

    .close-btn:hover {
        background: #ff2e2b;
    }





</style>
