<?php
session_start();
error_reporting(E_ALL);
include('includes/config.php');
if(!isset($_SESSION['login']) and !isset($_SESSION['alogin']))
{
  header('location:index.php');
}
if(isset($_GET['id']))
{
  $id = filter_var(trim($_GET['id']), FILTER_SANITIZE_URL);
}
else
{
  header("location:browse-books.php");
}
function book()
{
  global $dbh, $id;
  $sql = "SELECT tblauthors.AuthorName as author, tblstudents.StudentId, tblstudents.MobileNumber as phone, tblstudents.FullName, tblstudents.EmailId as email, tblbooks.uploaded_by, tblbooks.BookName, tblbooks.id, tblbooks.book_description, tblbooks.BookPrice, tblbooks.availability, tblbooks.RegDate from tblbooks join tblstudents on tblstudents.StudentId = tblbooks.uploaded_by join tblauthors on tblauthors.id = tblbooks.AuthorId WHERE tblbooks.id = :id";
  $query = $dbh->prepare($sql);
  $query->bindparam(':id', $id, PDO::PARAM_STR);
  $query->execute();
  if($query->rowCount())
  {
    return $query->fetchObject();
  }
  return false;
}
function book_tags()
{
  global $dbh, $id;
  $sql = "SELECT tags.id, tags.name from tags join book_tags on book_tags.tag_id = tags.id AND book_tags.book_id = :id";
  $query = $dbh->prepare($sql);
  $query->bindparam(':id', $id, PDO::PARAM_STR);
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
              <h4 class="header-line">Book Details</h4>
          </div>
        </div>
        <div class="jumbotron">
          <div class="container">
            <center>
              <h3><?php echo ucfirst(book()->BookName); ?></h3>
              <p>Author: <?php echo book()->author; ?></p>
            </center>
          </div>
        </div>

        <div class="row">
          <div class="col-md-9">
            <div class="book-details">
              <p class="book-date">Posted on <?php echo book()->RegDate; ?> uploaded by
                <a href="student.php?id=<?php echo book()->StudentId; ?>">
                  <?php echo book()->FullName; ?>
                </a>
              </p>
              <p class="book-description">
                <?php echo book()->book_description; ?>
              </p>
            </div>
            <hr>
            <p>Tags:</p>
            <div class="book-tags">
            <?php
            if(book_tags())
            {
              foreach (book_tags() as $tag)
              {
                ?>
                  <a href="browse-books.php?tag=<?php echo $tag->name;?>&tagId=<?php echo $tag->id; ?>" type="button" class="btn bnt-secondary tag"><?php echo $tag->name; ?></a>
                <?php
              }
            }
            ?>
            </div>

          </div>

          <div class="col-md-3">
            Owner
            <div class="panel owner-box">
              Full Name: <?php echo book()->FullName; ?><br>
              Phone: <?php echo book()->phone; ?><br>
              Email: <?php echo book()->email; ?>

            </div>

            <h4 class="contact-heading">Contact Owner</h4>
            <div class="contact-box">
              <form id="contact_form" method="post" action="message.php">


                <span class="error"></span><div class="form-group">
                  <label for="contact_name" class="control-label">Name * </label>
                  <input type="text" id="contact_name" name="name" placeholder="Your Name" class="form-control">
                  <span class="error text-danger"></span>
                </div>
                <div class="form-group">
                  <label for="contact_email" class="control-label">Email * </label>
                  <input type="text" id="contact_email" name="email" placeholder="Your Email" class="form-control">
                  <span class="error text-danger"></span>
                </div>
                <div class="form-group">
                    <label for="contact_phone" class="control-label">Phone </label>
                  <input type="text" id="contact_phone" name="phone" placeholder="Your phone" class="form-control">
                  <span class="error text-danger"></span>
                </div>
                <div class="form-group">
                  <label for="contact_message" class="control-label">Message * </label>
                  <textarea name="message" class="form-control" id="contact_message" placeholder="Hi! I want to take this book on rent..."></textarea>
                  <span class="error text-danger"></span>
                </div>
                <input type="hidden" name="toEmail" value="<?php echo book()->email; ?>">
                <input type="hidden" name="book_ref" value="<?php echo book()->id; ?>">

                <div class="form-group">
                  <input type="submit" name="send" value="Send" class="btn btn-primary pull-right" id="submit">
                </div>
              </form>

            </div>
          </div>
        </div><!--row-->

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

    <script type="text/javascript">
      $(document).ready(function(){
        var errors = {};
        $("#contact_form").on('submit', function(){
          event.preventDefault();
          var method = $(this).attr('method');
          var action = $(this).attr('action');
          var data = {};
          var permission = true;

          $(this).find('[name]').each(function(){
            name = $(this).attr('name');
            value = $(this).val();
            data[name] = value;
          });
          if(data){
            //checking for errors and validations
            if (data.name == '') {
              errors['contact_name'] = 'requried';
              permission = false;
            }
            if(data.email == ''){
              errors['contact_email'] = 'requried';
              permission = false;
            }
            if(data.message == ''){
              errors['contact_message'] = 'requried';
              permission = false;
            }

            //sent request
            else if(permission == true)
            {
              $.ajax({
                url:action,
                type:method,
                data:data,
                success:function(res){
                  $("#contact_form").trigger('reset');
                  alert(res);
                },
                error:function(){
                  alert("Something is wrong, please try again!");
                }

              });
            }
          }

          $.each(errors, function(key, value){
            $("#"+key).css('border','1px solid #A94442').siblings('span').html(value);
          });

          errors = {};

          return false;
        });

        $("input[type='text'], textarea").on('focus', function(){
          $(this).css('border','1px solid #ccc').siblings('span').html('');
        });

      });
    </script>
</body>
</html>
