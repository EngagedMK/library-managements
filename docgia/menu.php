<?php
    // session_start();

    if (!isset($_SESSION['userName']) || strtolower($_SESSION['role']) != 'docgia' || strtolower($_SESSION['status']) != 'hoatdong') {
        include('../logout.php');
        exit();
      }

      $name = $_SESSION['userName'];

      $current_page = basename($_SERVER['PHP_SELF']);

    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- <link rel="stylesheet" href="../css/style.css" /> -->
    <!-- <link href='https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css' rel='stylesheet'> -->
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.2/css/all.css' rel='stylesheet'>
    <script src='https://code.jquery.com/jquery-3.4.1.min.js' ></script>
    <script src='https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js' ></script>
    <!-- <script src='https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.min.js' ></script> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<nav class="navbar navbar-expand-custom navbar-mainbg" style="padding: 0 24px; z-index: 2">
    <!-- <div> -->
        <a class="navbar-brand navbar-logo" href="docgia/index.php" style="padding: 0;"> <img src="../assets/img/logo.png" width="50px" alt="" /></a>
        <!-- <button class="navbar-toggler" type="button" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fas fa-bars text-white"></i>
        </button> -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav">
                <div class="hori-selector"><div class="left"></div><div class="right"></div></div>
                <li class="nav-item <?php echo $current_page == 'docgia/index.php' ? 'active' : ''; ?>">
                    <a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt"></i>Trang chủ</a>
                </li>
                <li class="nav-item <?php echo $current_page == '/docgia/book.php' ? 'active' : ''; ?>">
                    <a class="nav-link" href="book.php"><i class="far fa-address-book"></i>Sách</a>
                </li>
                <li class="nav-item <?php echo $current_page == '/docgia/contact.php' ? 'active' : ''; ?>">
                    <a class="nav-link" href="contact.php"><i class="far fa-clone"></i>liên hệ</a>
                </li>
            </ul>
        </div>

        
        <div class="dropdown pr-2">
            <!-- <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"> -->
            <div class="admin-user tooltip-element" data-tooltip="1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="admin-profile hide">
                    <img src="../assets/img/avatar.png" alt="" />
                    <div class="admin-info">
                        <h3 style="color: #fff"><?php echo $name; ?></h3>
                    </div>
                </div>
                </div>
            <!-- </button> -->
            <ul class="dropdown-menu dropdown-menu-lg-end">
                <li><a class="dropdown-item" href="../logout.php" class="log-out" style=" display: flex;align-items: center;"> <i class="bx bx-log-out" style="display: flex;align-items: center; justify-content: center;width:30px;height:30px;border-radius:50%;background-color:#D8E9FF"></i> Menu item</a></li>
                <!-- <li><a class="dropdown-item" href="#">Menu item</a></li>
                <li><a class="dropdown-item" href="#">Menu item</a></li> -->
            </ul>
        </div>
        <!-- </div> -->
    </nav>
</body>
</html>

<script>
    // ---------Responsive-navbar-active-animation-----------
function test(){
	var tabsNewAnim = $('#navbarSupportedContent');
	var selectorNewAnim = $('#navbarSupportedContent').find('li').length;
	var activeItemNewAnim = tabsNewAnim.find('.active');
	var activeWidthNewAnimHeight = activeItemNewAnim.innerHeight();
	var activeWidthNewAnimWidth = activeItemNewAnim.innerWidth();
	var itemPosNewAnimTop = activeItemNewAnim.position();
	var itemPosNewAnimLeft = activeItemNewAnim.position();
	$(".hori-selector").css({
		"top":itemPosNewAnimTop.top + "px", 
		"left":itemPosNewAnimLeft.left + "px",
		"height": activeWidthNewAnimHeight + "px",
		"width": activeWidthNewAnimWidth + "px"
	});
	$("#navbarSupportedContent").on("click","li",function(e){
		$('#navbarSupportedContent ul li').removeClass("active");
		$(this).addClass('active');
		var activeWidthNewAnimHeight = $(this).innerHeight();
		var activeWidthNewAnimWidth = $(this).innerWidth();
		var itemPosNewAnimTop = $(this).position();
		var itemPosNewAnimLeft = $(this).position();
		$(".hori-selector").css({
			"top":itemPosNewAnimTop.top + "px", 
			"left":itemPosNewAnimLeft.left + "px",
			"height": activeWidthNewAnimHeight + "px",
			"width": activeWidthNewAnimWidth + "px"
		});
	});
}
$(document).ready(function(){
	setTimeout(function(){ test(); });
});
$(window).on('resize', function(){
	setTimeout(function(){ test(); }, 500);
});
$(".navbar-toggler").click(function(){
	$(".navbar-collapse").slideToggle(300);
	setTimeout(function(){ test(); });
});



// --------------add active class-on another-page move----------
jQuery(document).ready(function($){
	// Get current path and find target link
	var path = window.location.pathname.split("/").pop();

	// Account for home page with empty path
	if ( path == '' ) {
		path = 'index.html';
	}

	var target = $('#navbarSupportedContent ul li a[href="'+path+'"]');
	// Add active class to target link
	target.parent().addClass('active');
});




