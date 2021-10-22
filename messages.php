<?php
session_start();
error_reporting(E_ALL);
include('includes/config.php');

if(!isset($_SESSION['login']) and !isset($_SESSION['alogin']))
{
  header('location:index.php');
}

$sql = "update messages set messages.status=1 where messages.toEmail = :email";
$query = $dbh->prepare($sql);
$query->bindparam(':email',$_SESSION['login'],PDO::PARAM_STR);
$query->execute();

$sql = "update reply set reply.status=1 where reply.fromEmail = :email";
$query = $dbh->prepare($sql);
$query->bindparam(':email', $_SESSION['login'], PDO::PARAM_STR);
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


function messages()
{
  global $dbh;
  $email = $_SESSION['login'];

  $sql = "SELECT messages.id, messages.fromEmail, messages.toEmail, messages.name, messages.phone, messages.message, messages.book_ref, messages.added_at from messages where messages.toEmail = :email OR messages.fromEmail = :another ORDER by messages.id DESC";

  $query = $dbh->prepare($sql);
  $query->bindparam(':email', $email, PDO::PARAM_STR);
  $query->bindparam(':another', $email, PDO::PARAM_STR);
  $query->execute();
  if($query->rowCount())
  {
    return $query->fetchAll(PDO::FETCH_OBJ);
  }
  return false;
}

