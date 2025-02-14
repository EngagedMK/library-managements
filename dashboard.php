<?php
    session_start();

    if (!isset($_SESSION['userName']) && !isset($_SESSION['role'])) {
        header("Location: index.php"); 
        exit();
    }

    if ($_SESSION['role'] != 'admin') {
		header("Location: index.php"); 
        exit();
	}

    echo"dashboard đây";
    
?>
<a href="logout.php">Logout</a>