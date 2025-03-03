<?php
    session_start();

    if (!isset($_SESSION['userName']) || strtolower($_SESSION['role']) != 'docgia' || strtolower($_SESSION['status']) != 'hoatdong') {
        include('logout.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include("menu.php"); ?>

    <div class="container text-center mt-4">
        <div class="row">
            <div class="col-7 bg">
                <div class="row d_f_c">
                    <button id="btnBorrow" class="col-5 d_f_c">Sách đang mượn</button>
                    <button id="btnReturn" class="col-5 d_f_c">Sách đã trả</button>
                </div>
            </div>
            <div class="col-5"></div>
        </div>
    </div>

    <!-- Nội dung phần "Sách đang mượn" -->
    <div id="borrowSection" class="content-section">
        <?php include("borrowBookManagement.php"); ?>
    </div>

    <!-- Nội dung phần "Sách đã trả" -->
    <div id="returnSection" class="content-section" style="display: none;">
        <?php include("returnBookManagement.php"); ?>
    </div>

    <?php include("footer.php"); ?>

    <script>
        // Hàm hiển thị phần nội dung tương ứng
        function showSection(section) {
            const borrowButton = document.getElementById('btnBorrow');
            const returnButton = document.getElementById('btnReturn');
            const borrowSection = document.getElementById('borrowSection');
            const returnSection = document.getElementById('returnSection');

            if (section === 'borrow') {
                // Hiển thị phần "Sách đang mượn"
                borrowSection.style.display = 'block';
                returnSection.style.display = 'none';

                // Thêm class active vào nút "Sách đang mượn"
                borrowButton.classList.add('active');
                returnButton.classList.remove('active');
            } else if (section === 'return') {
                // Hiển thị phần "Sách đã trả"
                returnSection.style.display = 'block';
                borrowSection.style.display = 'none';

                // Thêm class active vào nút "Sách đã trả"
                returnButton.classList.add('active');
                borrowButton.classList.remove('active');
            }

            // Lưu trạng thái hiện tại vào localStorage
            localStorage.setItem('activeSection', section);
        }

        // Lấy trạng thái từ localStorage và hiển thị phần nội dung tương ứng khi tải lại trang
        window.onload = function () {
            const activeSection = localStorage.getItem('activeSection') || 'borrow';
            showSection(activeSection);
        };

        // Gán sự kiện click cho các nút
        document.getElementById('btnBorrow').addEventListener('click', () => showSection('borrow'));
        document.getElementById('btnReturn').addEventListener('click', () => showSection('return'));
    </script>
</body>
</html>

<style>
    .bg {
        background-color: #C4CDE8;
        border-radius: 8px;
        width: 100%;
        height: 58px;
        display: flex;
        justify-content: space-around;
        align-items: center;
    }

    .d_f_c {
        height: 100%;
        width: 100%;
        margin-right: 10px;
        margin-left: 10px;
        border: none;
        background-color: transparent;
        cursor: pointer;
    }

    .active {
        width: 70%;
        height: 80%;
        background-color: #fff;
        border-radius: 8px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: 500;
    }

    .content-section {
        margin-top: 20px;
    }
</style>
