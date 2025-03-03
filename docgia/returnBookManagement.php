<?php
// session_start();
require('../config.php');

// Lấy dữ liệu tìm kiếm
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Lấy trang hiện tại từ query string, mặc định là trang 1
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 5;
$offset = ($currentPage - 1) * $itemsPerPage;

// Điều kiện lọc trạng thái
$statusCondition = '';
if ($status === 'conhan') {
    $statusCondition = "AND DATEDIFF(CURDATE(), ms.ngayTra) <= 0";
} elseif ($status === 'quahan') {
    $statusCondition = "AND DATEDIFF(CURDATE(), ms.ngayTra) > 0";
}

// Tính tổng số bản ghi và số trang
$totalCountSql = "
    SELECT COUNT(*) AS totalCount
    FROM MuonTra ms
    JOIN TaiKhoan nd ON ms.idTaiKhoan = nd.idTaiKhoan
    JOIN TaiLieu tl ON ms.idTaiLieu = tl.idTaiLieu
    WHERE ms.trangThai = 'Đã trả'
      AND tl.tenTaiLieu LIKE CONCAT('%', ?, '%')
      $statusCondition
";
$totalCountStmt = $conn->prepare($totalCountSql);
$totalCountStmt->bind_param("s", $searchTerm);
$totalCountStmt->execute();
$totalCountResult = $totalCountStmt->get_result();
$totalCount = $totalCountResult->fetch_assoc()['totalCount'] ?? 0;

$totalPages = ceil($totalCount / $itemsPerPage);

// Lấy dữ liệu theo trang hiện tại
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
        DATEDIFF(CURDATE(), ms.ngayTra) AS daysLate
    FROM 
        MuonTra ms
    JOIN 
        TaiKhoan nd ON ms.idTaiKhoan = nd.idTaiKhoan
    JOIN 
        TaiLieu tl ON ms.idTaiLieu = tl.idTaiLieu
    WHERE 
        ms.trangThai = 'Đã trả'
        AND tl.tenTaiLieu LIKE CONCAT('%', ?, '%')
        $statusCondition
    ORDER BY 
        ms.idMuonTra DESC
    LIMIT ?, ?
";

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
    <!-- <link rel="stylesheet" href="../css/style.css"> -->
</head>
<body>
    <div class="body_div">
        <main>
            <div class="container mt-3">
                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="pt-3 pb-4 text-center font-bold font-up deep-purple-text">Danh sách sách đã trả</h2>
                        
                        <!-- Bộ lọc tìm kiếm -->
                        <form method="GET" class="mb-4">
                            <div class="row" style="display: flex; align-items: center;">
                                <div class="col-md-2">
                                    <select name="status" class="form-control " style="padding: 4px 8px;border-radius: 8px;margin:0">
                                        <option value="">Tất cả</option>
                                        <option value="conhan" <?= $status === 'conhan' ? 'selected' : '' ?>>Còn hạn</option>
                                        <option value="quahan" <?= $status === 'quahan' ? 'selected' : '' ?>>Quá hạn</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="search" style="padding: 1px 8px;border-radius: 8px;border: 1px solid #D9D9D9" placeholder="Tìm kiếm tài liệu..." value="<?= htmlspecialchars($searchTerm); ?>">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary btn-block" style="padding: 4px 8px;border-radius: 8px; height: 36px;" >Tìm kiếm</button>
                                </div>
                            </div>
                        </form>
                        
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <!-- <th>Tên tài khoản</th> -->
                                        <th>Tên tài liệu</th>
                                        <th>Ngày mượn</th>
                                        <th>Ngày trả</th>
                                        <!-- <th>Tiền cọc</th> -->
                                        <th>Tiền phạt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php $stt = 1; ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $stt; ?></td>
                                                <!-- <td><?= htmlspecialchars($row['tenDangNhap']); ?></td> -->
                                                <td><?= htmlspecialchars($row['tenTaiLieu']); ?></td>
                                                <td><?= htmlspecialchars($row['ngayMuon']); ?></td>
                                                <td><?= htmlspecialchars($row['ngayTra']); ?></td>
                                                <!-- <td><?= number_format($row['tienCoc']); ?> đồng</td> -->
                                                <td><?= number_format($row['tienPhat'] === null ? 0 : $row['tienPhat']); ?> đồng</td>
                                               
                                            </tr>
                                            <?php $stt++; ?>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Không có tài liệu nào đang được mượn.</td>
                                        </tr>
                                    <?php endif; ?>
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

    .active_pagination {
    background-color: #4285f4;
    color: #fff;
    border-radius: 99px;
    width: 30px;
    height: 30px;
    margin: 0 3px;
    }

    .active_text {
    color: #fff !important;
    }


</style>
