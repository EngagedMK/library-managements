<?php 
    require('../config.php');
    
    // Thêm biến $searchTerm và giới hạn kết quả
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
    $sqlBook = "SELECT * FROM TaiLieu WHERE tenTaiLieu LIKE '%$searchTerm%' ORDER BY idTaiLieu DESC LIMIT 8";

    $result = mysqli_query($conn, $sqlBook);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="content">
        <h1 class="header_content">Danh sách sách</h1>
        <div class="sub_content">
            <span class="sub_content_c">"Sách mở ra chân trời tri thức,</span>
            <span class="sub_content_c">Chạm vào từng trang là chạm đến ước mơ."</span>
        </div>
        <div class="container text-center mt-5 d_f_c">
            <div class="row" style="width: 80%">
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($book = mysqli_fetch_assoc($result)): ?>
                        <div class="col-md-3 mb-4">
                            <a class="card h-100 hoverImages" style="box-shadow: rgba(100, 100, 111, 0.5) 0px 7px 29px 0px;" href="">
                                <!-- Hiển thị hình ảnh -->
                                <img src="../<?= htmlspecialchars($book['img_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($book['tenTaiLieu']) ?>" style="max-height: 300px">
                                
                                <!-- Nội dung sách -->
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($book['tenTaiLieu']) ?></h5>
                                    <p class="card-text text-truncate"><?= htmlspecialchars($book['tacGia']) ?></p>
                                    <div class="soluong">
                                        <span class="card-text text-truncate" style="margin-right: 4px">Kho:</span>
                                        <span class="card-text text-truncate"> <?= htmlspecialchars($book['soLuong']) ?></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Không tìm thấy sách nào.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="d_f_c" style="margin-top: 44px ">
            <a href="book_detail.php" class="btn" style=" background-color:#5162CE; color: #fff ">Xem chi tiết</a>

        </div>
    </div>
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

    .hoverImages {
        margin-top:20px;
        margin-bottom:20px;
        transition: margin 0.5s ease-out;
        -webkit-transition: margin 0.5s ease-out;
        -moz-transition: margin 0.5s ease-out;
        -o-transition: margin 0.5s ease-out;
    }

    .hoverImages:hover {
        cursor:pointer;
        margin-top: 5px;
    }
    
    a {
        text-decoration: none;
    }
</style>
