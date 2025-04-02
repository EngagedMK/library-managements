
<?php 
session_start(); 

if (!isset($_SESSION['userName']) || strtolower($_SESSION['role']) != ('admin' || 'thuthu') || strtolower($_SESSION['status']) != 'hoatdong') {
    include('logout.php');
    exit();
}

require('config.php');

// Lấy vai trò hiện tại của người dùng đăng nhập
$currentUserRole = strtolower($_SESSION['role']);

// Điều kiện tìm kiếm
$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = mysqli_real_escape_string($conn, $_GET['search']);
}

// Lấy tháng từ form tìm kiếm, nếu không có thì lấy tháng hiện tại
$selectedMonth = !empty($_GET['month']) ? $_GET['month'] : date('Y-m');
list($selectedYear, $selectedMonth) = explode('-', $selectedMonth);

    // Mặc định cho admin
    $sqlCount = "SELECT COUNT(*) as total FROM muontra 
    WHERE (idTaiKhoan LIKE '%$searchTerm%' OR idTaiLieu LIKE '%$searchTerm%') 
    AND YEAR(ngayTra) = '$selectedYear' 
    AND MONTH(ngayTra) = '$selectedMonth'";
    $sqlLogin = "SELECT * FROM muontra 
    WHERE (idTaiKhoan LIKE '%$searchTerm%' OR idTaiLieu LIKE '%$searchTerm%') 
    AND YEAR(ngayTra) = '$selectedYear' 
    AND MONTH(ngayTra) = '$selectedMonth'";

// Phân trang
$rowsPerPage = 10;
$currentPage = max((int)($_GET['page'] ?? 1), 1);

$totalRowsResult = mysqli_query($conn, $sqlCount);
$totalRows = mysqli_fetch_assoc($totalRowsResult)['total'] ?? 0;
$totalPages = max(ceil($totalRows / $rowsPerPage), 1);
$offset = ($currentPage - 1) * $rowsPerPage;

$sqlLogin .= " LIMIT $offset, $rowsPerPage";
$result = mysqli_query($conn, $sqlLogin);
?>

<?php



//lấy dữ liệu từ các bảng
$sql = "SELECT t.tenDangNhap, tl.tenTaiLieu, m.ngayMuon, m.ngayTra, m.tienCoc, m.tienPhat, 
        (m.tienCoc ) AS tongThu
        FROM muontra m
        JOIN taikhoan t ON m.idTaiKhoan = t.idTaiKhoan 
        JOIN tailieu tl ON m.idTaiLieu = tl.idTaiLieu
        WHERE (t.tenDangNhap LIKE '%$searchTerm%' 
            OR tl.tenTaiLieu LIKE '%$searchTerm%')
          AND YEAR(m.ngayTra) = '$selectedYear' 
          AND MONTH(m.ngayTra) = '$selectedMonth'
        LIMIT $offset, $rowsPerPage";





// $sql = "SELECT * FROM muontra WHERE ngayTra LIKE '$selectedMonth%'";
// $result = $conn->query($sql);
$sql = "SELECT t.tenDangNhap, tl.tenTaiLieu, m.ngayMuon, m.ngayTra, m.tienCoc, m.tienPhat, 
        (m.tienCoc) AS tongThu
        FROM muontra m
        JOIN taikhoan t ON m.idTaiKhoan = t.idTaiKhoan 
        JOIN tailieu tl ON m.idTaiLieu = tl.idTaiLieu
        WHERE (t.tenDangNhap LIKE '%$searchTerm%' 
            OR tl.tenTaiLieu LIKE '%$searchTerm%') AND YEAR(m.ngayTra) = '$selectedYear' 
          AND MONTH(m.ngayTra) = '$selectedMonth'
        ORDER BY m.ngayTra DESC
        LIMIT $offset, $rowsPerPage";

$result = mysqli_query($conn, $sql);

// Tính tổng thu theo tháng
$sqlTongThuTheoThang = "
    SELECT 
        (SELECT SUM(tienCoc) FROM muontra 
         WHERE trangThai = 'Đang mượn' 
         AND YEAR(ngayMuon) = '$selectedYear' 
         AND MONTH(ngayMuon) = '$selectedMonth') AS tongTienCoc,
        
        (SELECT SUM(tienPhat) FROM muontra 
         WHERE trangThai = 'Đã trả' 
         AND YEAR(ngayTra) = '$selectedYear' 
         AND MONTH(ngayTra) = '$selectedMonth') AS tongTienPhat
";

$resultTongThuTheoThang = mysqli_query($conn, $sqlTongThuTheoThang); //truy vấn sql 
$row = mysqli_fetch_assoc($resultTongThuTheoThang);// lấy dữ liệu từ kết quảquả

