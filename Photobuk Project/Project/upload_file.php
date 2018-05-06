<?php  session_start(); ?> 
<?php

  require_once("connect.php");
  require_once("helpers.php");
  $dbh = ConnectDB();
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  ini_set('display_errors', 'On');

// In order for this to work, there has to be a directory where
// the web server can save files, and where you can go in and work
// with them later.  That directory has to be mode 777, which are
// the permissions on "./images".
// To keep everybody in the world from monkeying around in there,
// the directory UPLOADED is mode 701.  So people on Elvis in
// group "everyone" can't go in, but you can, and the webserver can.
// This is not great security, and it could be hacked, but it'll keep
// out the casual visitor.



if (!file_exists("./images/archive/" . $_SESSION["userId"])) {
    // bug in mkdir() requires you to chmod()
    mkdir("./images/archive/". $_SESSION["userId"], 0777);
    chmod("./images/archive/". $_SESSION["userId"], 0777);
    echo "done.</p>";
}

if(isset($_FILES["userfile"])){
// Make sure it was uploaded

 if(fileValid($_FILES["userfile"]) && strlen(trim($_POST['photo-description']))<=250 ){
  if (! is_uploaded_file ( $_FILES["userfile"]["tmp_name"] ) ) {
      echo $_FILES["userfile"]["name"];
      die("Error: " . $_FILES["userfile"]["name"] . " did not upload.");
  }
  $targetname = "./images/archive/" . $_SESSION["userId"] . "/" .$_FILES["userfile"]["name"];
  if (file_exists($targetname)) {
    $_SESSION['fileUploadError'] = "You have already uploaded a file with similar name. Please change the file name before trying again if you are trying to upload a different file with same name";
    header('Location: index.php');
  } 
  else {
      if ( copy($_FILES["userfile"]["tmp_name"], $targetname) ) {
          // if we don't do this, the file will be mode 600, owned by
          // www, and so we won't be able to read it ourselves
          chmod($targetname, 0444);
          // but we can't upload another with the same name on top,
          // because it's now read-only
        try {
        
          $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $stmt = $dbh->prepare("INSERT INTO photos(uploaddate, uploadname, caption, uploadlocation, fk_user_id) VALUES (:uploaddate, :name, :caption, :location, :userId)");
          date_default_timezone_set('America/New_York');
          $currentDate = date('Y-m-d H:i:s', time());
          $insertimagesuccess = $stmt->execute(array(
              "uploaddate" => $currentDate,
              "name" => $_FILES["userfile"]["name"],
              "caption" => $_POST["photo-description"],
              "location" => $targetname,
              "userId" => $_SESSION['userId']
          ));
          if($insertimagesuccess) {
            $photoId = $dbh->lastInsertId(); 
            if(!empty($_POST['photo-description'])){
                $tagsArray = getHashtags($_POST['photo-description']);
                $arrlength = count($tagsArray);
                for($x = 0; $x < $arrlength; $x++) {

                    if(!empty($tagsArray[$x])){
                        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        $tagText = substr($tagsArray[$x], 1);
                        $stmt = $dbh->prepare("INSERT INTO tags(fk_photo_id, tag_text) VALUES (:photoId, :tagText)");
                        $inserttag = $stmt->execute(array(
                            "photoId" => $photoId,
                            "tagText" => $tagText
                        ));

                        if($inserttag){
                            echo "tag successfully inserted" . $tagsArray[$x];
                        }
                    }
                }
            }
            $_SESSION['fileUploadError'] = '';
            header("Location: index.php");
          }
        }
        catch(PDOException $e) {
          $_SESSION['fileUploadError'] = "DB Error". $e->getMessage(). " Please try again after some time";
          header("Location:index.php");
        }

      } else {
          $_SESSION['fileUploadError'] = "Error copying ". $_FILES["userfile"]["name"]. " Please try again after some time";
          header("Location:index.php");
      }
  }
 }
else {
        $_SESSION['fileUploadError'] = getError($_FILES["userfile"]);
        echo $_SESSION['fileUploadError'];
        header("Location:index.php");
    }
}
else {
    $_SESSION['fileUploadError'] = "Please choose a file to upload.";
    header("Location:index.php");
}

function fileValid($file){
    $maxsize = 2097152;
    $fileTypes = array(
        'gif',
        'GIF',
        'jpg',
        'JPG',
        'jpeg',
        'JPEG',
        'PNG',
        'png'
    );

    $path_parts = pathinfo($file["name"]);
    $extension = $path_parts['extension'];
    if($file["size"]>$maxsize || $file["size"] == 0 || !in_array($extension, $fileTypes)) {
        return false;
    }
    else {
        return true;
    }
}

function getError($file){
    $maxsize = 2097152;
    $fileTypes = array(
        'gif',
        'GIF',
        'jpg',
        'JPG',
        'jpeg',
        'JPEG',
        'PNG',
        'png'
    );

    $path_parts = pathinfo($file["name"]);
    $extension = $path_parts['extension'];
    $filesize = $file["size"]/(1024*1024);
    if(!in_array($extension, $fileTypes) ){
        return 'File must be jpeg, jpg, gif or in png format. File:'. $file['name'];
    }
    else if($file["size"] > $maxsize || $file["size"] == 0){
        return 'File size cannot exceed 2mb.';
    } 
    else if(strlen(trim($_POST['photo-description'])) > 250){
        return 'Photo description cannot exceed 250 characters';
    }
    else {
        return 'Oops.. There is some error with file you are trying to upload.';
    }
}


?>