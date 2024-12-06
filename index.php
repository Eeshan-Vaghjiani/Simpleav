<?php
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Simpleav</title>
  <link rel="shortcut icon" href="img/logo2.jpg" type="image/x-icon">

  <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

<!--  css style sheet-->
  <link rel="stylesheet" href="css/style.css">
<!-- box icons links <-->
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<!-- remix icons link <-->
  <link
    href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
    rel="stylesheet"
  />
<!--  google fonts link-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Forum&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
</head>
<body>
<!--header-->
<header>
  <a href="/" class = "logo">
    <img src="img/logo2.jpg" >
  </a>

  <ul class = "navlinks">
    <a href="#home">Home</a>
    <a href="#shop">Subscription</a>
    <a href="#about">About Us </a>
    <a href="#contact">Contacts</a>


  </ul>
  <div class = "nav-side">
    <a href="User interface\login.php" class = "n-btn"> Sign In</a>
    <div class = "bx bx-menu" id = "menu-icon"> </div>

  </div>

</header>
<!--home-->
<section class = "home" id = "home">
  <div class = "home-text" data-aos="fade-up"  data-aos-duration = "1400">
    <h4> </h4>
    <h1>Simpleav </h1>
    <p>Experience a new level of operational excellence that keeps your workforce engaged and your business running smoothly!
      </p>
  </div>
  <div class = "home-img"  data-aos="zoom-in"  data-aos-duration = "1400">
    <img src="" >

  </div>
  <div class="social">

    <a href="/" data-aos="fade-in"  data-aos-duration = "1400" data-aos-delay ="200"><i class='bx bxl-facebook'></i></a>
    <a href="/" data-aos="fade-in"  data-aos-duration = "1400" data-aos-delay ="300"><i class='bx bxl-instagram'></i></a>
    <a href="/" data-aos="fade-in"  data-aos-duration = "1400" data-aos-delay ="400"><i class='bx bxl-twitter'></i></a>
    <a href="/" data-aos="fade-in"  data-aos-duration = "1400" data-aos-delay ="500"><i class='bx bxl-reddit'></i></a>

  </div>
  <div class  ="arrow-box" data-aos="zoom-in-left"  data-aos-duration = "1400" data-aos-delay ="200">
    <a href="#about"><i class='bx bxs-chevron-down'></i></a>
  </div>
</section>
<!--about-->
<section class = "about" id = "about">
  <div class = "about-img" data-aos="zoom-in"  data-aos-duration = "1400" >
    <img src="" >

  </div>
  <div class  = "about-text" data-aos="fade-up"  data-aos-duration = "1400" >
    <h2>OUR<br>  FEATURES</h2>
    <p>Try out our HR Management Approval</p>
    <a href="/" class ="btn">View More</a>
    <br> <br>
    <p> Faster Leave Approval For Employees</p>
    <a href="/" class = "btn">View More</a>

  </div>

</section>
<!--shop-->
<section class = "shop" id="shop">
  <div class = "head-text" data-aos="fade-in"  data-aos-duration = "1400" data-aos-delay ="100">
    <div class = "head-left">


      </div>


    </div>
    <div class  = "head-right">

    </div>
<div class = "shop-content" data-aos="fade-in"  data-aos-duration = "1400" data-aos-delay ="200">
  <div class="box">
    <h2 >Check Out Our Payment Plan</h2> <br>
    <div class="box-img " >
      <img src="image/img.png" >

    </div>
    <div class = "feature">
      <a href="pay1.php"><i class='bx bx-play-circle'></i></a>

    </div>
  </div>


</div>



</section>

<!--contact-->
<section class = "contact" id = "contact">
  <div class="contact-content" data-aos="fade-in"  data-aos-duration = "1400" data-aos-delay ="400">
    <h2>Be A Part Of Us</h2>
    <hr>
    <p>Strathmore University , Ole-Sangale Road</p>
    <div class="s-links">
      <a href="https://www.facebook.com/login.php/" class="s-btnn">Facebook</a>
      <a href="https://www.instagram.com/accounts/login/?hl=en" class="s-btnn">Instagram</a>
      <a href="https://www.reddit.com/" class = "s-btnn">Reddit</a>

    </div>


  </div>


</section>

<!--footer-->
<div class ="footer">
  <div class="copyright">
    <a href="/" target="_blank">&copy; Simpleav 2024 | All Rights Reserved</a>

  </div>
  <div class="scroll-btnn">
    <a href=""><i class = "ri-arrow-up-line"></i></a>

  </div>

</div>
<!--js link -->
<script src = "js/app.js"></script>

<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
  AOS.init({
  });
</script>

</body>
</html>
