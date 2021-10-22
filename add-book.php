<?php
session_start();
error_reporting(E_ALL);
include('includes/config.php');
if(!isset($_SESSION['login']))
{
  header('location:index.php');
}
else
{

  if(isset($_POST['add']))
  {
    $bookname=$_POST['bookname'];
    $category=$_POST['category'];
    $author=$_POST['author'];
    $isbn=$_POST['isbn'];
    $price=$_POST['price'];
    $tags=$_POST['tag'];
    $desc = $_POST['desc'];

    $uploaded_by = isset($_SESSION['stdid'])?$_SESSION['stdid']:'';
    $uploaded_type = 'user';
    $image = $_FILES['book_image'];
    if(!empty($image['name']))
    {
      $file_tmp=$image['tmp_name'];
			$file_error=$image['error'];
			$file_type=$image['type'];
			$file_size=$image['size'];
			$file_ext=explode('.', $image['name']);
			$file_ext=strtolower(end($file_ext));
      $image=uniqid('',true).'.'. $file_ext;
			$file_destination="assets/img/$image";
      move_uploaded_file($file_tmp, $file_destination);
    }

    $sql="INSERT INTO  tblbooks(BookName,CatId,AuthorId,ISBNNumber,BookPrice,uploaded_by,uploaded_type,book_description, book_image) VALUES(:bookname,:category,:author,:isbn,:price,:uploaded_by,:uploaded_type, :book_description, :book_image)";

    $query = $dbh->prepare($sql);
    $query->bindParam(':bookname',$bookname,PDO::PARAM_STR);
    $query->bindParam(':category',$category,PDO::PARAM_STR);
    $query->bindParam(':author',$author,PDO::PARAM_STR);
    $query->bindParam(':isbn',$isbn,PDO::PARAM_STR);
    $query->bindParam(':price',$price,PDO::PARAM_STR);
    $query->bindParam(':uploaded_by', $uploaded_by, PDO::PARAM_STR);
    $query->bindParam(':uploaded_type',$uploaded_type, PDO::PARAM_STR);
    $query->bindParam(':book_description',$desc, PDO::PARAM_STR);
    $query->bindParam(':book_image',$image, PDO::PARAM_STR);
    $query->execute();
    $lastInsertId = $dbh->lastInsertId();
    if($lastInsertId)
    {
      if($tags!= '')
      {
        foreach ($tags as $tag)
        {
          $sql = "INSERT INTO book_tags (book_id, tag_id) VALUES(:book_id,:tag_id)";
          $query = $dbh->prepare($sql);
          $query->bindParam(':book_id', $lastInsertId, PDO::PARAM_STR);
          $query->bindParam(':tag_id', $tag, PDO::PARAM_STR);
          $query->execute();
        }
      }
      $_SESSION['msg']="Book Listed successfully";
      header('location:manage-books.php');
    }
    else
    {
      $_SESSION['error']="Something went wrong. Please try again";
      header('location:manage-books.php');
    }

  }//if isset $_POST['add']

  function tags()
  {
    global $dbh;
    $sql = "SELECT * FROM tags";
    $query = $dbh->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    if($query->rowCount())
    {
      return $results;
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
    <title>Online Book Sharing System | Add Book</title>
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
              <h4 class="header-line">Add Book</h4>
          </div>
        </div>
<div class="row">
<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
<div class="panel panel-info">
<div class="panel-heading">
Book Info
</div>
<div class="panel-body">
<form role="form" method="post" enctype="multipart/form-data">
<div class="form-group">
  <label>Book Name<span style="color:red;">*</span></label>
  <input class="form-control" type="text" name="bookname" autocomplete="off"  required />
</div>

<div class="form-group">
  <label for="desc">Book description</label>
  <textarea name="desc" placeholder="book short description" class="form-control"></textarea>
</div>

<div class="form-group">
<label> Category<span style="color:red;">*</span></label>
<select class="form-control" name="category" required="required">
<option value=""> Select Category</option>
<?php
$status=1;
$sql = "SELECT * from  tblcategory where Status=:status";
$query = $dbh -> prepare($sql);
$query -> bindParam(':status',$status, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{               ?>
<option value="<?php echo htmlentities($result->id);?>"><?php echo htmlentities($result->CategoryName);?></option>
 <?php }} ?>
</select>
</div>


<div class="form-group">
<label> Author<span style="color:red;">*</span></label>
<select class="form-control" name="author" required="required">
<option value=""> Select Author</option>
<?php

$sql = "SELECT * from  tblauthors ";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{               ?>
<option value="<?php echo htmlentities($result->id);?>"><?php echo htmlentities($result->AuthorName);?></option>
 <?php }} ?>
</select>
</div>

<div class="form-group">
<label>ISBN Number<span style="color:red;">*</span></label>
<input class="form-control" type="text" name="isbn"  required="required" autocomplete="off"  />
<p class="help-block">An ISBN is an International Standard Book Number.ISBN Must be unique</p>
</div>

 <div class="form-group">
 <label>Price<span style="color:red;">*</span></label>
 <input class="form-control" type="text" name="price" autocomplete="off"   required="required" />
 </div>

 <div class="form-group">
   <?php
    if (tags()) {
      ?>
      <label for="">Tags</label><br>
      <?php
      foreach (tags() as $tag) {
      ?>
        <div class="checkbox">
           <label><input type="checkbox" name="tag[]" value="<?php echo $tag->id;?>"><?php echo $tag->name; ?></label>
        </div>

      <?php
      }
    }
    ?>

 </div>

 <div class="form-group">
   <label for="Image">Book Image</label>
   <input type="file" name="book_image" id="Image" class="form-control">
 </div>

<button type="submit" name="add" class="btn btn-info">Add </button>

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
