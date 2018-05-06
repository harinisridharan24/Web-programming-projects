<?php
// search.php : search page 
// allows user to search photos via tags
// Harini Sridharan
?>

<?php $this_page = "search.php"; ?>
<!DOCTYPE html>

<?php  session_start(); ?> 

<?php

if(!isset($_SESSION['email']) || empty($_SESSION['email']))   // Checking whether the session is already there or not if 
                              // true then header redirect it to the home page directly 
{
    header("Location:login.php"); 
 }


  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);



 ?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>FotoBuk</title>
        <link href="favicon.ico" rel="icon" type="image/x-icon" />
        <!-- CSS-->
        <?php include "stylesheets.php" ?>
        <!-- Compressed CSS -->

        <?php include "scripts.php" ?>

    </head>
    <body>
        <!-- common header -->
        <?php include "header.php" ?>
        <div class="clear"></div>
        <section class="content">
            <!-- PAGE CONTENT HERE determined by $this_page value -->
            <!-- 'content/home.php' have the content-->
            
<?php
  require_once("connect.php");
  require_once("helpers.php");
  $dbh = ConnectDB();
  if(count(checkUser($_SESSION['email'])) == 0){
     header("Location:login.php");
   }
  if(!empty($_POST['search-text'])){
    $searchText = $_POST['search-text'];
  }
  else {
    $searchText = '';
  }
  ?>

<!-- search form -->
<form id="search-photo" enctype="multipart/form-data" method="POST" action="search.php">
    <input type="text" name="search-text" placeholder="Search via tags" maxlength="255" value="<?php echo $searchText;?>"></input>
    <div class="search-buttons">
        <input type="submit"/>
    </div>
</form>


<?php

if(!empty($_POST['search-text'])){
    try {
        $searchText = $_POST['search-text'];
        // search for photos matching the search text tag
        $query = "SELECT photo_id, uploadname, uploaddate, caption, uploadlocation, users.fname, users.lname, users.user_id  FROM photos, tags, users where tag_text = :searchText and tags.fk_photo_id = photos.photo_id  and photos.fk_user_id = users.user_id order by uploaddate desc";
        $stmt = $dbh->prepare($query);
        $stmt->execute(array(':searchText' => $searchText));
        $photos = $stmt->fetchAll(PDO::FETCH_OBJ);

        $howmany = count($photos);
        // displays empty photos message if no photos are found
        echo "<div id='search-container' class='large-12 small-12'>";
        if ( $howmany == 0) {
            echo "<div id='empty-photos' class='callout warning'>Oopsie. There are no photos matching your search text</div>\n";
        }
        $username = $_SESSION['username'];
        
        $userQuery = "SELECT user_id FROM users";
        $stmt = $dbh->prepare($userQuery);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);
        // creates html for each photo block       
        foreach ($photos as $photo) {
            if(!empty($photo->uploaddate)){
              $uploadDate = getTimeDifference($photo->uploaddate);
            }
            else {
              $uploadDate = '';
            }
            echo "<div id='photo_$photo->photo_id' class='photo-wrapper large-5 medium-12 small-12 '>";
              echo "<input class='delete_photo_id' type='hidden' value='$photo->photo_id' />";
              echo "<div class='photo-wrapper-left'>";
                echo "<div class='photo-name'><a href='index.php?id=$photo->user_id'><img class='default-pic' src='default.png' width='50' height='50' /><h4>$photo->fname $photo->lname</h4></a></div>";
                echo "<div class='photo-date'><label class='upload-date'>$uploadDate</label></div>";
                echo "<div class='photo-caption'><label>$photo->caption</label></div>";
              echo "</div>";
              echo "<div class='photo-wrapper-right'>";
                if($photo->user_id == $_SESSION["userId"] ){
                  echo "<i class='fi-x photo-delete large'></i>";
                }
              echo "</div>";
              echo "<div class='photo-source'><a href='photo.php?id=$photo->photo_id'><img src='$photo->uploadlocation' alt='$photo->uploadname' /></a></div>";
 
            $query = "SELECT comment_id,username,comment_text, comment_user_id, comment_date FROM comments where fk_photo_id=:photoId order by comment_date desc";

            $stmt = $dbh->prepare($query);
            $stmt->execute(array(':photoId' => $photo->photo_id));
            $comments = $stmt->fetchAll(PDO::FETCH_OBJ);
             // displays comment count           
            $commentCount = count($comments);
            echo "<div style='float:right;'><i class='comment-secton-toggle fi-comment large'><span class='count'>$commentCount</span></i></div>";            
              // creates add comment section for each photo block
              echo "<div class='comments-container'>";
              echo "<form class='comment-form' method='post' action=''>";
                echo "<input class='photo_id' name='photo_id' type='hidden' value='$photo->photo_id' />";
                echo "<input class='username' name='username' type='hidden' value= '$username' />";
                echo "<textarea class='comment textarea' name='comment' placeholder='Your comment'></textarea>";
                echo "<div class='comment-error error'></div>";
                echo "<div id='post-comment-button'>";
                  echo "<input type='submit' value='Post'>";
                echo "</div>";
              echo "</form>";
              echo "<div class='comment-list'>";
                foreach ($comments as $comment) {
                  $userfound = false;
                  foreach ($users as $user){
                    if($user->user_id == $comment->comment_user_id){
                      $userfound = true;
                      break;
                    }
                  }

                  if(!empty($comment->comment_date)){
                    $commentDate = getTimeDifference($comment->comment_date);
                  }
                  else {
                    $commentDate = '';
                  } 
                }
              echo "</div>";
            echo "</div>";
          echo "</div>";
        echo "</div>";
        }
    } catch(PDOException $e) {
        die ('PDO error fetching grade: ' . $e->getMessage() );
    }
}

