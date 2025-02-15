<?php
// session_start();

if (!isset($_SESSION['userName']) || strtolower($_SESSION['role']) != ('admin' || 'thuthu') || strtolower($_SESSION['status']) != 'hoatdong') {
  include('logout.php');
  exit();
}

$name = $_SESSION['userName'];
$role = $_SESSION['role'];

// Lấy URL hiện tại
$current_page = basename($_SERVER['PHP_SELF']);

$role = strtolower($_SESSION['role'] ?? '');

if ($role === 'thuthu' && $current_page === 'account.php') {
  header('Location: dashboard.php?status=error&message=Không có quyền truy cập.');
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link rel="stylesheet" href="css/styles.css" media="all" />
    <style>
      .active {
        color: #fff !important;
        background-color:#3651d4 !important;
        border-radius: 8px;
      }
    </style>
  </head>
  <body>
    <nav style="height: auto !important">
      <div class="sidebar-top d_f_ju_center">
        <span class="shrink-btn">
          <i class="bx bx-chevron-left"></i>
        </span>
        <img src="./assets/img/logo.png" class="logo" alt="" />
      </div>
      <div class="sidebar-links">
        <ul>
          <li class="tooltip-element" data-tooltip="0">
            <a 
              href="dashboard.php" 
              class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>" 
              data-active="0">
              <div class="icon">
                <i class="bx bx-tachometer"></i>
                <i class="bx bxs-tachometer"></i>
              </div>
              <span class="link hide">Dashboard</span>
            </a>
          </li>

          <?php if (strtolower($_SESSION['role']) === 'admin') : ?>
            <li class="tooltip-element" data-tooltip="1">
              <a 
                href="account.php" 
                class="<?php echo $current_page == 'account.php' ? 'active' : ''; ?>" 
                data-active="1">
                <div class="icon">
                  <i class='bx bx-user'></i>
                  <i class='bx bxs-user'></i>
                </div>
                <span class="link hide">Tài Khoản</span>
              </a>
            </li>
          <?php endif; ?>

        </ul>
      </div>

      <div class="sidebar-footer" style="margin-top: auto">
        <a href="#" class="account tooltip-element" data-tooltip="0">
          <i class="bx bx-user"></i>
        </a>
        <div class="admin-user tooltip-element" data-tooltip="1">
          <div class="admin-profile hide">
            <img src="./assets/img/avatar.png" alt="" />
            <div class="admin-info">
              <h3><?php echo $name; ?></h3>
              <h5><?php echo $role; ?></h5>
            </div>
          </div>
          <a href="logout.php" class="log-out">
            <i class="bx bx-log-out"></i>
          </a>
        </div>
      </div>
    </nav>

    <script src="js/menu.js" defer async></script>
  </body>
</html>
