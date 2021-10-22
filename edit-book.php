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

  function book_tags()
  {
    global $dbh;
    $bookid=intval($_GET['bookid']);
    $sql = "SELECT tags.id, tags.name from tags join book_tags on book_tags.tag_id = tags.id AND book_tags.book_id = :id";
    $query = $dbh->prepare($sql);
    $query->bindparam(':id', $bookid, PDO::PARAM_STR);
    $query->execute();
    if($query->rowCount())
    {
      return $query->fetchAll(PDO::FETCH_OBJ);
    }
    return false;
  }

  if(isset($_POST['update']))
  {
    $bookid=intval($_GET['bookid']);

    $sql = "SELECT tblbooks.book_image, tblbooks.BookName, tblcategory.CategoryName,tblcategory.id as cid,tblauthors.AuthorName,tblauthors.id as athrid,tblbooks.ISBNNumber,tblbooks.BookPrice,tblbooks.id as bookid from tblbooks join tblcategory on tblcategory.id=tblbooks.CatId join tblauthors on tblauthors.id=tblbooks.AuthorId where tblbooks.id=:bookid";

    $query = $dbh -> prepare($sql);
    $query->bindParam(':bookid',$bookid,PDO::PARAM_STR);
    $query->execute();
    $results=$query->fetchObject();

    $bookname=$_POST['bookname'];
    $category=$_POST['category'];
    $author=$_POST['author'];
    $isbn=$_POST['isbn'];
    $price=$_POST['price'];
    $desc = $_POST['desc'];
    $tags=$_POST['tag'];

    $image = $_FILES['book_image'];

    if(!empty($image['name']) or $image['name'] != '')
    {
      if ($results->book_image != '')
      {
        $unlinked_file = "assets/img/$results->book_image";
        unlink($unlinked_file);
      }

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

    $sql="update  tblbooks set BookName=:bookname,CatId=:category,AuthorId=:author,ISBNNumber=:isbn,BookPrice=:price,
    book_description = :book_description";
    if(!empty($_FILES['book_image']['name']) or $_FILES['book_image']['name'] != '')
    {
      $sql .= ", book_image = :book_image";
    }
    $sql.= " where id=:bookid";

    $query = $dbh->prepare($sql);
    $query->bindParam(':bookname',$bookname,PDO::PARAM_STR);
    $query->bindParam(':category',$category,PDO::PARAM_STR);
    $query->bindParam(':author',$author,PDO::PARAM_STR);
    $query->bindParam(':isbn',$isbn,PDO::PARAM_STR);
    $query->bindParam(':price',$price,PDO::PARAM_STR);
    $query->bindParam(':book_description',$book_description,PDO::PARAM_STR);
    if (!empty($_FILES['book_image']['name']))
    {
      $query->bindParam(':book_image',$image,PDO::PARAM_STR);
    }
    $query->bindParam(':bookid',$bookid,PDO::PARAM_STR);

    $query->execute();

    // Deleting old tags
    $sql = "DELETE FROM book_tags WHERE book_id = :book_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':book_id', $bookid, PDO::PARAM_STR);
    $query->execute();

    if($tags!= '')
    {
      foreach ($tags as $tag)
      {
        $sql = "INSERT INTO book_tags (book_id, tag_id) VALUES(:book_id,:tag_id)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':book_id', $bookid, PDO::PARAM_STR);
        $query->bindParam(':tag_id', $tag, PDO::PARAM_STR);
        $query->execute();
      }
    }
    $_SESSION['msg']="Book info updated successfully";
    header('location:manage-books.php');
  }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Book Sharing System | Edit Book</title>
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
<?php
$bookid=intval($_GET['bookid']);

$sql = "SELECT tblbooks.book_image, tblbooks.book_description, tblbooks.BookName, tblcategory.CategoryName,tblcategory.id as cid,tblauthors.AuthorName,tblauthors.id as athrid,tblbooks.ISBNNumber,tblbooks.BookPrice,tblbooks.id as bookid from tblbooks join tblcategory on tblcategory.id=tblbooks.CatId join tblauthors on tblauthors.id=tblbooks.AuthorId where tblbooks.id=:bookid";

$query = $dbh -> prepare($sql);
$query->bindParam(':bookid',$bookid,PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{               ?>

<div class="form-group">
<label>Book Name<span style="color:red;">*</span></label>
<input class="form-control" type="text" name="bookname" value="<?php echo htmlentities($result->BookName);?>" required />
</div>

<div class="form-group">
  <label for="desc">Book description</label>
  <textarea name="desc" placeholder="book short description" class="form-control"><?php echo htmlentities($result->book_description);?></textarea>
</div>

<div class="form-group">
<label> Category<span style="color:red;">*</span></label>
<select class="form-control" name="category" required="required">
<option value="<?php echo htmlentities($result->cid);?>"> <?php echo htmlentities($catname=$result->CategoryName);?></option>
<?php
$status=1;
$sql1 = "SELECT * from  tblcategory where Status=:status";
$query1 = $dbh -> prepare($sql1);
$query1-> bindParam(':status',$status, PDO::PARAM_STR);
$query1->execute();
$resultss=$query1->fetchAll(PDO::FETCH_OBJ);
if($query1->rowCount() > 0)
{
foreach($resultss as $row)
{
if($catname==$row->CategoryName)
{
continue;
}
else
{
    ?>
<option value="<?php echo htmlentities($row->id);?>"><?php echo htmlentities($row->CategoryName);?></option>
 <?php }}} ?>
</select>
</div>


<div class="form-group">
<label> Author<span style="color:red;">*</span></label>
<select class="form-control" name="author" required="required">
<option value="<?php echo htmlentities($result->athrid);?>"> <?php echo htmlentities($athrname=$result->AuthorName);?></option>
<?php

$sql2 = "SELECT * from  tblauthors ";
$query2 = $dbh -> prepare($sql2);
$query2->execute();
$result2=$query2->fetchAll(PDO::FETCH_OBJ);
if($query2->rowCount() > 0)
{
foreach($result2 as $ret)
{
if($athrname==$ret->AuthorName)
{
continue;
} else{

    ?>
<option value="<?php echo htmlentities($ret->id);?>"><?php echo htmlentities($ret->AuthorName);?></option>
 <?php }}} ?>
</select>
</div>

<div class="form-group">
<label>ISBN Number<span style="color:red;">*</span></label>
<input class="form-control" type="text" name="isbn" value="<?php echo htmlentities($result->ISBNNumber);?>"  required="required" />
<p class="help-block">An ISBN is an International Standard Book Number.ISBN Must be unique</p>
</div>

 <div class="form-group">
 <label>Price in USD<span style="color:red;">*</span></label>
 <input class="form-control" type="text" name="price" value="<?php echo htmlentities($result->BookPrice);?>"   required="required" />
 </div>

 <div class="form-group">
   <?php
    if (tags())
    {
      $tagIds = array();
      if(book_tags())
      {
        foreach (book_tags() as $tag)
        {
          $tagIds []= $tag->id;
        }
      }

      ?>
      <label for="">Tags</label><br>
      <?php
      foreach (tags() as $tag) {
      ?>
        <div class="checkbox">
           <label><input type="checkbox" <?php echo in_array($tag->id, $tagIds)?'checked':'';?> name="tag[]" value="<?php echo $tag->id;?>"><?php echo $tag->name; ?></label>
        </div>

      <?php
      }
    }
    ?>

 </div>

 <div class="form-group book-list">
   <label for="Image">Book Image</label>
   <div class="book-list-image">
     <?php
     if ($result->book_image == '') {
       echo "<img src='assets/img/book.jpg' style='margin:5px;width:100px;height:100px;'>";
     }
     else {
       echo "<img src='assets/img/$result->book_image'>";
     }
      ?>
   </div>
   <input type="file" name="book_image" id="Image" class="form-control">
 </div>

 <?php }} ?>
<button type="submit" name="update" class="btn btn-info">Update </button>

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
