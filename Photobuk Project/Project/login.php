<?php
// login.php : Login page 
// Harini Sridharan
?>

<!DOCTYPE html>
<html>
<?php $this_page = "login.php"; ?>

<?php  session_start(); ?> 

<?php

if(isset($_SESSION['email']) and !empty($_SESSION['email']))   // Checking whether the session is already there or not if 
                              // true then header redirect it to the home page directly 
{
    header("Location:index.php"); 
 }



 ?>

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

              // validates email and password 
              $email = $password =  ""; 
              $emailError = $passwordError = $loginError = "";
              if(isset($_POST["email"]) && isset($_POST["password"])){
                $email = strtolower($_POST["email"]);
                $password = $_POST["password"];

                if (empty($email)) {
                  $emailError = "Email is required";
                } 
                else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                  $emailError = "Invalid email format";
                }
                else {
                    $emailError =  "";
                }

                if (empty($password)) {
                  $passwordError = "Password is required";
                }
                else {
                    $passwordError = "";
                }


                // if no errors checks if the email and password match
                if (empty($emailError) && empty($passwordError)) {
                  try {
                    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $user = getUser($email, $password);

                    $getUserCount = count($user);
                    if($getUserCount == 1){
                        // creates a session when user is succesfully logged in and redirects to the home page
                        $_SESSION['email']=$email;
                        $_SESSION['userId']=$user[0]->user_id;
                        $_SESSION['username']=$user[0]->fname ." ". $user[0]->lname;
                        setCookie('loggedout',0);
                        header("Location: index.php");
                        exit();
                    }
                    else {
                      $loginError = "Email address and password do not match or You might have not confirmed your account yet";
                    }
                  }
                  catch(PDOException $e) {
                    die ('PDO error fetching grade: ' . $e->getMessage() );
                  }
                }

              }


            ?>

            <div class="register-login">

              <h4>Login</h4>
              <form action="login.php" method="post">
                <table>
                  <tbody>

                    <tr>
                      <td class="form-label"><span class="required">*</span>Email Address:</td>
                      <td class="form-input">
                        <input type="text" name="email" size="50" maxlength="40" required value="<?php echo $email;?>" />
                        <div class="error"><?php echo $emailError;?></div></td>
                    </tr>

                    <tr>
                      <td class="form-label"><span class="required">*</span>Password:</td>
                      <td class="form-input">
                        <input type="password" name="password" size="50" maxlength="15" required/>
                        <div class="error"><?php echo $passwordError;?></div></td>
                    </tr>
                  </tbody>
                </table>
                <div class="error"><?php echo $loginError;?></div>
                <input type="submit" value='Login' />
                <label>Don't have an account?</label><a href="register.php">Register</a>
                <div><a href="forgot_password.php">Forgot Password?</a></div>
              </form>
            </div>
        </div>

        <!-- common footer -->
        <?php include "footer.php" ?>

    </body>
</html>


