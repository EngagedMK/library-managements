<?php
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

// Tính tổng số bản ghi
$totalCountSql = "
    SELECT COUNT(*) AS totalCount
    FROM MuonTra ms
    JOIN TaiKhoan nd ON ms.idTaiKhoan = nd.idTaiKhoan
    JOIN TaiLieu tl ON ms.idTaiLieu = tl.idTaiLieu
    WHERE ms.trangThai = 'Đang Mượn'
      AND tl.tenTaiLieu LIKE CONCAT('%', ?, '%')
      $statusCondition
";
$totalCountStmt = $conn->prepare($totalCountSql);
$totalCountStmt->bind_param("s", $searchTerm);
$totalCountStmt->execute();
$totalCountResult = $totalCountStmt->get_result();
$totalCount = $totalCountResult->fetch_assoc()['totalCount'] ?? 0;

$totalPages = ceil($totalCount / $itemsPerPage);

// Lấy dữ liệu danh sách mượn sách
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
        ms.trangThai = 'Đang Mượn'
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
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách mượn sách</title>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.4.1/css/mdb.min.css' rel='stylesheet'>
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-body">
                <h2 class="pt-3 pb-4  text-center font-bold deep-purple-text">Danh sách sách đang mượn</h2>

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

                <!-- Bảng hiển thị danh sách -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tên tài liệu</th>
                                <th>Ngày mượn</th>
                                <th>Ngày trả</th>
                                <th>Tiền cọc</th>
                                <th>Tiền đang bị phạt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php $stt = $offset + 1; ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $stt; ?></td>
                                        <td><?= htmlspecialchars($row['tenTaiLieu']); ?></td>
                                        <td><?= htmlspecialchars($row['ngayMuon']); ?></td>
                                        <td><?= htmlspecialchars($row['ngayTra']); ?></td>
                                        <td><?= number_format($row['tienCoc']); ?> đồng</td>
                                        <td><?= number_format(max(0, $row['daysLate'] * 25000)); ?> đồng</td>
                                    </tr>
                                    <?php $stt++; ?>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Không có tài liệu nào đang được mượn.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $currentPage == 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?search=<?= urlencode($searchTerm); ?>&status=<?= $status; ?>&page=<?= $currentPage - 1; ?>">Trước</a>
                    </li>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active_pagination' : ''; ?>">
                            <a class="page-link <?= $i == $currentPage ? 'active_text' : ''; ?>" href="?search=<?= urlencode($searchTerm); ?>&status=<?= $status; ?>&page=<?= $i; ?>">
                                <?= $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?search=<?= urlencode($searchTerm); ?>&status=<?= $status; ?>&page=<?= $currentPage + 1; ?>">Sau</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <style>
        .stylish-select, .stylish-input {
            border-radius: 20px;
            padding: 10px;
            font-size: 16px;
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
</body>
</html>