?>


        </section>
        <div id="delete-comment-modal" class="reveal small" data-reveal>
            <h2 id="modalTitle">Are you sure?</h2>
            <input id="delete-comment-id" type="hidden" value="" />
            <p class="lead">You will not be able to undo this action. Are you sure you want to continue?</p>
            <p>
                <button id="delete-comment" class="alert button">Delete</button>
                <button  id="cancel-comment" class="secondary button cancel">Cancel</button> 
            </p>
              <button class="close-button" data-close aria-label="Close modal" type="button">
                <span aria-hidden="true">&times;</span>
              </button>
        </div>
        <!-- different modals for delete comment, delete photo and delete account -->
        <div id="delete-photo-modal" class="reveal small" data-reveal>
            <h2 id="modalTitle">Are you sure?</h2>
            <input id="delete-photo-id" type="hidden" value="" />
            <p class="lead">Deleting the photo will also remove all the comments associated with it. Are you sure you want to continue?</p>
            <p>
                <button id="delete-photo" class="alert button">Delete</button>
                <button  id="cancel-photo" class="secondary button cancel">Cancel</button> 
            </p>
              <button class="close-button" data-close aria-label="Close modal" type="button">
                <span aria-hidden="true">&times;</span>
              </button>
        </div>

        <div id="delete-account-modal" class="reveal small" data-reveal>
            <h2 id="modalTitle">Are you sure?</h2>
            <input id="delete-photo-id" type="hidden" value="" />
            <p class="lead">We are sorry you wish to delete your account. By deleting your account, all your beautiful photos you had uploaded will be deleted and the jokes you shared in the comments section. The comments you posted on other user's photo will still be visible. Are you sure you want to delete your account ?</p>
            <p>
                <button id="delete-account" class="alert button">Delete</button>
                <button  id="cancel-delete" class="secondary button cancel">Cancel</button> 
            </p>
              <button class="close-button" data-close aria-label="Close modal" type="button">
                <span aria-hidden="true">&times;</span>
              </button>
        </div>
      <div id="loader">
            <div class='loader-image'></div>
      </div>
        <!-- common footer -->
        
        <?php include "footer.php" ?>
        <script>
            $(document).foundation();
            function readCookie(name) {
                var nameEQ = escape(name) + "=";
                var ca = document.cookie.split(';');
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                    if (c.indexOf(nameEQ) === 0) return unescape(c.substring(nameEQ.length, c.length));
                }
                return null;
            }
            window.setInterval(function() {
                if(readCookie('loggedout')==1) {
                    window.location.assign('logout.php')
                    //Or whatever else you want!
                }
            },2000)
        </script>
    </body>
</html>