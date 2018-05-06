<?php
// confirm_user.php : The page is displayed when the user clicks the confirmation link on his/her email.
// Harini Sridharan
?>



<!DOCTYPE html>

<?php $this_page = "confirm_user.php"; ?>
<?php  session_start(); ?> 

<?php


  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);

  require_once("connect.php");
    $dbh = ConnectDB();

    $action = array();
    $action['result'] = '';
    $action['text'] = '';



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
        <section class="content">
            <?php 

// check if a user is already logged in
if(empty($_SESSION['email'])){

    // checck if the url contains email and confirmation ket
    if(empty($_GET['email']) || empty($_GET['key'])){
        $action['result'] = 'error';
        $action['text'] = 'We are missing variables. Please double check your email.';          
    }
            
    if($action['result'] != 'error'){
    
    
        $email = $_GET['email'];
        $key =  $_GET['key'];    

        //check if the key is in the database

        $stmt = $dbh->prepare("SELECT confirm.userid, fname, lname, username FROM confirm,users WHERE useremail = :email and confirmationkey = :confirmationkey and confirm.userid = users.user_id");
        // There should only be one, but this means if we get
        // more than one match we can find out easily.
        $stmt->execute(array(':email' => $email, ':confirmationkey' => $key));
        $user = $stmt->fetchAll(PDO::FETCH_OBJ);
        if(count($user) == 1){
                    
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $dbh->prepare("UPDATE users SET active = 1 WHERE user_id=:userId");

            $update_users = $stmt->execute(array(
                "userId" => $user[0]->userid,
            ));

            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $dbh->prepare("DELETE FROM  confirm WHERE userid = :userId");

            $delete_confirm = $stmt->execute(array(
                "userId" => $user[0]->userid,
            ));

            // on user confirmation create a session for user            
            if($update_users){
                $_SESSION['email'] = $user[0]->username;
                $_SESSION['userId'] = $user[0]->userid;
                $_SESSION['username'] = $user[0]->fname ." ". $user[0]->lname;      
                $action['result'] = 'success';
                setcookie('loggedout', 0);
                $action['text'] = 'User has been confirmed. Thank You!';
            
            }else{
                $action['result'] = 'error';
                $action['text'] = 'The user could not be updated Reason: '.mysql_error();;
            
            }
        
        }else{
            $action['result'] = 'error';
            $action['text'] = 'Please verify the link.';
        
        }
    
    }
}
// display already logged in message
else {
   $action['result'] = 'error';
   $action['text'] = 'You are already logged in. If not, please <a href="logout.php">Logout</a> and retry'; 
}

?>

<?php
  $text = $action['text'];
  if($action['result'] == 'error'){
    echo "<div class='callout alert'>$text</div>";
  }

  else {
    echo "<div class='callout success'>$text</div>";
    echo "<div><a href='index.php'>Go to Home Page</a></div>";
  }
?>

        </section>

        <!-- common footer -->
        <?php include "footer.php" ?>
        <?php include "scripts.php" ?>
    </body>
</html>