// Add active class on another page linked
// ==========================================
// $(window).on('load',function () {
//     var current = location.pathname;
//     console.log(current);
//     $('#navbarSupportedContent ul li a').each(function(){
//         var $this = $(this);
//         // if the current path is like this link, make it active
//         if($this.attr('href').indexOf(current) !== -1){
//             $this.parent().addClass('active');
//             $this.parents('.menu-submenu').addClass('show-dropdown');
//             $this.parents('.menu-submenu').parent().addClass('active');
//         }else{
//             $this.parent().removeClass('active');
//         }
//     })
// });
</script>

<style>
    @import url("https://fonts.googleapis.com/css?family=Roboto");

body {
  font-family: "Roboto", sans-serif;
}
* {
  margin: 0;
  padding: 0;
}
i {
  margin-right: 10px;
}
/*----------bootstrap-navbar-css------------*/
.navbar-logo {
  padding: 15px;
  color: #fff;
}
.navbar-mainbg {
  background-color: #5161ce;
  padding: 0px;
}
#navbarSupportedContent {
  overflow: hidden;
  position: relative;
}
#navbarSupportedContent ul {
  padding: 0px;
  margin: 0px;
}
#navbarSupportedContent ul li a i {
  margin-right: 10px;
}
#navbarSupportedContent li {
  list-style-type: none;
  /* float: left; */
}
#navbarSupportedContent ul li a {
  color: rgba(255, 255, 255, 0.5);
  text-decoration: none;
  font-size: 15px;
  display: block;
  padding: 20px 20px;
  transition-duration: 0.6s;
  transition-timing-function: cubic-bezier(0.68, -0.55, 0.265, 1.55);
  position: relative;
}
#navbarSupportedContent > ul > li.active > a {
  color: #5161ce;
  background-color: transparent;
  transition: all 0.7s;
}
#navbarSupportedContent a:not(:only-child):after {
  content: "\f105";
  position: absolute;
  right: 20px;
  top: 10px;
  font-size: 14px;
  font-family: "Font Awesome 5 Free";
  display: inline-block;
  padding-right: 3px;
  vertical-align: middle;
  font-weight: 900;
  transition: 0.5s;
}
#navbarSupportedContent .active > a:not(:only-child):after {
  transform: rotate(90deg);
}
.hori-selector {
  display: inline-block;
  position: absolute;
  height: 100%;
  top: 0px;
  left: 0px;
  transition-duration: 0.6s;
  transition-timing-function: cubic-bezier(0.68, -0.55, 0.265, 1.55);
  background-color: #fff;
  border-top-left-radius: 15px;
  border-top-right-radius: 15px;
  margin-top: 10px;
}
.hori-selector .right,
.hori-selector .left {
  position: absolute;
  width: 25px;
  height: 25px;
  background-color: #fff;
  bottom: 10px;
}
.hori-selector .right {
  right: -25px;
}
.hori-selector .left {
  left: -25px;
}
.hori-selector .right:before,
.hori-selector .left:before {
  content: "";
  position: absolute;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background-color: #5161ce;
}
.hori-selector .right:before {
  bottom: 0;
  right: -25px;
}
.hori-selector .left:before {
  bottom: 0;
  left: -25px;
}

@media (min-width: 592px) {
  .navbar-expand-custom {
    -ms-flex-flow: row nowrap;
    flex-flow: row nowrap;
    -ms-flex-pack: start;
    justify-content: space-around;
  }
  .navbar-expand-custom .navbar-nav {
    -ms-flex-direction: row;
    flex-direction: row;
  }
  .navbar-expand-custom .navbar-toggler {
    display: none;
  }
  .navbar-expand-custom .navbar-collapse {
    display: -ms-flexbox !important;
    display: flex !important;
    -ms-flex-preferred-size: auto;
    flex-basis: auto;
    justify-content: center;
  }
}

@media (max-width: 591px) {
  #navbarSupportedContent ul li a {
    padding: 12px 30px;
  }
  .hori-selector {
    margin-top: 0px;
    margin-left: 10px;
    border-radius: 0;
    border-top-left-radius: 25px;
    border-bottom-left-radius: 25px;
  }
  .hori-selector .left,
  .hori-selector .right {
    right: 10px;
  }
  .hori-selector .left {
    top: -25px;
    left: auto;
  }
  .hori-selector .right {
    bottom: -25px;
  }
  .hori-selector .left:before {
    left: -25px;
    top: -25px;
  }
  .hori-selector .right:before {
    bottom: -25px;
    left: -25px;
  }
}

.admin-user {
  display: flex;
  align-items: center;
}

.admin-profile {
  white-space: nowrap;
  max-width: 100%;
  transition: opacity 0.3s 0.2s, max-width 0.7s 0s ease-in-out;
  display: flex;
  align-items: center;
  flex: 1;
  overflow: hidden;
}

.admin-user img {
  width: 2.5rem;
  border-radius: 50%;
  margin: 0 0.4rem;
}

.admin-info {
  padding-left: 0.3rem;
}

.admin-info h3 {
  font-weight: 500;
  font-size: 1rem;
  line-height: 1;
}

.admin-info h5 {
  font-weight: 400;
  font-size: 0.75rem;
  color: var(--text-color);
  margin-top: 0.3rem;
  line-height: 1;
}

.log-out {
  display: flex;
  height: 40px;
  min-width: 2.4rem;
  background-color: var(--main-color-dark);
  color: var(--text-color);
  align-items: center;
  justify-content: center;
  font-size: 1.15rem;
  border-radius: 10px;
  margin: 0 0.65rem;
  transition: color 0.3s;
}

.log-out:hover {
  color: #fff;
}

</style>