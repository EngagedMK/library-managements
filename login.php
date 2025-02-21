<?php 
session_start(); 
include "config.php";

if (isset($_POST['username']) && isset($_POST['password'])) {

	function validate($data){
       $data = trim($data);
	   $data = stripslashes($data);
	   $data = htmlspecialchars($data);
	   return $data;
	}

	$username = validate($_POST['username']);
	$pass = validate($_POST['password']);

	if (empty($username)) {
		header("Location: index.php?error=User Name là bắt buộc");
	    exit();
	}else if(empty($pass)){
        header("Location: index.php?error=Password là bắt buộc");
	    exit();
	}else{
		$sql = "SELECT * FROM TaiKhoan WHERE tenDangNhap='$username' AND matKhau='$pass'";

		$result = mysqli_query($conn, $sql);

		if (mysqli_num_rows($result) === 1) {
			$row = mysqli_fetch_assoc($result);
            if ($row['tenDangNhap'] === $username && $row['matKhau'] === $pass) {
            	$_SESSION['userName'] = $row['tenDangNhap'];
            	$_SESSION['name'] = $row['tenDangNhap'];
            	$_SESSION['id'] = $row['idTaiKhoan'];
            	$_SESSION['role'] = $row['vaiTro'];
            	$_SESSION['status'] = $row['trangThai'];

				if (strtolower($row['vaiTro']) === 'docgia'){
					header("Location: docgia/index.php");
					exit();
				} else {
					header("Location: dashboard.php");
					exit();
				}
            }else{
				header("Location: index.php?error=User name hoặc password không chính xác");
		        exit();
			}
		}else{
			header("Location: index.php?error=User name hoặc password không chính xác");
	        exit();
		}
	}
	
}else{
	header("Location: index.php");
	exit();
}