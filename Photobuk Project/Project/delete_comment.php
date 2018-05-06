<?php
// delete_comment.php : The page is called as an ajax request when user confims to delete comment
// The comment is deleted from the databse
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
  $commentId = $_POST['commentId'];
  try{
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //remove the comment from database
    $stmt = $dbh->prepare("DELETE FROM comments where comment_id=:commentId");

    $deleteCommentSuccess = $stmt->execute(array(
        "commentId" => $commentId
    ));
    //send response back to the client
    if($deleteCommentSuccess){
        header('Content-Type: application/json');
        echo json_encode(array('id' => $commentId, 'status' => "success"));
        exit();
      }
    else {
      header('Content-Type: application/json');
      echo json_encode(array('message' => "could not delete comment", 'status' => "error"));
      exit();
    }
  }
  catch(PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json');
    die(json_encode(array('message' => $e->getMessage())));
  }

?>