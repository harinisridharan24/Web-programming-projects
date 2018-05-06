<?php
// search_users.php : an ajax request is made to retrieve the users matching the text entered in photo description field 
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
  $searchTerm =  $_POST['searchTerm'];
  try{
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // search for users based on search term
    $param = "%{$searchTerm}%";
    $stmt = $dbh->prepare("SELECT user_id,fname, lname FROM users where fname LIKE :fname or lname LIKE :lname");

    $stmt->execute(array(
        "fname" => $param,
        "lname" => $param
    ));

    $userlist = $stmt->fetchAll(PDO::FETCH_OBJ);
    $usernames = array();
    // send the list back to the client
    header('Content-Type: application/json');
    $arrlength = count($userlist);
    echo json_encode(array('users' => $userlist, 'status' => "success"));
    exit();
  }
  catch(PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json');
    die(json_encode(array('message' => $e->getMessage())));
  }

?>