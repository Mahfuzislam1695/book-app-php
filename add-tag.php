<?php
session_start();
error_reporting(E_ALL);
include('includes/config.php');
function student()
{
  global $dbh;
  $email = $_SESSION['login'];
  $sql = "SELECT StudentId from tblstudents WHERE EmailId = :email";
  $query = $dbh->prepare($sql);
  $query->bindParam(':email',$email, PDO::PARAM_STR);
  $query->execute();
  $student = $query->fetchObject();
  if($query->rowCount())
  {
    return $student;
  }
  return false;
}
if(!isset($_SESSION['login']))
{
  header('location:index.php');
}
else{

if(isset($_POST['create']))
{
$name=$_POST['tag'];

$sql="INSERT INTO  tags(name) VALUES(:name)";
$query = $dbh->prepare($sql);
$query->bindParam(':name',$name,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
  $_SESSION['msg']="Author Listed successfully";
  header('location:manage-tags.php');
}
else
{
  $_SESSION['error']="Something went wrong. Please try again";
  header('location:manage-tags.php');
}

}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Book Sharing System | Add Tag</title>
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

    <div class="content-wrapper">
         <div class="container">
        <div class="row pad-botm">
            <div class="col-md-12">
                <h4 class="header-line">Add Tag</h4>

                            </div>

</div>
<div class="row">
<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
<div class="panel panel-info">
<div class="panel-heading">
Tag Info
</div>
<div class="panel-body">
<form role="form" method="post">
<div class="form-group">
<label>Tag Name</label>
<input class="form-control" type="text" name="tag" autocomplete="off"  required />
</div>

<button type="submit" name="create" class="btn btn-info">Add </button>

                                    </form>
                            </div>
                        </div>
                            </div>

        </div>

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
</body>
</html>
<?php } ?>