$tongTienCocThang = $row['tongTienCoc'] ?? null ; // gán giá trị từ kết quả truy vấn sql(&row) nếu ko có thì giá trị bằng 00
$tongTienPhatThang = $row['tongTienPhat'] ;
$tongThuTheoThang = $tongTienCocThang + $tongTienPhatThang;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.4.1/css/mdb.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/style.css">
</head>
<div class="body_div">
  <?php include("menu.php");  ?> 

    <main>
            <body class="hm-gradient">
                <?php include("components/toast.php");  ?>
                    <div class="container mt-3">
                        
                        <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h2 class="pt-3 pb-4 text-center font-bold font-up deep-purple-text">Quản lý thu </h2>
                            <form method="GET" action="" class="mb-4">
                                <div class="input-group md-form form-sm form-2 pl-0">
                                     <!-- Chọn tháng margin-right : 8px --> 
                                     <input type="month" name="month" style="padding: 4px 8px; line-height: 1.2rem;" value="<?php echo isset($_GET['month']) ? $_GET['month'] : date('Y-m'); ?>">
                                     <input class="form-control my-0 py-1 pl-3 purple-border" type="text" placeholder="Tìm kiếm..." aria-label="Search" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">

                                 
                               
                                    <button class="input-group-addon waves-effect purple lighten-2" type="submit" id="basic-addon1">
                                            <i class="fa fa-search white-text" aria-hidden="true"></i>
                                    </button>
                                </div>
                                
                            </form>

                            </div>
                        </div>
                        
                        
                        <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tài khoản </th>
                                    <th>Tên tài liệu</th>
                                    <th>Ngày mượn </th>
                                    <th>Ngày trả </th>
                                    <th>Tiền cọc</th>
                                    <th>Tiền phạt</th>
                                    
                                    
                                    <th></th>
                                </tr>
                            </thead>
                        
                            <tbody>
                        
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php $stt=1; ?>

                           <?php foreach ($result as $item) :
                           
                            ?>
                                <tr>
                                    <td scope="row"><?php echo $stt; ?></td>
                                    <td><?php echo htmlspecialchars($item['tenDangNhap']); ?></td>
                                    <td><?php echo htmlspecialchars($item['tenTaiLieu']); ?></td>
                                    <td><?php $date = new DateTime($item['ngayMuon']);
                                                echo $date->format('d/m/Y'); 
                                    
                                    ?></td>
                                    
                                    <td><?php //echo $item['ngayTra'];
                                    // 
                                      $date = new DateTime($item['ngayTra']);
                                    echo $date->format('d/m/Y'); 
                                    
                                     ?></td>
                                    <td><?php echo number_format($item['tienCoc'] )   . ' VNĐ'; ?></td>
                                    <td><?php echo number_format($item['tienPhat']) . ' VNĐ' ; ?></td>
                                    
                                   
                                    
                                    
                                    <?php $stt++; ?>
                                </tr>
                               
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <tr>
                            <td colspan="5" style="text-align:right; font-weight:bold;">Tổng thu tháng <?php echo "$selectedMonth-$selectedYear"; ?>:</td>
                            <td colspan="2" style="font-weight:bold;"><?php echo number_format($tongThuTheoThang, 0, ',', '.') . ' VNĐ'; ?></td>
                        </tr>  
                        
                    </tbody>
                        </table>
                        <!-- Pagination -->
        
                        <ul class="pagination justify-content-center">

                            <!-- Các trang -->

    <!-- Hiển thị phân trang -->
   
    <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo $currentPage == 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?search=<?php echo urlencode($searchTerm); ?>&month=<?php echo urlencode($_GET['month'] ?? ''); ?>&page=<?php echo $currentPage - 1; ?>" style="margin-right: 12px">Trước</a>
                                </li>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo $i == $currentPage ? 'active_pagination' : ''; ?>">
                                        <a class="page-link <?php echo $i == $currentPage ? 'active_text' : ''; ?>" href="?search=<?php echo urlencode($searchTerm); ?>&month=<?php echo urlencode($_GET['month'] ?? '');?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?php echo $currentPage == $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?search=<?php echo urlencode($searchTerm); ?>&month=<?php echo urlencode($_GET['month'] ?? '');?>&page=<?php echo $currentPage + 1; ?>" style="margin-left: 12px">Sau</a>
                                </li>
                            </ul>
                        </ul>
                        </div>
                    </div>
                </div>
            </div>
        </body> 
        <p class="copyright">
            &copy; 2024 - <span>Nhóm 2</span> All Rights Reserved.
        </p>
        </main>
    </div>

</html>
<script src="js/menu.js"></script>
<!-- 
<style>
tr:hover {
    background-color: red ;
}
</style> -->


