<?php
session_start();
error_reporting(E_ALL);

include('includes/config.php');
if(!isset($_SESSION['login']))
{
  header('location:index.php');
}

if(isset($_POST['fetch']))
{
  $email = $_POST['email'];
  $email = filter_var($email, FILTER_VALIDATE_EMAIL);

  $sql = "SELECT * from messages where messages.toEmail = :email or messages.FromEmail = :email ORDER by messages.id DESC";

  $query = $dbh->prepare($sql);
  $query->bindparam(':email', $email, PDO::PARAM_STR);
  $query->execute();

  if($query->rowCount())
  {
    $messages = array();
    $messages['messages'] = $query->fetchAll(PDO::FETCH_OBJ);
    $i = 0;
    foreach ($messages['messages'] as $message)
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
             StudentID #
             <strong>
             <span class='book_student'>
               <?php echo student()?student($message->fromEmail)->StudentId:''; ?>
            </span>
            </strong>
             <br>
             Studentphone #

             <strong><span class='book_phone'><?php echo $message->phone; ?></strong></span><br>
             Student Email:
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

    </div><!--messages-box-->
    <?php
      if(replies($message->id))
      {
        $messages['replies'][$i] = replies($message->id);
      }
      if($_SESSION['login'] == $message->toEmail)
      {
        $messages['book_ref'] = book_ref($message->book_ref);
        $messages['student'] = student()?student($message->fromEmail)->StudentId:'';
      }
      $i++;
    }
    //echo json_encode($messages);
    die;
  }

}

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

  $sql = "SELECT messages.id, messages.fromEmail, messages.toEmail, messages.name, messages.phone, messages.message, messages.book_ref, messages.added_at from messages where messages.toEmail = :email OR messages.fromEmail = :another ORDER by messages.added_at DESC";

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
  $sql = "SELECT reply.id, reply.fromEmail, reply.toEmail, reply.name, reply.phone, reply.message, reply.added_at from reply where reply.message_id = :id ORDER by reply.id DESC";

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

function checkCount()
{
  $count = 0;
  $email = $_SESSION['login'];
  global $dbh;
  $sql = "SELECT * from messages left join reply on messages.toEmail = reply.fromEmail where (messages.toEmail = '$email' and  messages.status = 0) or (reply.toEmail = '$email' and  reply.status = 0)";
  $query = $dbh->prepare($sql);
  if($query->execute())
  {
    $count =  $query->rowCount();
    echo $count;
  }
}

if(isset($_POST['count']))
{
  checkCount();
}

 ?>
