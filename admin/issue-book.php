<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
    {
header('location:index.php');
}
else{

  function findBook($bookid = '')
  {
    $id = $_SESSION['alogin'];
    global $dbh;
    if($bookid)
    {
      $sql="SELECT availability FROM tblbooks WHERE id = :id";

      $query = $dbh->prepare($sql);
      $query->bindParam(':id', $bookid, PDO::PARAM_STR);
      $query->execute();

      if($query->rowCount())
      {
        return $query->fetchObject()->availability;
      }
    }
    else
    {
      $sql="SELECT id FROM tblbooks WHERE uploaded_by = :id";

      $query = $dbh->prepare($sql);
      $query->bindParam(':id', $id, PDO::PARAM_STR);
      $query->execute();

      if($query->rowCount())
      {
        return $query->fetchAll(PDO::FETCH_OBJ);
      }
    }
    return false;

  }

if(isset($_POST['issue']))
{

  $books = array();

  foreach (findBook() as $book)
  {
    $books[] = $book->id;
  }


  $studentid=strtoupper($_POST['studentid']);
  $bookid=$_POST['bookdetails'];
  $owner_id = $_SESSION['alogin'];;
  $type = 'admin';
  $return_date = $_POST['return_date'].date(' h:i:s');
  $ReturnStatus = 0;
  $IssuesDate = date('Y-m-d h:i:s');

  if($return_date < date('Y-m-d h:i:s'))
  {
    ?>
    <div class="alert alert-danger">
      Enter Future date or date Today.
    </div>
    <?php
  }
  else if (!in_array($bookid, $books)) {
    ?>
    <div class="alert alert-danger">
      That book does not belongs to you!
    </div>
    <?php
  }
  else if(findBook($bookid) == 0 or findBook($bookid) == '0')
  {
    ?>
    <div class="alert alert-danger">
      That book is already issued
    </div>
    <?php
  }
  else if($bookid == '' or $return_date == '')
  {
    ?>
    <div class="alert alert-danger">
      Fields with * are required.
    </div>
    <?php
  }

  else
  {
    $date = date('Y-m-d h:i:s');

    $sql="INSERT INTO  tblissuedbookdetails(StudentID,BookId,StudentID_owner,uploader_type,IssuesDate,ReturnDate,RetrunStatus) VALUES
    (:studentid,:bookid,:StudentID_owner,:uploader_type,:IssuesDate,:ReturnDate,:ReturnStatus)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':studentid',$studentid,PDO::PARAM_STR);
    $query->bindParam(':bookid',$bookid,PDO::PARAM_STR);
    $query->bindParam(':StudentID_owner',$owner_id,PDO::PARAM_STR);
    $query->bindParam(':uploader_type',$type,PDO::PARAM_STR);
    $query->bindParam(':IssuesDate',$date,PDO::PARAM_STR);
    $query->bindParam(':ReturnDate',$return_date,PDO::PARAM_STR);
    $query->bindParam(':ReturnStatus',$ReturnStatus,PDO::PARAM_STR);
    $query->execute();
    $lastInsertId = $dbh->lastInsertId();

    if($lastInsertId)
    {
      $_SESSION['msg']="Book issued successfully";
      $id = $bookid;
      //set availability to zero
      $sql = "update tblbooks set availability = 0 where id = :id";
      $query = $dbh->prepare($sql);
      $query->bindParam(':id',$id,PDO::PARAM_STR);
      $query->execute();

      header('location:manage-issued-books.php');
    }
    else
    {
      $_SESSION['error']="Something went wrong. Please try again";
      header('location:manage-issued-books.php');
    }
  }//else

}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Book Sharing System | Issue a new Book</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <!-- GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
<script>
// function for get student name
function getstudent() {
$("#loaderIcon").show();
jQuery.ajax({
url: "get_student.php",
data:'studentid='+$("#studentid").val(),
type: "POST",
success:function(data){
$("#get_student_name").html(data);
$("#loaderIcon").hide();
},
error:function (){}
});
}

//function for book details
function getbook() {
$("#loaderIcon").show();
jQuery.ajax({
url: "get_book.php",
data:'bookid='+$("#bookid").val(),
type: "POST",
success:function(data){
$("#get_book_name").html(data);
$("#loaderIcon").hide();
},
error:function (){}
});
}

</script>
<style type="text/css">
  .others{
    color:red;
}

</style>


</head>
<body>
      <!------MENU SECTION START-->
<?php include('includes/header.php');?>
<!-- MENU SECTION END-->
    <div class="content-wra
    <div class="content-wrapper">
         <div class="container">
        <div class="row pad-botm">
            <div class="col-md-12">
                <h4 class="header-line">Issue a New Book</h4>

                            </div>

</div>
<div class="row">
<div class="col-md-10 col-sm-6 col-xs-12 col-md-offset-1"">
<div class="panel panel-info">
<div class="panel-heading">
Issue a New Book
</div>
<div class="panel-body">
<form role="form" method="post">

<div class="form-group">
<label>Srtudent id<!--<span style="color:red;">*</span>--></label>
<input class="form-control" type="text" name="studentid" id="studentid" onBlur="getstudent()" autocomplete="off"  required />
</div>

<div class="form-group">
<span id="get_student_name" style="font-size:16px;"></span>
</div>





<div class="form-group">
<label>ISBN Number or Book Title<span style="color:red;">*</span></label>
<input class="form-control" type="text" name="booikid" id="bookid" onBlur="getbook()"  required="required" />
</div>

 <div class="form-group">

  <select  class="form-control" name="bookdetails" id="get_book_name" readonly>

 </select>
 </div>

 <div class="form-group">
   <label for="">Return Date <span style="color:red;">*</span></label>
    <input type="date" name="return_date" class="form-control" style="width:30%;">
 </div>

<button type="submit" name="issue" id="submit" class="btn btn-info">Issue Book </button>

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
