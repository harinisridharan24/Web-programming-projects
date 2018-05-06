<?php
// forgot_password.php : The page is displayed when the user clicks the forgit password link on the login page
// Harini Sridharan
?>
<!DOCTYPE html>
<?php $this_page = "login.php"; ?>

<?php  session_start(); ?> 

<?php

if(isset($_SESSION['email']) and !empty($_SESSION['email']))   // Checking whether the session is already there or not if 
                              // true then header redirect it to the home page directly 
{
    header("Location:index.php"); 
}


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
            <?php

              ini_set('display_errors', 1);
              ini_set('display_startup_errors', 1);
              error_reporting(E_ALL);
              require_once("connect.php");
              require_once("helpers.php");
              $dbh = ConnectDB();

            ?>


            <?php
              // validate email field
              $useremail = ""; 
              $emailError = "";
              if(isset($_POST["useremail"]) ){
                $useremail = strtolower($_POST["useremail"]);
                if (empty($useremail)) {
                  $emailError = "Email is required";
                } 
                else if(!filter_var($useremail, FILTER_VALIDATE_EMAIL)){
                  $emailError = "Invalid email format";
                }
                else {
                    $emailError =  "";
                }
                if (empty($emailError)) {
                  try {
                    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    // check if user with entered email exists as a activated user
                    $user = checkUser($useremail);

                    $getUserCount = count($user);
                    // generate a resetpassword key to identify the user when resetting the password
                    if($getUserCount == 1){
                        $userId = $user[0]->user_id;
                        $username = $user[0]->fname ." ". $user[0]->lname;
                        $resetpasswordkey = $useremail . $userId;
                        $resetpasswordkey = sha1($resetpasswordkey);
                        $stmt = $dbh->prepare("INSERT INTO resetpassword(user_id, resetpasswordkey, useremail) VALUES (:userId, :key, :email)");       
                        $resetPasswordEmail = $stmt->execute(array(
                            "userId" => $userId,
                            "key" => $resetpasswordkey,
                            "email" => $useremail
                        ));
                        // send an email to the email address with reset password link
                        if($resetPasswordEmail){
                          $sender = 'Foto Buk<no-reply@fotobuk.com>';
                          // Subject of confirmation email.
                          $subject = 'Reset your password';


                        
                          $msg = "Hi ". $username . ",\n\n Please click /copy and paste the following link in your browser to reset your password.\n\n http://elvis.rowan.edu/~sridharah9/awp/Project/reset_password.php?email=".$useremail."&key=".$resetpasswordkey;
                          if(@mail( $useremail, $subject, $msg, 'From: ' . $sender )){
                            header('Location: forgot_password_success.php');
                          }
                          else {
                            $emailError = "There was problem sending your email. Please try again after some time.";
                          }
                        }
                        exit();
                    }
                    else {
                      $emailError = "Please check the email address you entered.";
                    }
                  }
                  catch(PDOException $e) {
                    die ('PDO error fetching grade: ' . $e->getMessage() );
                  }
                }

              }


            ?>

            <div class="register-login">

              <form method="POST" action="forgot_password.php">
                <h4>Forgot Password</h4>
                <input type="email" name="useremail" placeholder="Enter Email Address" required  value="<?php echo $useremail;?>"/>
                <div class="error"><?php echo $emailError;?></div>
                <input class="button" type="submit" value="Request reset password link" />
                <label>Remembered your password?<a  href="login.php">Login</a></label>
              </form>
            </div>
        </div>
        <!-- common footer -->
        <?php include "footer.php" ?>
    </body>
</html>



