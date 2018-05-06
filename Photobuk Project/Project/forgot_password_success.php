<?php
// forgot_password_success.php : The page is displayed when the user submits a succesful forgot password form
// Harini Sridharan
?>
<!DOCTYPE html>
<?php $this_page = "login.php"; ?>
<?php  session_start(); ?> 

<?php

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);

 ?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>FotoBuk</title>
         <link href="favicon.ico" rel="icon" type="image/x-icon" />
        <!-- CSS-->
        <?php include "stylesheets.php" ?>

    </head>
    <body>
        <!-- common header -->
        <header>
          <div class="top-bar">
            <div class="top-bar-left">
              <ul class="dropdown menu" data-dropdown-menu>
                <li><a href="index.php">FotoBuk</a></li>
              </ul>
            </div>
            <div class="top-bar-right">
            </div>
          </div>
        </header>
        <div class="clear"></div>
        <div class="content">
          
            <div class='callout success'>An email has been sent to your registered email address. Please click on the link to reset your password. </div>
            <div>
              <a href='login.php'>Back to Login.</a>
            </div>
        </div>

        <!-- common footer -->
        <?php include "footer.php" ?>
        <?php include "scripts.php" ?>
    </body>
</html>