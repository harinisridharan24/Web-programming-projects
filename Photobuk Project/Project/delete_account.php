<?php
// delete_account.php : The page is called as an ajax request when user confirms account deletion
// All the user data is deleted and all files the user uploaded are removed from the database
// Harini Sridharan
?>

<?php  session_start(); ?> 
<?php
  require_once("connect.php");
  require_once("helpers.php");
  $dbh = ConnectDB();
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
?>

<?php
  $userId = $_SESSION['userId'];
  try{
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // get all the photos the user has uploaded
    $stmt = $dbh->prepare("SELECT uploadlocation FROM photos where fk_user_id=:userId");

    $stmt->execute(array(
        "userId" => $userId
    ));

    $photoLocation = $stmt->fetchAll(PDO::FETCH_OBJ);

    //delete user record from uses database
    $stmt = $dbh->prepare("DELETE FROM users where user_id=:userId");

    $deleteUserSuccess = $stmt->execute(array(
        "userId" => $userId
    ));
    //mark all comments made by the user to be displayed as [deleted]
    $stmt = $dbh->prepare("UPDATE comments set deleted_user = true where comment_user_id=:userId");

    $updateCommentSuccess = $stmt->execute(array(
        "userId" => $userId
    ));

    //remove all files uploaded by the user and send ajax response back
    if($deleteUserSuccess){
        header('Content-Type: application/json');
        $arrlength = count($photoLocation);
        for ($i = 0; $i < $arrlength; $i++){
          unlink($photoLocation[$i]->uploadlocation);
        }
        echo json_encode(array('id' => $userId, 'status' => "success"));
        //header("Location: logout.php");
        exit();
      }
    else {
      header('Content-Type: application/json');
      echo json_encode(array('message' => "could not delete photo", 'status' => "error"));
      exit();
    }
  }
  catch(PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json');
    die(json_encode(array('message' => $e->getMessage())));
  }

?>