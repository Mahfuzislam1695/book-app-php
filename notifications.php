<?php
session_start();
error_reporting(E_ALL);
include('includes/config.php');

if(!isset($_SESSION['login']) and !isset($_SESSION['alogin']))
{
  header('location:index.php');
}


$sql = "update notifications set notifications.opened=1 where notifications.send_to = :id";
$query = $dbh->prepare($sql);
$student = student()->StudentId;
$query->bindparam(':id',$student,PDO::PARAM_STR);
$query->execute();

function student($email = '')
{
    if($email)
    {
    }
    else if(isset($_SESSION['login']))
    {
      $email = $_SESSION['login'];
    }
    global $dbh;
    $sql = "SELECT * from tblstudents where EmailId = :email";
    $query = $dbh->prepare($sql);
    $query->bindparam(':email',$email,PDO::PARAM_STR);
    $query->execute();
    if($query->rowCount())
    {
      return $query->fetchObject();
    }
    return false;
  }


function notifications()
{
    
  global $dbh;
  $email = $_SESSION['login'];
  
  $student = student()->StudentId;
 
  $sql = "SELECT notifications.id, notifications.send_to, notifications.book_ref from notifications where notifications.send_to = :student ORDER by notifications.id DESC";
  $query = $dbh->prepare($sql);
  $query->bindparam(':student', $student, PDO::PARAM_STR);
  $query->execute();
  if($query->rowCount())
  {
    return $query->fetchAll(PDO::FETCH_OBJ);
  }
  return false;
  
}


function book_ref($id)
{
  global $dbh;
  $sql = "select * from tblbooks where ISBNNumber = :id";

  $query = $dbh->prepare($sql);
  $query->bindparam(':id', $id, PDO::PARAM_STR);
  $query->execute();
  if($query->rowCount())
  {
    return $query->fetchObject();
  }
  return false;
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Book Sharing System | Messages</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />

</head>
<body>
      <!------MENU SECTION START-->
<?php include('includes/header.php');?>
<!-- MENU SECTION END-->
    <div class="content-wrapper" style="background:#FCFCFC;">
      <div class="container">
        <div class="row pad-botm">
          <div class="col-md-12">
              <h4 class="header-line">Notifications (<span id='messages-counter'><?php echo notifications()?count(notifications()):0; ?></span>)
              </h4>
          </div>
        </div>
        <input type="hidden" id="focused" value="0">
        <div class="chat-box">

        <?php
        if(notifications())
        {
          foreach (notifications() as $notification)
          {
            ?>
            <div class="panel message-box my-2">
              <h4>Return Notify</h4>
              <?php
                
                echo "The Book <strong>". book_ref($notification->book_ref)->BookName ." (#$notification->book_ref) </strong> has been returned to you.";
                ?>

          </div><!--message-box-->
            <?php
          }
        }// if messages()
        else
        {
          ?>
          <div class="alert alert-danger">
            No new notifications
          </div>
          <?php
        }
         ?>
       </div><!--chat-box-->
      </div>
    </div>
     <!-- CONTENT-WRAPPER SECTION END-->
  <?php include('includes/footer.php');?>
      <!-- FOOTER SECTION END-->
    <!-- JAVASCRIPT FILES PLACED AT THE BOTTOM TO REDUCE THE LOADING TIME  -->
    <!-- CORE JQUERY  -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <!-- BOOTSTRAP SCRIPTS  -->
    <script src="assets/js/bootstrap.js"></script>
      <!-- CUSTOM SCRIPTS  -->
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/jquery.js"></script>
    

</body>
</html>
