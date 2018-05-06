<?php
// delete_photo.php : The page is called as an ajax request when user confirms photo deletion
// The photo and all related comments are deleted and the photo is removed from the location.
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
  $photoId = $_POST['photoId'];
  try{
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // get photo location for the photo to be deleted
    $stmt = $dbh->prepare("SELECT uploadlocation FROM photos where photo_id=:photoId");

    $stmt->execute(array(
        "photoId" => $photoId
    ));

    $photoLocation = $stmt->fetchAll(PDO::FETCH_OBJ);

    // remove photo from database which will also removes comments associated with photo
    $stmt = $dbh->prepare("DELETE FROM photos where photo_id=:photoId");

    $deletePhotoSuccess = $stmt->execute(array(
        "photoId" => $photoId
    ));

    //remove photo from location and send response back to the client.
    if($deletePhotoSuccess){
        header('Content-Type: application/json');
        unlink($photoLocation[0]->uploadlocation);
        echo json_encode(array('id' => $photoId, 'status' => "success", 'location' => $photoLocation[0]->uploadlocation));
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