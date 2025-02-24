<?php
    session_start();
    require('../config.php');

    if (!isset($_SESSION['userName']) || strtolower($_SESSION['role']) != 'docgia' || strtolower($_SESSION['status']) != 'hoatdong') {
        include('../logout.php');
        exit();
    }

    // Lấy dữ liệu từ form tìm kiếm
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
    $searchBy = isset($_GET['searchBy']) ? $_GET['searchBy'] : 'tenTaiLieu'; // Mặc định tìm theo tên tài liệu

    // Số bản ghi trên mỗi trang
    $itemsPerPage = 8;

    // Trang hiện tại
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($currentPage < 1) $currentPage = 1;

    // Vị trí bắt đầu
    $offset = ($currentPage - 1) * $itemsPerPage;

    // Đếm tổng số sách theo tiêu chí tìm kiếm
    $totalBooksQuery = "SELECT COUNT(*) AS total FROM TaiLieu WHERE $searchBy LIKE '%$searchTerm%'";
    $totalBooksResult = mysqli_query($conn, $totalBooksQuery);
    $totalBooksRow = mysqli_fetch_assoc($totalBooksResult);
    $totalBooks = $totalBooksRow['total'];

    // Tổng số trang
    $totalPages = ceil($totalBooks / $itemsPerPage);

    // Lấy danh sách sách theo tiêu chí tìm kiếm
    $sqlBook = "SELECT * FROM TaiLieu WHERE $searchBy LIKE '%$searchTerm%' ORDER BY idTaiLieu DESC LIMIT $offset, $itemsPerPage";
    $result = mysqli_query($conn, $sqlBook);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm sách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include("menu.php"); ?>
    <?php include("../components/toastNewVesion.php"); ?>
    <div class="content mt-4">
        <h1 class="header_content">Danh sách sách</h1>
        <div class="container text-center mt-5 d_f_c">
            <div class="row" style="width: 80%">
                <div class="container mb-4" style="display: flex; justify-content: end;">
                <!-- Form tìm kiếm -->
                    <form method="GET" action="" class="row g-3 align-items-center">
                        <div class="col-auto">
                            <input type="text" name="search" class="form-control" placeholder="Nhập từ khóa..." value="<?= htmlspecialchars($searchTerm) ?>">
                        </div>
                        <div class="col-auto">
                            <select name="searchBy" class="form-select">
                                <option value="tenTaiLieu" <?= $searchBy === 'tenTaiLieu' ? 'selected' : '' ?>>Tên tài liệu</option>
                                <option value="tacGia" <?= $searchBy === 'tacGia' ? 'selected' : '' ?>>Tác giả</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                        </div>
                    </form>
                </div>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($book = mysqli_fetch_assoc($result)): ?>
                        <div class="col-md-3 mb-4">
                            <div class="card h-100" style="box-shadow: rgba(100, 100, 111, 0.5) 0px 7px 29px 0px;">
                                <!-- Hiển thị hình ảnh -->
                                <img src="../<?= htmlspecialchars($book['img_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($book['tenTaiLieu']) ?>" style="max-height: 300px">
                                
                                <!-- Nội dung sách -->
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($book['tenTaiLieu']) ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($book['tacGia']) ?></p>
                                    <div class="soluong">
                                        <span class="card-text text-truncate" style="margin-right: 4px">Kho:</span>
                                        <span class="card-text text-truncate"> <?= htmlspecialchars($book['soLuong']) ?></span>
                                    </div>
                                </div>
                                <div class="d_f_c" style="margin-top: 4px; margin-bottom:16px;">
                                <a href="borrow_book.php?id=<?= htmlspecialchars($book['idTaiLieu']) ?>" 
                                    class="btn btn-primary <?= $book['soLuong'] > 0 ? '' : 'disabled' ?>">
                                    <?= $book['soLuong'] > 0 ? 'Mượn sách' : 'Hết sách' ?>
                                    </a>

                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Không tìm thấy sách nào.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Phân trang -->
        <div class="pagination-container mt-4">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= ($currentPage == 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $currentPage - 1 ?>&search=<?= htmlspecialchars($searchTerm) ?>&searchBy=<?= htmlspecialchars($searchBy) ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&search=<?= htmlspecialchars($searchTerm) ?>&searchBy=<?= htmlspecialchars($searchBy) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($currentPage == $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $currentPage + 1 ?>&search=<?= htmlspecialchars($searchTerm) ?>&searchBy=<?= htmlspecialchars($searchBy) ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <?php include("footer.php"); ?>
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

    .pagination-container {
        margin-top: 20px;
    }

    .pagination .page-item.active .page-link {
        background-color: #5162CE;
        border-color: #5162CE;
        color: #fff;
    }

    .pagination .page-item .page-link {
        color: #5162CE;
    }

    .pagination .page-item.disabled .page-link {
        color: #ccc;
        pointer-events: none;
    }

</style>