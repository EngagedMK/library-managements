<?php
session_start();

if (isset($_SESSION['userName'])) {
    header("Location: dashboard.php"); 
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>LOGIN</title>
  <link rel="stylesheet" type="text/css" href="css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" crossorigin="">
  <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>
  <div class="login">
    <img src="assets/img/login-bg.png" alt="image" class="login__bg" />
    <form action="login.php" method="post" class="login__form">
      <h1 class="login__title">Login</h1>

      <div class="login__inputs">
        <div class="login__box">
          <input
            type="type"
            placeholder="UserName"
            name="username"
            required
            class="login__input" />
          <i class="ri-mail-fill"></i>
        </div>

        <div class="login__box">
          <input
            type="password"
            name="password"
            placeholder="Password"
            required
            class="login__input" />
          <i class="ri-lock-2-fill"></i>
        </div>
      </div>

      <?php if (isset($_GET['error'])) { ?>
     		<p class="error"><?php echo $_GET['error']; ?></p>
     	<?php } ?>

      <button type="submit" class="login__button">Login</button>

    </form>
  </div>
</body>

</html>