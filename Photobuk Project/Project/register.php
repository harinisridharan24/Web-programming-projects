<?php
// register.php : The page is displayed when the user clciks the register link
// Registration page for the site
// Harini Sridharan
?>
<!DOCTYPE html>
<?php $this_page = "register.php"; ?>

<?php  session_start(); ?> 

<?php

if(isset($_SESSION['email']) and !empty($_SESSION['email']))   // Checking whether the session is already there or not if // true then header redirect it to the home page directly 
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
            <!-- PAGE CONTENT HERE determined by $this_page value -->
            <!-- 'content/home.php' have the content-->
            <?php

              ini_set('display_errors', 1);
              ini_set('display_startup_errors', 1);
              error_reporting(E_ALL);
              require_once("connect.php");
              require_once("helpers.php");
              $dbh = ConnectDB();
            ?>

            <div class="register-login">
            <?php
              // validates all field entered by user
              $fnameError = $lnameError = $emailError = $passwordError = $confirmPasswordError = "";
              $fname = $lname = $email = $password = $confirmPassword = ""; 
              $registrationsuccess = false;

              if(isset($_POST["first_name"]) && isset($_POST["last_name"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm_password"])){


              $fname = $_POST["first_name"];
              $lname = $_POST["last_name"];
              $email = strtolower($_POST["email"]);
              $password = $_POST["password"];
              $confirmPassword = $_POST["confirm_password"];
              
              if (empty($fname)) {
                $fnameError = "Name is required";
              }
              else if(!preg_match("/^[a-zA-Z ]*$/",$fname)) {
                $fnameError = "Name can contain only letters";
              }

              else if(validateLength($fname, 15)){
                $fnameError = 'Name cannot exceed 15 characters';
              }

              else {
                $fnameError = "";
              }

              if (empty($lname)) {
                $lnameError = "Name is required";
              } 
              else if(!preg_match("/^[a-zA-Z ]*$/",$lname)) {
                $lnameError = "Name can contain only letters";
              }
              
              else if(validateLength($lname, 15)){
                $lnameError = 'Name cannot exceed 15 characters';
              }
              else {
                $lnameError = "";
              }

              if (empty($email)) {
                $emailError = "Email is required";
              } 
              else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $emailError = "Invalid email format";
              }
              else if(validateLength($email, 40)){
                $emailError = 'Email cannot exceed 40 characters';
              }
              else {
                  $emailError =  "";
              }

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

              // if no errors the user is added to the database
              if (empty($fnameError) && empty($lnameError) && empty($emailError)  && empty($passwordError)  && empty($confirmPasswordError)) {
                try {
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $result = checkIfUserExists($email);
                if(!$result){
                $password = sha1($password);


                $stmt = $dbh->prepare("INSERT INTO users(fname, lname, username, password) VALUES (:fname, :lname, :email, :password)");

                $registrationsuccess = $stmt->execute(array(
                    "fname" => $fname,
                    "lname" => $lname,
                    "email" => $email,
                    "password" => $password
                ));

                  // on succesfull addition the confirmation key is generated and inserted it to confirm database

                  if($registrationsuccess) {
                    $userId =$dbh->lastInsertId();
                  
                    $username = $fname ." ". $lname;

                    $confirmationkey = $email . $fname . $lname;
                    $confirmationkey = sha1($confirmationkey);

                    $stmt = $dbh->prepare("INSERT INTO confirm(userid, confirmationkey, useremail) VALUES (:userId, :key, :email)");       
                    $confirmEmail = $stmt->execute(array(
                        "userId" => $userId,
                        "key" => $confirmationkey,
                        "email" => $email
                    ));
                    // a confirmation email is sent to the user's email address
                    if($confirmEmail){
                      $sender = 'Foto Buk<no-reply@fotobuk.com>';
                      // Subject of confirmation email.
                      $subject = 'Thanks for signing up. Please confirm your account';

                      // Who should the confirmation email be from?
                      

                      $msg = "Hi ". $username . ",\n\nThanks for signing up to Foto Buk. Please click /copy and paste the following link in your browser to confirm your account.\n\n http://elvis.rowan.edu/~sridharah9/awp/Project/confirm_user.php?email=".$email."&key=".$confirmationkey;
                      if(@mail( $email, $subject, $msg, 'From: ' . $sender )){
                        header('Location: registration_success.php');
                      }
                      else {
                        echo "There was problem sending your email. Please try signing up again";
                      }
                    }
                  }
                  
                }

                else {
                  $emailError = 'Email id already exists';
                }

                } catch(PDOException $e) {
                die ('PDO error fetching grade: ' . $e->getMessage() );
                }
              }
            }




            ?>
              <!-- registration form -->
              <h4>Register</h4>
              <form action="register.php" method="post">
                <table>
                  <tbody>
                    <tr>
                      <td class="form-label"><span class="required">*</span>First Name:</td>
                      <td class="form-input"><input type="text" name="first_name" size="50" maxlength="15" required value="<?php echo $fname;?>"/>
                      <div class="error"><?php echo $fnameError;?></div></td>
                    </tr>

                    <tr>
                      <td class="form-label"><span class="required">*</span>Last Name:</td>
                      <td class="form-input">
                      <input type="text" name="last_name" maxlength="15" size="50" required value="<?php echo $lname;?>" />
                      <div class="error"><?php echo $lnameError;?></div></td>
                    </tr>

                    <tr>
                      <td class="form-label"><span class="required">*</span>Email Address:</td>
                      <td class="form-input">
                        <input type="email" name="email" maxlength="40" size="50"  required value="<?php echo $email;?>" />
                        <div class="error"><?php echo $emailError;?></div></td>
                    </tr>

                    <tr>
                      <td class="form-label"><span class="required">*</span>Password:</td>
                      <td class="form-input"> 
                        <input type="password" name="password" size="50"  maxlength="15" required/>
                        <div class="error"><?php echo $passwordError;?></div></td>
                    </tr>

                    <tr>
                      <td class="form-label"><span class="required">*</span>Confirm Password:</td>
                      <td class="form-input">
                        <input type="password" name="confirm_password" size="50"  maxlength="15" required />
                        <div class="error"><?php echo $confirmPasswordError;?></div></td>
                    </tr>
                  </tbody>
                </table>
                <input type="submit" value="Register" class="submit-button" />
              </form>
              <label>Already have an account?<a href="login.php">Login</a></label>
              </div>



          </div>

        <!-- common footer -->
        <?php include "footer.php" ?>

    </body>
</html>