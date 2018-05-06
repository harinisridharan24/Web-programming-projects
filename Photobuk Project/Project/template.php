<!DOCTYPE html>
<?php
// template.php : common template on how all pages need to be displayed
// Harini Sridharan
?>
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

<html>
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
            <!-- PAGE CONTENT HERE determined by $this_page value -->
            <!-- 'content/home.php' have the content-->
            <?php include "content/$this_page" ?>
            <div id="loader"></div>
        </section>
        <div id="delete-comment-modal" class="reveal small" data-reveal>
            <h2 id="modalTitle">Are you sure?</h2>
            <input id="delete-comment-id" type="hidden" value="" />
            <p class="lead">You will not be able to undo this action</p>
            <p>
                <button id="delete-comment" class="alert button">Delete</button>
                <button  id="cancel-comment" class="secondary button cancel">Cancel</button> 
            </p>
              <button class="close-button" data-close aria-label="Close modal" type="button">
                <span aria-hidden="true">&times;</span>
              </button>
        </div>

        <div id="delete-photo-modal" class="reveal small" data-reveal>
            <h2 id="modalTitle">Are you sure?</h2>
            <input id="delete-photo-id" type="hidden" value="" />
            <p class="lead">Deleting the photo will also remove all the comments associated with it. Are you sure you want to continue</p>
            <p>
                <button id="delete-photo" class="alert button">Delete</button>
                <button  id="cancel-photo" class="secondary button cancel">Cancel</button> 
            </p>
              <button class="close-button" data-close aria-label="Close modal" type="button">
                <span aria-hidden="true">&times;</span>
              </button>
        </div>

        <div id="delete-account-modal" class="reveal small" data-reveal>
            <h2 id="modalTitle">Are you sure?</h2>
            <input id="delete-photo-id" type="hidden" value="" />
            <p class="lead">We are sorry you wish to delete your account. By deleting your account, all your beautiful photos you had uploaded will be deleted and the jokes you shared in the comments section. The comments you posted on other user's photo will still be visible. Are you sure you want to delete your account ?</p>
            <p>
                <button id="delete-account" class="alert button">Delete</button>
                <button  id="cancel-delete" class="secondary button cancel">Cancel</button> 
            </p>
              <button class="close-button" data-close aria-label="Close modal" type="button">
                <span aria-hidden="true">&times;</span>
              </button>
        </div>

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