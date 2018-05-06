<?php
// post_comment.php : The code is run as an ajax request and post a comment
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
  // validates the comment , comment date and photo fo which the comment is posted 
  if (!empty($_POST['comment']) && !empty($_POST['photoId']) && !empty($_POST['commentDate'])){
    // preventing sql injection
    
    $userId = $_SESSION['userId'];
    $username = $_SESSION['username'];
    $comment = $_POST['comment'];
    $photoId = $_POST['photoId'];
    $commentDate = $_POST['commentDate'];
    // insert new comment into comment table

     try {
      $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      // adds the comment to the comment database
      $stmt = $dbh->prepare("INSERT INTO comments(comment_user_id, fk_photo_id, username, comment_text, comment_date) VALUES (:userId, :photoId, :username, :comment, :commentDate)");

      $postCommentSuccess = $stmt->execute(array(
          "userId" => $userId,
          "photoId" => $photoId,
          "username" => $username,
          "comment" => $comment,
          "commentDate" => $commentDate
      ));
      //sends response back to the client
      if($postCommentSuccess){
        $commentId = $dbh->lastInsertId();
        header('Content-Type: application/json');
        echo json_encode(array('id' => $commentId, 'status' => "success", 'userId' => $userId));
        exit();
      }
      else {
        header('Content-Type: application/json');
        echo json_encode(array('message' => "could not upload comment", 'status' => "error"));
        exit();
      }
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


