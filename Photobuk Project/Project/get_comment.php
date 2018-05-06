<?php
// get_comment.php : The code is run as an ajax request and gets the comments for the particular photo when user clicks the comments icon
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
// code will run if request through ajax  
  if (!empty($_POST['photoId'])){
    
    $userId = $_SESSION['userId'];
    $username = $_SESSION['username'];
    $photoId = $_POST['photoId'];
      // get comments from the comment table in the descending order of comments posted
     try {
      $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $query = "SELECT comment_id,username,comment_text, comment_user_id, comment_date, deleted_user, photos.fk_user_id FROM comments, photos where fk_photo_id=:photoId  and comments.fk_photo_id = photos.photo_id order by comment_date desc";

      $stmt = $dbh->prepare($query);
      $stmt->execute(array(':photoId' => $photoId));
      $comments = $stmt->fetchAll(PDO::FETCH_OBJ);
      // get all users . send to check if the comment is made by a deleted user
      $query = "SELECT user_id FROM users";

      $stmt = $dbh->prepare($query);
      $stmt->execute();
      $users = $stmt->fetchAll(PDO::FETCH_OBJ);

      // response back to the client
      header('Content-Type: application/json');
      echo json_encode(array('status' => "success",'current_user_id'=> $userId ,'comments' => $comments, 'users' => $users ));
      exit();
     }
     catch(PDOException $e) {
      header('HTTP/1.1 500 Internal Server Error');
      header('Content-Type: application/json');
      die(json_encode(array('message' => $e->getMessage(), 'code' => "Not all fields were filled in")));
    }
  }
  else {
    {
      header('HTTP/1.1 500 Internal Server Error');
      header('Content-Type: application/json');
      die(json_encode(array('message' => 'Not all fields were filled in', 'code' => "Not all fields were filled in")));
    }
  }
?>


