<?php
session_start();
require('config.php');

if (!isset($_SESSION['userName']) || strtolower($_SESSION['role']) != ('admin' || 'thuthu') || strtolower($_SESSION['status']) != 'hoatdong') {
    include('logout.php');
    exit();
}

      if (strtolower($_SESSION['role']) === 'docgia' ) {
    include('logout.php');
    exit();
}

    // Truy vấn số liệu
$sqlCount = "SELECT COUNT(*) FROM TaiKhoan";
$sqlCountTaiLieu = "SELECT COUNT(*) FROM TaiLieu";
$sqlCountSachMuon = "SELECT COUNT(*) FROM MuonTra WHERE trangThai = 'Đang mượn'";
$sqlCountSachTra = "SELECT COUNT(*) FROM MuonTra WHERE trangThai = 'Đã trả'";

// Thực hiện truy vấn
$result = mysqli_query($conn, $sqlCount);
$resultTaiLieu = mysqli_query($conn, $sqlCountTaiLieu);
$resultSachMuon = mysqli_query($conn, $sqlCountSachMuon);
$resultSachTra = mysqli_query($conn, $sqlCountSachTra);

// Lấy kết quả trả về
$count = ($result) ? mysqli_fetch_array($result)[0] : 0;
$countTaiLieu = ($resultTaiLieu) ? mysqli_fetch_array($resultTaiLieu)[0] : 0;
$countSachMuon = ($resultSachMuon) ? mysqli_fetch_array($resultSachMuon)[0] : 0;
$countSachTra = ($resultSachTra) ? mysqli_fetch_array($resultSachTra)[0] : 0;

// Truy vấn số sách mượn và trả theo tháng
$sqlSachTheoThang = "
    SELECT MONTH(ngayMuon) AS thang, 
           COUNT(CASE WHEN trangThai = 'Đang mượn' THEN 1 END) AS soLuongMuon, 
           COUNT(CASE WHEN trangThai = 'Đã trả' THEN 1 END) AS soLuongTra,
           SUM(CASE WHEN trangThai = 'Đang mượn' THEN tienCoc ELSE 0 END) +
           SUM(CASE WHEN trangThai = 'Đã trả' THEN tienPhat ELSE 0 END) AS tongThu,
           SUM(CASE WHEN trangThai = 'Đã trả' THEN tienCoc ELSE 0 END) AS tongChi
    FROM MuonTra
    GROUP BY thang
    ORDER BY thang ASC
";

$resultSachTheoThang = mysqli_query($conn, $sqlSachTheoThang);

$soLuongMuon = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
$soLuongTra = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
$tongThuThang = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
$tongChiThang = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

while ($row = mysqli_fetch_assoc($resultSachTheoThang)) {
    $index = (int)$row['thang'] - 1; // Vì mảng bắt đầu từ 0
    $soLuongMuon[$index] = (int)$row['soLuongMuon'];
    $soLuongTra[$index] = (int)$row['soLuongTra'];
    $tongThuThang[$index] = (int)$row['tongThu'];
    $tongChiThang[$index] = (int)$row['tongChi'];
}

// Chuyển đổi dữ liệu sang JSON để sử dụng trong JavaScript
$soLuongMuonJson = json_encode($soLuongMuon);
$soLuongTraJson = json_encode($soLuongTra);
$tongThuThangJson = json_encode($tongThuThang);
$tongChiThangJson = json_encode($tongChiThang);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>" >
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<div class="body_div">
  <?php include("menu.php"); ?>

  <main>
    <?php include("components/toast.php"); ?>
    <h1 style="font-weight: 500; margin-bottom: 16px">My Dashboard</h1>
    <div class="row">
      <div class="col box_db">
        <div class='bx bxs-user' style="background-color: #AC39F4;box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;height: 40px;width: 40px;padding: 12px 12px;color: #fff; border-radius: 8px">
        </div>

        <div style="margin-left: 16px">
          <h6 class="text-muted font-semibold">Tài khoản</h6>
          <h6 class="font-extrabold mb-0"><?php echo $count ?></h6>
        </div>
      </div>
      <div class="col box_db">
        <div class='bx bxs-book-bookmark' style="background-color:rgb(57, 194, 244);box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;height: 40px;width: 40px;padding: 12px 12px;color: #fff; border-radius: 8px">
        </div>

        <div style="margin-left: 16px">
          <h6 class="text-muted font-semibold">Sách</h6>
          <h6 class="font-extrabold mb-0"><?php echo $countTaiLieu ?> Quyển</h6>
        </div>
      </div>
      <div class="col box_db">
        <div class='bx bxs-bookmark-alt-minus' style="background-color:rgb(57, 244, 91);box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;height: 40px;width: 40px;padding: 12px 12px;color: #fff; border-radius: 8px">
        </div>

        <div style="margin-left: 16px">
          <h6 class="text-muted font-semibold">Sách mượn</h6>
          <h6 class="font-extrabold mb-0"><?php echo $countSachMuon ?> Quyển</h6>
        </div>
      </div>
      <div class="col box_db">
        <div class='bx bxs-bookmark-alt-plus' style="background-color:rgb(244, 235, 57);box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;height: 40px;width: 40px;padding: 12px 12px;color: #fff; border-radius: 8px">
        </div>

        <div style="margin-left: 16px">
          <h6 class="text-muted font-semibold">Sách trả</h6>
          <h6 class="font-extrabold mb-0"><?php echo $countSachTra ?> Quyển</h6>
        </div>
      </div>
    </div>

    <div class=" ">
      
      <div class=" " style="margin-top: 30px;">


      <div class="row">
        <div class="col box_chart" style="margin-right: 8px;">
          <h3 style="margin-top: 20px;font-weight: 500;">bảng thống kê sách</h3>
          <canvas id="sachMuonChart" width="400" height="200"></canvas>
        </div>
        
        <div class="col box_chart" style="margin-left: 8px;">
          <h3 style="margin-top: 20px;font-weight: 500;">bảng thống kê thu chi</h3>
          <canvas id="thuChiChart" width="400" height="200"></canvas>
        </div>
      </div>
      </div>
    </div>

    <p class="copyright">
      &copy; 2024 - <span>Nhóm 2</span> All Rights Reserved.
    </p>
  </main>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('sachMuonChart').getContext('2d');
    const soLuongMuonData = <?php echo $soLuongMuonJson; ?>;
    const soLuongTraData = <?php echo $soLuongTraJson; ?>;
    const data = [{id: 1}]
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["Tháng 1","Tháng 2","Tháng 3","Tháng 4","Tháng 5","Tháng 6","Tháng 7","Tháng 8","Tháng 9","Tháng 10","Tháng 11","Tháng 12"],
            datasets: [
                {
                    label: 'Sách mượn',
                    data: soLuongMuonData,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Sách trả',
                    data: soLuongTraData,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('thuChiChart').getContext('2d');
    const tongThuThangData = <?php echo $tongThuThangJson; ?>;
    const tongChiThangData = <?php echo $tongChiThangJson; ?>;
    const data = [{id: 1}]
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ["Tháng 1","Tháng 2","Tháng 3","Tháng 4","Tháng 5","Tháng 6","Tháng 7","Tháng 8","Tháng 9","Tháng 10","Tháng 11","Tháng 12"],
            datasets: [
                {
                    label: 'Tổng tiền thu',
                    data: tongThuThangData,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Tổng tiền chi',
                    data: tongChiThangData,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>

</body>
</html>