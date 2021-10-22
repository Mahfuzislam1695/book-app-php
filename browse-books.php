<?php
session_start();
error_reporting(E_ALL);
include('includes/config.php');
if(!isset($_SESSION['login']) and !isset($_SESSION['alogin']))
{
  header('location:index.php');
}

function book()
{
  global $dbh;
  $id = $_SESSION['stdid'];

  $sql = "SELECT distinct tblauthors.AuthorName, tblbooks.AuthorId, tblbooks.book_image, tblstudents.StudentId, tblstudents.FullName, tblbooks.uploaded_by, tblbooks.BookName, tblbooks.id, tblbooks.book_description, tblbooks.BookPrice, tblbooks.availability as avail, tblbooks.RegDate from tblstudents join tblbooks on tblstudents.StudentId = tblbooks.uploaded_by join tblauthors on tblauthors.id= tblbooks.AuthorId where not tblstudents.StudentId = :id and tblbooks.availability = 1 ORDER BY id DESC";

  $query = $dbh->prepare($sql);
  $query->bindParam(':id',$id,PDO::PARAM_STR);
  $query->execute();
  if($query->rowCount())
  {
    return $query->fetchAll(PDO::FETCH_OBJ);
  }
  return false;
}

function book_tags()
{
  global $dbh;
  $id = $_SESSION['stdid'];
  $sql = "SELECT distinct tblauthors.AuthorName, tblbooks.book_image, tblbooks.AuthorId, tblstudents.StudentId, tblstudents.FullName, tblbooks.uploaded_by, tblbooks.BookName, tblbooks.id, tblbooks.book_description, tblbooks.BookPrice, tblbooks.availability as avail, tblbooks.RegDate from tblstudents join tblbooks on tblstudents.StudentId = tblbooks.uploaded_by join tblauthors on tblauthors.id = tblbooks.AuthorId join book_tags on tblbooks.id = book_tags.book_id where book_tags.tag_id = :book_id AND not tblstudents.StudentId = :id";

  $query = $dbh->prepare($sql);
  $book_id = isset($_GET['tagId'])?$_GET['tagId']:'';
  $query->bindParam(':book_id',$book_id,PDO::PARAM_STR);
  $query->bindParam(':id',$id,PDO::PARAM_STR);
  $query->execute();
  if($query->rowCount())
  {
    return $query->fetchAll(PDO::FETCH_OBJ);
  }
  return false;
}

function student_books()
{
  global $dbh;

  if(!isset($_GET['student']))
  {
    return false;
  }
  $id = $_SESSION['stdid'];

  $student = $_GET['student'];

  $sql = "SELECT distinct tblauthors.AuthorName, tblbooks.book_image, tblbooks.AuthorId, tblstudents.StudentId, tblstudents.FullName, tblbooks.uploaded_by, tblbooks.BookName, tblbooks.id, tblbooks.book_description, tblbooks.BookPrice, tblbooks.availability as avail, tblbooks.RegDate from tblstudents join tblbooks on tblstudents.StudentId = tblbooks.uploaded_by join tblauthors on tblauthors.id = tblbooks.AuthorId join book_tags on tblbooks.id = book_tags.book_id where not tblstudents.StudentId = :id AND tblstudents.StudentId = :student";

  $query = $dbh->prepare($sql);
  $query->bindParam(':id',$id,PDO::PARAM_STR);
  $query->bindParam(':student',$student,PDO::PARAM_STR);
  $query->execute();
  if($query->rowCount())
  {
    return $query->fetchAll(PDO::FETCH_OBJ);
  }
  return false;
}

