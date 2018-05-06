<?php
// reset_password.php : The page is displayed when user clicks the reset password link in the emal
// Harini Sridharan
?>
<!DOCTYPE html>
<?php $this_page = "login.php"; ?>

<?php  session_start(); ?> 

<?php


  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);

  require_once("connect.php");
    $dbh = ConnectDB();

    $action = array();
    $action['result'] = '';
    $action['text'] = '';

    $email = $key = "";

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
          $confirmPassword = $password =  ""; 
          $confirmPasswordError = $passwordError = "";
          // check is user is already logged in
          if(empty($_SESSION['email'])){
              // gets email and reset password key
             if(empty($_GET['email']) || empty($_GET['key'])){
                 $action['result'] = 'loggedin';
                 $action['text'] = 'We are missing variables. Please double check your email.';          
             }

            if($action['result'] != 'loggedin'){
            
                //cleanup the variables
                $email = $_GET['email'];
                $key =  $_GET['key'];  
                //check if the key is in the database
                $stmt = $dbh->prepare("SELECT users.user_id, fname, lname, username FROM resetpassword,users WHERE resetpasswordkey = :resetpasswordkey and resetpassword.user_id = users.user_id");
                // There should only be one, but this means if we get
                // more than one match we can find out easily.
                $stmt->execute(array(':resetpasswordkey' => $key));
                $user = $stmt->fetchAll(PDO::FETCH_OBJ);
                if(count($user) >= 1){
                  // validates new password 
                  if(!empty($_POST['password']) &&  !empty($_POST['confirm_password'])){
                                     $password = $_POST['password'];
                  $confirmPassword = $_POST['confirm_password'];
                    if (empty($password)) {
                      $passwordError = "Password is required"; 
                    } 
                    else if(strlen($password) < 3 || strlen($password)> 15) {
                      $passwordError = "Password must be between 3 and 15 characters";
                    }
                    else {
                        $passwordError = "";
                    }

                    if (empty($confirmPassword)) {
                        $confirmPasswordError = "Confirm Password is required";
                    } 
                    else if ($password != $confirmPassword){
                      $confirmPasswordError = "Password and confirm Password do not match";
                    }
                    else {
                        $confirmPasswordError = "";
                    }

                    // on no errors update the password in the database and remove the resept password key entry
                    if (empty($passwordError)  && empty($confirmPasswordError)) {

                      try{
                        $password = sha1($password);  
                        
                        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $stmt = $dbh->prepare("UPDATE users SET password = :password WHERE user_id=:userId");

                        $update_users = $stmt->execute(array(
                            "userId" => $user[0]->user_id,
                            "password" => $password
                        ));

                        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $stmt = $dbh->prepare("DELETE FROM  resetpassword WHERE user_id = :userId");

                        $delete_confirm = $stmt->execute(array(
                            "userId" => $user[0]->user_id,
                            
                        ));

                        
                        if($update_users){
                            $_SESSION['email'] = $user[0]->username;
                            $_SESSION['userId'] = $user[0]->user_id;
                            $_SESSION['username'] = $user[0]->fname ." ". $user[0]->lname;      
                            $action['result'] = 'success';
                            $action['text'] = 'User password has been changed';
                            setcookie('loggedout', 0);
                        
                        }
                        else{
                            $action['result'] = 'error';
                            $action['text'] = 'The user could not be updated Reason: '.mysql_error();;
                        
                        }
                      }
                      catch(PDOException $e) {
                        die ('PDO error fetching grade: ' . $e->getMessage() );
                      }
                  }
                  }
            }
            else {
              $action['result'] = 'loggedin';
              $action['text'] = 'The link is expired.';
            }        
          }
        }
        else {
          $action['result'] = 'loggedin';
          $action['text'] = 'You are already logged in. If not, please <a href="logout.php">Logout</a> and retry'; 
        }

        ?>
<?php

if($action['result'] != 'loggedin'){

          $text = $action['text'];
          if($action['result'] == 'error'){
            echo "<div class='callout alert'>$text</div>";
          }

          else if($action['result'] == 'success'){
            echo "<div class='callout success'>$text <a href='index.php'>Go to Home Page</a></div>";
          }
        // reset password html block
        if($action['result'] != 'success'){

          echo '<div class="register-login">';

            echo "<form method='POST' action='reset_password.php?email=$email&key=$key'>";
              echo "<h4>Reset Password</h4>";
              echo "<label>New Password:</label>";
              echo "<input type='password' name='password' size='50'  maxlength='15' required/>";
              echo "<div class='error'>$passwordError</div>";
              echo "<label>Confirm Password:</label>";
              echo "<input type='password' name='confirm_password' size='50'  maxlength='15' required />";
              echo "<div class='error'>$confirmPasswordError</div>";
              echo "<input class='button' type='submit' value='Update Password' />";
            echo "</form>";
          echo "</div>";
        }
        

}
else {
  $text = $action['text'];
  echo  "<div class='callout alert'>$text</div>";
}
?>
        </div>

        <!-- common footer -->
        <?php include "footer.php" ?>

    </body>
</html>



