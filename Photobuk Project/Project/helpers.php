<?php
// helpers.php : The page serves as helpers pge whcih runs different helper functions
// Harini Sridharan
?>
<?php

require_once("connect.php");

?>

<?php


/*
** check if user exists in the data base when user tries to register for a new account 
** $data - email address the user is trying to register with
*/
function checkIfUserExists($data) {
 try {
  $dbh = ConnectDB();
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $stmt = $dbh->prepare("SELECT user_id FROM users WHERE username = :email");
  // There should only be one, but this means if we get
  // more than one match we can find out easily.
  $stmt->execute(array(':email' => $data));
  $count = $stmt->fetchAll(PDO::FETCH_OBJ);

  $howmany = count($count);

  if($howmany>=1) {
    return true;
  }

  else {
    return false;
  }
 }
 catch(PDOException $e)
  {
      trigger_error ('PDO error in "ConnectDB()": ' . $e->getMessage() );
  }
  //$stmt->close();
  
  return true;
}


/*
** gets the user matching the email and password when user tries to login
** $username - email address the user is trying to login with
** $password - password the user is trying to login with
*/
function getUser($username, $password) {
  try {
  $password = sha1($password);
  $dbh = ConnectDB();
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  // get the user and return it to the login.php
  $stmt = $dbh->prepare("SELECT user_id,fname,lname  FROM users WHERE username = :username and password = :password and active = :active");
  $stmt->execute(array(':username' => $username, ':password' => $password, ':active' => 1));
  $user = $stmt->fetchAll(PDO::FETCH_OBJ);

  return $user;
 }
 catch(PDOException $e)
  {
      trigger_error ('PDO error in "ConnectDB()": ' . $e->getMessage() );
  }
  //$stmt->close();

}

/*
** gets the user matching the email for forgot password confirmation
** $username - email address the user is trying to login with
*/
function checkUser($username) {
  try {
  $dbh = ConnectDB();
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  // retreive only confirmed usr entries
  $stmt = $dbh->prepare("SELECT user_id, fname, lname  FROM users WHERE username = :username and active = :active");
  // There should only be one, but this means if we get
  // more than one match we can find out easily.
  $stmt->execute(array(':username' => $username, ':active' => 1));
  $user = $stmt->fetchAll(PDO::FETCH_OBJ);

  return $user;
 }
 catch(PDOException $e)
  {
      trigger_error ('PDO error in "ConnectDB()": ' . $e->getMessage() );
  }
  //$stmt->close();

}

/*
** trims the text the user enter and converts any special characters the user tries to enter
** $data - text entered by the user in the input field
*/
function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

/*
** validates whether text entered is below the length accepted
** $value - text entered by the user in the input field
** $length - maximum length allowed for the field
*/
function validateLength($value, $length){
  if(strlen($value) > $length){
    return true;
  }
  else {
    return false;
  }
}

/*
** retreives the hashtags entered for photo description which can be used as a search text when user searches for photos
** $string - text entered by the user in the photo description field
*/
function getHashtags($string) {  
    $hashtags= FALSE;  
    preg_match_all("/(#\w+)/u", $string, $matches);  
    if ($matches) {
        $hashtagsArray = array_count_values($matches[0]);
        $hashtags = array_keys($hashtagsArray);
    }
    return $hashtags;
}


/*
** calculates the time difference when user posted the photo to the current time and display it in the photo list
** $postedDateTime - date timephoto was uploaded
*/
function getTimeDifference($postedDateTime){
  date_default_timezone_set('America/New_York');
  $postedDateTime = date_format (new DateTime($postedDateTime), 'Y-m-d H:i:s');
  $currentTime = date('Y-m-d H:i:s', time());
  $t1 = strtotime ( $postedDateTime );
  $t2 = strtotime ( $currentTime );
  $diff = $t2 - $t1;
  $minutes = round(abs($diff) / 60, 0);

  if($minutes <=1){
    return  "Just Now";
  }
  else if($minutes > 2 && $minutes <=59){
    return $minutes . " minutes ago";
  }

  else if($minutes >= 60 && $minutes < 120){
    return "1 hour ago";
  }

  else if($minutes >= 121 && $minutes < 1440){
    $hours = round(abs($minutes) / 60,0);
    return  $hours. " hours ago";
  }
  else {
    return date_format (new DateTime($postedDateTime), 'M d, Y');
  }



}


?>