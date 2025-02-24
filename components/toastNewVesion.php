<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <title>Document</title>
</head>
<body>

    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050">
        <div id="toastMessage" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <span id="toastText"></span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const params = new URLSearchParams(window.location.search);
            if (params.has("status") && params.has("message")) {
                const status = params.get("status");
                const message = params.get("message");

                const toastEl = document.getElementById("toastMessage");
                const toastText = document.getElementById("toastText");

                // Thay đổi màu nền dựa trên trạng thái
                toastEl.classList.add(status === "success" ? "bg-success" : "bg-danger");

                toastText.textContent = message;

                const toast = new bootstrap.Toast(toastEl);
                toast.show();

                // Xóa trạng thái và thông báo khỏi URL ngay sau khi hiển thị toast

                    const url = new URL(window.location.href);
                    url.searchParams.delete('status');
                    url.searchParams.delete('message');
                    window.history.replaceState(null, '', url.toString());
            }
        });
    </script>

</body>
</html>
