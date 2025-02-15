<?php 
     session_start(); 

     if (!isset($_SESSION['userName']) || strtolower($_SESSION['role']) != ('admin' || 'thuthu') || strtolower($_SESSION['status']) != 'hoatdong') {
        include('logout.php');
        exit();
      }

    require('config.php');
    $sqlLogin = "SELECT * FROM TaiKhoan";
    
    $result = mysqli_query($conn, $sqlLogin);

    $searchTerm = '';
    if (isset($_GET['search'])) {
        $searchTerm = mysqli_real_escape_string($conn, $_GET['search']);
    }
    $sqlSearch = "SELECT * FROM TaiKhoan WHERE tenDangNhap LIKE '%$searchTerm%'";
    $result = mysqli_query($conn, $sqlSearch);

    
    // Số dòng trên mỗi trang
    $rowsPerPage = 5;

    // Trang hiện tại
    $currentPage = max((int)($_GET['page'] ?? 1), 1);

    // Tổng số dòng
    $totalRowsQuery = "SELECT COUNT(*) as total FROM TaiKhoan WHERE tenDangNhap LIKE '%$searchTerm%'";
    $totalRowsResult = mysqli_query($conn, $totalRowsQuery);
    $totalRows = mysqli_fetch_assoc($totalRowsResult)['total'] ?? 0;

    // Tổng số trang
    $totalPages = max(ceil($totalRows / $rowsPerPage), 1);

    // Offset cho SQL
    $offset = ($currentPage - 1) * $rowsPerPage;

    // Lấy dữ liệu của trang hiện tại
    $sqlLogin = "SELECT * FROM TaiKhoan WHERE tenDangNhap LIKE '%$searchTerm%' LIMIT $offset, $rowsPerPage";
    $result = mysqli_query($conn, $sqlLogin);

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
                                <h2 class="pt-3 pb-4 text-center font-bold font-up deep-purple-text">Tài khoản</h2>
                                <form method="GET" action="" class="mb-4">
                                <div class="input-group md-form form-sm form-2 pl-0">
                                        <input class="form-control my-0 py-1 pl-3 purple-border" type="text" placeholder="Tìm kiếm..." aria-label="Search" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>">
                                        <button class="input-group-addon waves-effect purple lighten-2" type="submit" id="basic-addon1"><a><i class="fa fa-search white-text" aria-hidden="true"></i></a></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="my-2 d-flex justify-content-end w-80 mr-3 ">
                            <td colspan="4"  style="text-align: center;"><a href="addAccount.php" class="p-2 bg-primary text-white rounded" style="display: flex; justify-content:cente; align-items:center" ><i class="fa fa-plus-square-o" aria-hidden="true" style="font-size:20px;margin-right: 8px; "></i></i>Thêm mới</a></td>
                        </div>
                        
                        <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tên đăng nhập</th>
                                    <th>mật khẩu</th>
                                    <th>Vai trò</th>
                                    <th>Trạng thái</th>
                                    <th></th>
                                </tr>
                            </thead>
                        
                            <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php $stt=1; ?>
                            <?php foreach ($result as $item) :?>
                                <tr>
                                    <td scope="row"><?php echo $stt; ?></td>
                                    <td><?php echo $item['tenDangNhap'] ;?></td>
                                    <td><?php echo $item['matKhau']; ?></td>
                                    <td><?php echo $item['vaiTro'] ;?></td>
                                    <td><?php echo $item['trangThai']; ?></td>
                                    <td>
                                    <div class="d-flex align-items-center justify-content-center" >
                                        <a href="editAccount.php?id=<?php echo $item['idTaiKhoan']; ?>" class="p-1 mx-1"><i class="fa fa-pencil-square-o" aria-hidden="true" style="font-size:20px;color: #F6C200"></i></a>  
                                        <a href="deleteAccount.php?id=<?php echo $item['idTaiKhoan']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này không?')" class="p-1 mx-1">
                                            <i class="fa fa-trash-o" aria-hidden="true" style="font-size:20px;color: #F63623"></i>
                                        </a>
                                    </div>
                                    </td>
                                    <?php $stt++; ?>
                                </tr>
                            <?php endforeach; ?>
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
        </body> 
        <p class="copyright">
            &copy; 2024 - <span>Nhóm 2</span> All Rights Reserved.
        </p>
        </main>
    </div>

</html>
<script src="js/menu.js"></script>