function author_books()
{
  global $dbh;
  $id = $_SESSION['stdid'];

  if(!isset($_GET['author']))
  {
    return false;
  }

  $author = $_GET['author'];

  $sql = "SELECT distinct tblauthors.AuthorName, tblbooks.book_image, tblbooks.AuthorId, tblstudents.StudentId, tblstudents.FullName, tblbooks.uploaded_by, tblbooks.BookName, tblbooks.id, tblbooks.book_description, tblbooks.BookPrice, tblbooks.availability as avail, tblbooks.RegDate from tblstudents join tblbooks on tblstudents.StudentId = tblbooks.uploaded_by join tblauthors on tblauthors.id = tblbooks.AuthorId join book_tags on tblbooks.id = book_tags.book_id where not tblstudents.StudentId = :id AND tblbooks.AuthorId = :author";


  $query = $dbh->prepare($sql);
  $query->bindParam(':id',$id,PDO::PARAM_STR);
  $query->bindParam(':author',$author,PDO::PARAM_STR);
  $query->execute();
  if($query->rowCount())
  {
    return $query->fetchAll(PDO::FETCH_OBJ);
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
    <title>Online Book Sharing System | Browse Books</title>
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
              <h4 class="header-line">Browse Books</h4>
          </div>
        </div>

        <div class="books-container">
          <div class="books-list">

              <?php

              if (book() and !isset($_GET['author']) and !isset($_GET['student']) and !isset($_GET['tagId']))
              {
                foreach (book() as $info)
                {
                  ?>
                  <div class="row" style="margin:15px 0 15px 0;">
                    <div class="col-sm-2">
                      <div class="book-list-image">
                        <?php
                        if ($info->book_image == null)
                        {
                          echo "<img src='assets/img/book.jpg'>";
                        }
                        else
                        {
                          echo "<img src='assets/img/".$info->book_image."'>";
                        }
                         ?>
                      </div>
                    </div>

                    <div class="col-sm-8">
                      <div class="book-list-description">
                          <h4><a href="book.php?id=<?php echo $info->id; ?>">
                            <?php echo ucfirst($info->BookName); ?>
                          </a></h4>
                          <div class="book-and-student-info">
                            <p>
                              Uploaded By: <a href="?student=<?php echo $info->StudentId;?>"><?php echo $info->FullName; ?></a> |
                              Date: <?php
                              $date = new dateTime($info->RegDate);
				                      $date = date_format($date, 'M j, Y ');
                              echo $date;
                              ?> |
                              Author: <a href="?author=<?php echo $info->AuthorId;?>"><?php echo $info->AuthorName; ?></a>
                            </p>
                          </div>
                          <p>
                            <?php
                            echo strlen($info->book_description) > 245
                            ? substr($info->book_description, 0, 244).'...'
                            :$info->book_description;
                            ?>
                          </p>
                      </div>
                    </div>

                    <div class="col-sm-2">
                      <div class="book-list-exchange-button">
                        <div><a type="button" class="btn btn-primary" href="book.php?id=<?php echo $info->id; ?>">Exchange</a></div>
                      </div>
                    </div>

                  </div><!--row-->

                  <?php
                }
              }

              else if (isset($_GET['author']) and author_books() and !isset($_GET['student']) and !isset($_GET['tagId']))
              {
                foreach (author_books() as $info)
                {
                  ?>
                  <div class="row" style="margin:15px 0 15px 0;">
                    <div class="col-sm-2">
                      <div class="book-list-image">
                        <?php
                        if ($info->book_image == null)
                        {
                          echo "<img src='assets/img/book.jpg'>";
                        }
                        else
                        {
                          echo "<img src='assets/img/".$info->book_image."'>";
                        }
                         ?>
                      </div>
                    </div>

                    <div class="col-sm-8">
                      <div class="book-list-description">
                          <h4><a href="book.php?id=<?php echo $info->id; ?>">
                            <?php echo ucfirst($info->BookName); ?>
                          </a></h4>
                          <div class="book-and-student-info">
                            <p>
                              Uploaded By: <a href="?student=<?php echo $info->StudentId;?>"><?php echo $info->FullName; ?></a> |
                              Date: <?php
                              $date = new dateTime($info->RegDate);
				                      $date = date_format($date, 'M j, Y ');
                              echo $date;
                              ?> |
                              Author: <a href="?author=<?php echo $info->AuthorId;?>"><?php echo $info->AuthorName; ?></a>
                            </p>
                          </div>
                          <p>
                            <?php
                            echo strlen($info->book_description) > 245
                            ? substr($info->book_description, 0, 244).'...'
                            :$info->book_description;
                            ?>
                          </p>

                      </div>
                    </div>

                    <div class="col-sm-2">
                      <div class="book-list-exchange-button">
                        <div><a type="button" class="btn btn-primary" href="book.php?id=<?php echo $info->id; ?>">Exchange</a></div>
                      </div>
                    </div>

                  </div><!--row-->

                  <?php
                }
              }
              else if(isset($_GET['student']) and student_books() and !isset($_GET['author']) and !isset($_GET['tagId']))
              {
                foreach (student_books() as $info)
                {
                  ?>
                  <div class="row" style="margin:15px 0 15px 0;">
                    <div class="col-sm-2">
                      <div class="book-list-image">
                        <?php
                        if ($info->book_image == null)
                        {
                          echo "<img src='assets/img/book.jpg'>";
                        }
                        else
                        {
                          echo "<img src='assets/img/".$info->book_image."'>";
                        }
                         ?>
                      </div>
                    </div>

                    <div class="col-sm-8">
                      <div class="book-list-description">
                          <h4><a href="book.php?id=<?php echo $info->id; ?>">
                            <?php echo ucfirst($info->BookName); ?>
                          </a></h4>
                          <div class="book-and-student-info">
                            <p>
                              Uploaded By: <a href="?student=<?php echo $info->StudentId;?>"><?php echo $info->FullName; ?></a> |
                              Date: <?php
                              $date = new dateTime($info->RegDate);
				                      $date = date_format($date, 'M j, Y ');
                              echo $date; ?> |
                              Author: <a href="?author=<?php echo $info->AuthorId;?>"><?php echo $info->AuthorName; ?></a>
                            </p>
                          </div>
                          <p>
                            <?php
                            echo strlen($info->book_description) > 245
                            ? substr($info->book_description, 0, 244).'...'
                            :$info->book_description;
                            ?>
                          </p>

                      </div>
                    </div>

                    <div class="col-sm-2">
                      <div class="book-list-exchange-button">
                        <div><a type="button" class="btn btn-primary" href="book.php?id=<?php echo $info->id; ?>">Exchange</a></div>
                      </div>
                    </div>

                  </div><!--row-->

                  <?php
                }
              }
              else if(book_tags() and !isset($_GET['author']) and !isset($_GET['student']))
              {

                $tag = isset($_GET['tag'])?$_GET['tag']:'';
                echo "<span style='margin-left:40px;'>Tag <button class='btn btn-secondary'>$tag</button></span>";
                foreach (book_tags() as $info)
                {
                  ?>
                  <div class="row" style="margin:15px 0 15px 0;">
                    <div class="col-sm-2">
                      <div class="book-list-image">
                        <?php
                        if ($info->book_image == null)
                        {
                          echo "<img src='assets/img/book.jpg'>";
                        }
                        else
                        {
                          echo "<img src='assets/img/".$info->book_image."'>";
                        }
                         ?>
                      </div>
                    </div>

                    <div class="col-sm-8">
                      <div class="book-list-description">
                          <h4><a href="book.php?id=<?php echo $info->id; ?>">
                            <?php echo ucfirst($info->BookName); ?>
                          </a></h4>
                          <div class="book-and-student-info">
                            <p>
                              Uploaded By: <a href="?student=<?php echo $info->StudentId;?>"><?php echo $info->FullName; ?></a> |
                              Date: <?php
                              $date = new dateTime($info->RegDate);
				                      $date = date_format($date, 'M j, Y ');
                              echo $date;
                               ?> |
                              Author: <a href="?author=<?php echo $info->AuthorId;?>"><?php echo $info->AuthorName; ?></a>
                            </p>
                          </div>
                          <p>
                            <?php
                            echo strlen($info->book_description) > 245
                            ? substr($info->book_description, 0, 244).'...'
                            :$info->book_description;
                            ?>
                          </p>

                      </div>
                    </div>

                    <div class="col-sm-2">
                      <div class="book-list-exchange-button">
                        <div><a type="button" class="btn btn-primary" href="book.php?id=<?php echo $info->id; ?>">Exchange</a></div>
                      </div>
                    </div>

                  </div><!--row-->

                  <?php
                }
              }
              else
              {
                ?>
                <div class="alert alert-danger">
                  No book found
                </div>
                <?php
              }
               ?>

          </div><!--books-list-->
        </div><!--books-container-->

        <!-- <ul class="pagination">
          <li>
            <a href="#">1</a>
          </li>
          <li class="active">
            <a href="#">2</a>
          </li>
          <li>
            <a href="#">3</a>
          </li>
          <li>
            <a href="#">4</a>
          </li>
          <li>
            <a href="#">5</a>
          </li>
        </ul> -->


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
