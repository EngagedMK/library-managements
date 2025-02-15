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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>
<body>
<div aria-live="polite" aria-atomic="true" >
        <div id="toast-container" style="position: fixed; top: 20px; right: 20px;z-index: 4">
        </div>
     </div>
</body>
</html>

  <?php if (isset($_GET['status']) && isset($_GET['message'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const status = "<?php echo $_GET['status']; ?>";
        const message = "<?php echo $_GET['message']; ?>";

        // Toast Template
        const toastTemplate = `
            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">
                <div class="toast-header ${status === 'success' ? 'bg-success text-white' : 'bg-danger text-white'}" style="padding: 12px; border-radius: 8px">
                    <strong>${message}</strong>
                    <button type="button" class="ml-2 close" data-dismiss="toast" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        `;

        // Thêm Toast vào Container
        const toastContainer = document.getElementById('toast-container');
        toastContainer.insertAdjacentHTML('beforeend', toastTemplate);

        // Khởi tạo và Hiển thị Toast
        const newToast = toastContainer.querySelector('.toast:last-child');
        $(newToast).toast('show');

        // Xóa trạng thái và thông báo khỏi URL sau khi hiển thị toast
        const url = new URL(window.location.href);
        url.searchParams.delete('status');
        url.searchParams.delete('message');
        window.history.replaceState(null, '', url.toString());
    });
</script>
<?php endif; ?>