function replies($id)
{
  global $dbh;
  $sql = "SELECT reply.fromEmail, reply.toEmail, reply.name, reply.phone, reply.message, reply.added_at from reply where reply.message_id = :id ORDER by reply.id DESC";

  $query = $dbh->prepare($sql);
  $query->bindparam(':id',$id, PDO::PARAM_STR);
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
  $sql = "select * from tblbooks where id = :id";

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
              <h4 class="header-line">Messages (<span id='messages-counter'><?php echo messages()?count(messages()):0; ?></span>)
              </h4>
          </div>
        </div>
        <input type="hidden" id="focused" value="0">
        <div class="chat-box">

        <?php
        if(messages())
        {
          foreach (messages() as $message)
          {
            ?>
            <div class="panel message-box">
              <?php
              if($message->fromEmail !=  $_SESSION['login'])
              {
                $isbn = book_ref($message->book_ref)
                ? book_ref($message->book_ref)->ISBNNumber
                : '';
                $student = student($message->fromEmail)
                ?student($message->fromEmail)->StudentId
                :'';

              ?>
              <span class='ref_button'>
                <a class="btn btn-primary pull-right btn-sm" type="button" href="issue-book.php?student=<?php echo $student;?>&book_ref=<?php echo $isbn;?>">
                  Issue Book
                </a>
              </span>

                <div class='clearfix'></div>
                <div class="book-ref">
                  Book Ref ISBNNumber#
                  <strong>
                    <span class='book_isbn'>
                      <?php
                      echo book_ref($message->book_ref)?ucfirst(book_ref($message->book_ref)->ISBNNumber):'';
                      ?>
                    </span>
                   <br>
                 </strong>
                   Book Name:
                   <strong>
                     <span class='book_name'>
                       <?php echo book_ref($message->book_ref)? book_ref($message->book_ref)->BookName:''; ?>
                     </strong>
                    </span>
                   <br>
                   UserID #
                   <strong>
                   <span class='book_student'>
                     <?php echo student()?student($message->fromEmail)->StudentId:''; ?>
                  </span>
                  </strong>
                   <br>
                   User Phone #

                   <strong><span class='book_phone'><?php echo $message->phone; ?></strong></span><br>
                   User Email:
                   <strong><span class='book_fromEmail'><?php echo $message->fromEmail; ?></span></strong>
                   <br><hr>
                </div>
              <?php
              }
              ?>
            <span class='message-skeleton' id="message-<?php echo $message->id;?>">
              <div class="message-name">
                <?php echo $message->name;?>
              </div>

              <div class="message-body">
                <?php echo nl2br($message->message);?>
              </div>
              <div class='message-time'>
                <?php

                $date = new dateTime($message->added_at);
				        $date = date_format($date, 'M j h:i A');
                echo $date;
                ?>
              </div>
            </span>

              <div id="reply-link"></div>

              <div class="reply-link">
                <a href="#">Reply</a>
              </div>

              <div class='replies-counter'>Replies
                (<span id="replies-counter"><?php echo replies($message->id) !=false
                  ?count(replies($message->id))
                  :0;?></span>)
              </div>

              <div class="reply-box">

                <textarea name="reply" placeholder="Reply..." class="form-control reply-text"></textarea>
                <input type="hidden" name="id" class="message_id" value="<?php echo $message->id; ?>">
                <input type="hidden" name="name" class="message_name" value="<?php echo student()->FullName; ?>">
                <small><span class="help">Type and hit enter</span></small>

              </div>

              <div class="replies-box">

                <?php
                if (replies($message->id))
                {
                  foreach (replies($message->id) as $reply)
                  {
                    ?>
                    <div class="reply-name">
                      <span><?php echo $reply->name;?></span>
                    </div>

                    <div class="reply-body">
                      <span><?php echo nl2br($reply->message);?></span>

                    </div>

                    <div class='message-time'>
                      <?php

                      $date = new dateTime($reply->added_at);
      				        $date = date_format($date, 'M j h:i A');
                      echo $date;
                      ?>
                    </div>

                    <?php
                  }
                }
                ?>
            </div><!--replies-box-->

          </div><!--message-box-->
            <?php
          }
        }// if messages()
        else
        {
          ?>
          <div class="alert alert-danger">
            No new messages
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
    <script type="text/javascript">
      $(document).ready(function(){
        // $(".reply-link").on('click', 'a', function(){
        //   event.preventDefault();
        //   var t = $(this);
        //   t.parent().siblings('.reply-box').show().find('textarea').focus();
        // });

      });
    </script>

    <script type="text/javascript">
      $(document).ready(function(){

        var f = $("#focused");

        $(document).on("focus",".reply-text", function(){
          f.val(1);
        });
        $(document).on("blur",".reply-text", function(){
          f.val(0);
        });

        console.log(f);

        var errors = {};

        $("textarea").on('focus', function(){
          $(this).css('border','1px solid #ccc').siblings('span').html('');
        });

        $(".reply-link").find('a').on('click', function(){
          event.preventDefault();
          var t = $(this);
          t.parent().siblings('.reply-box').show().find('textarea').focus();
        });

        $(".reply-text").on('keyup', function(e){

          var key = e.which;
          var value = $.trim($(this).val());
          var id = $(this).siblings('.message_id').val();
          var name = $(this).siblings('.message_name').val();

          if (key == 13) {
            if(value == ''){
              $(this).css('border','1px solid #A94442');
              $(this).blur();
              return false;
            }
            else{
              $.ajax({
                url:'message.php',
                type:'post',
                data:{
                  message:value,
                  id:id,
                  name:name
                },
                success:function(res){
                  //uncommented because it is not useful :)
                    // var d = new Date().toString('hh:mm tt');
                    // //var d = moment().format('hh:mm a');
                    // $('.reply-text').parent().siblings('.replies-box').prepend("<div class='reply-name'>"+name+"</div><div class='reply-body'>"+value+"</div><div class='message-time'>"+d+"</div>");
                    // $(".reply-text").val('');
                    // var replies = $("#replies-counter").html();
                    // var i=1;
					          // replies=parseInt(replies)+parseInt(i);
                    // $("#replies-counter").html(replies);
                    fetch();
                },
                error:function(){
                  alert('Something went wrong! Please try again');
                }

              });
            }
          }
        });//.reply-text

        function fetch()
        {
          var val = 1;
          var email = '<?php echo $_SESSION['login'];?>'
          try
          {
            $.ajax({
              url:'fetchMessages.php',
              type:'post',
              data:{fetch:val, email:email},
              success:function(res){
                $(".message-box").remove();
                $(".chat-box").html(res);

                $(".reply-link").on('click', 'a', function(){
                  event.preventDefault();
                  var t = $(this);
                  t.parent().siblings('.reply-box').show().find('textarea').focus();
                });

                $(".reply-text").on('keyup', function(e){

                  var key = e.which;
                  var value = $.trim($(this).val());
                  var id = $(this).siblings('.message_id').val();
                  var name = $(this).siblings('.message_name').val();

                  if (key == 13) {
                    if(value == ''){
                      $(this).css('border','1px solid #A94442');
                      $(this).blur();
                      return false;
                    }
                    else{
                      $.ajax({
                        url:'message.php',
                        type:'post',
                        data:{
                          message:value,
                          id:id,
                          name:name
                        },
                        success:function(res){
                            fetch();
                        },
                        error:function(){
                          alert('Something went wrong! Please try again');
                        }

                      });
                    }
                  }
                });//.reply-text

              }
            });
          }//try

          catch(e)
          {
            alert(e);
          }//catch
        }

        try{$(".fa-envelope-o").removeClass('message_counter');}
        catch(e){alert(e);}

        // if(f.val() == 0)
        // {
        //   setInterval(function(){
        //     fetch();
        //   }, 7000);
        // }

      });
    </script>
</body>
</html>
