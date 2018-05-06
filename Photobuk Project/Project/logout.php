<?php
// logout.php : called when user clicks the logout link
// Harini Sridharan
?>
<?php $this_page = "logout.php"; ?>

<!DOCTYPE html>

<?php  session_start(); ?> 

<?php

if(!isset($_SESSION['email']) || empty($_SESSION['email']))   // Checking whether the session is already there or not if 
                              // true then header redirect it to the home page directly 
{
    header("Location:login.php"); 
 }

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);



 ?>
    <head>
        <title>FotoBuk</title>
        <link href="favicon.ico" rel="icon" type="image/x-icon" />
        <!-- CSS-->
        <?php include "stylesheets.php" ?>
        <!-- Compressed CSS -->

        <?php include "scripts.php" ?>

    </head>
    <body>
        <!-- common header -->
        <?php include "header.php" ?>
        <div class="clear"></div>
        <section class="content">
            
            <?php

                // destroys the session and sets loggedout cookie to 1 which will logout users in other tabs too
                  session_start();

                  echo "Logout Successfully ";
                  setcookie('loggedout',1);
                  session_unset();
                  session_destroy();
                  session_write_close();
                  header("Location: login.php");
            ?>
            <div id="loader"></div>
        </section>
        

        <!-- common footer -->
        <?php include "footer.php" ?>
        <script>
            $(document).foundation();
            function readCookie(name) {
                var nameEQ = escape(name) + "=";
                var ca = document.cookie.split(';');
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                    if (c.indexOf(nameEQ) === 0) return unescape(c.substring(nameEQ.length, c.length));
                }
                return null;
            }
            window.setInterval(function() {
                if(readCookie('loggedout')==1) {
                    window.location.assign('logout.php')
                    //Or whatever else you want!
                }
            },5000)
        </script>
    </body>
</html>