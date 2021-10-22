
<?php error_reporting(0);?>
<script src="assets/js/jquery.js"></script>
<div class="navbar navbar-inverse set-radius-zero" >
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" >
                    <img src="assets/img/logo.png" />
                </a>

            </div>

        <?php
        if(isset($_SESSION['login']))
        {

          $count = 0;
          $email = $_SESSION['login'];

          $sql = "SELECT * from messages left join reply on messages.toEmail = reply.fromEmail where (messages.toEmail = '$email' and  messages.status = 0) or (reply.toEmail = '$email' and  reply.status = 0)";
          $query = $dbh->prepare($sql);
          if($query->execute())
          {
            $count =  $query->rowCount();
          }
          
          //notifications
          
          
          $count_notif = 0;
          $sid = $_SESSION['stdid'];
          

          $sql = "SELECT notifications.id, notifications.send_to, notifications.book_ref from notifications where notifications.send_to = :student and notifications.opened = 0";
          
          $query = $dbh->prepare($sql);
          $query->bindParam(':student',$sid);
          if($query->execute())
          {
            $count_notif =  $query->rowCount();
          }
          
          
        ?>
          <div class="right-div">
              <a href="logout.php" class="btn btn-danger pull-right">LOG ME OUT</a>
               
               
              <a class="icon" href="notifications.php">
                <i class="fa fa-bell-o <?php if($count_notif){echo 'message_counter';}?>"></i>
              </a>
                 
               
              <a href="messages.php" class="icon">
                <i class="fa fa-envelope-o <?php if($count){echo 'message_counter';}?>">
                </i>
              </a>
              
          </div>
        <?php
        }?>

        </div>
    </div>
    <!-- LOGO HEADER END-->
<?php if(isset($_SESSION['login']))
{
?>
<section class="menu-section">
        <div class="container">
            <div class="row ">
                <div class="col-md-12">
                    <div class="navbar-collapse collapse ">
                        <ul id="menu-top" class="nav navbar-nav navbar-right">
                            <li><a href="dashboard.php" class="menu-top-active">DASHBOARD</a></li>


                            <li>
                                <a href="#" class="dropdown-toggle" id="ddlmenuItem" data-toggle="dropdown"> Account <i class="fa fa-angle-down"></i></a>
                                <ul class="dropdown-menu" role="menu" aria-labelledby="ddlmenuItem">
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="my-profile.php">My Profile</a></li>
                                     <li role="presentation"><a role="menuitem" tabindex="-1" href="change-password.php">Change Password</a></li>
                                </ul>
                            </li>
							
							
							<li role="presentation"><a role="menuitem" tabindex="-1" href="issued-books.php">Exchanged Books</a></li>
                            
                            <script type="text/javascript">
                              $(document).ready(function(){
                                function checkCount()
                                {
                                  $.ajax({
                                    url:'fetchMessages.php',
                                    type:'post',
                                    data:{count:1},
                                    success:function(count){
                                      if(count != 0)
                                      {
                                        $('.fa-envelope-o').addClass('message_counter');
                                        $(".notification").fadeIn('slow').show();
                                      }
                                    }
                                  });
                                  setTimeout(function(){
                                    $(".notification").fadeOut('slow').hide();
                                  }, 5000);
                                }
                                setInterval(function(){
                                  checkCount();
                                }, 7000);
                              });
                            </script>
                            
                            <li>
                                <a href="#" class="dropdown-toggle" id="ddlmenuItem" data-toggle="dropdown"> Books <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu" role="menu" aria-labelledby="ddlmenuItem">
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="add-book.php">Add Books</a></li>
                                    
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="manage-books.php">Manage Books</a></li>
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="add-tag.php">Add Tags</a></li>
                                    
									<li role="presentation"><a role="menuitem" tabindex="-1" href="manage-tags.php">Manage Tags</a></li>
									<li role="presentation"><a role="menuitem" tabindex="-1" href="add-category.php">Add Category</a></li>
									
									<li role="presentation"><a role="menuitem" tabindex="-1" href="manage-categories.php">Manage Category</a></li>
									
                                </ul>
                            </li>

                            <li>
                                <a href="#" class="dropdown-toggle" id="ddlmenuItem" data-toggle="dropdown">  Authors <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu" role="menu" aria-labelledby="ddlmenuItem">
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="add-author.php">Add Author</a></li>
                                     <li role="presentation"><a role="menuitem" tabindex="-1" href="manage-authors.php">Manage Authors</a>
                                     </li>
                                </ul>
                            </li>

                            <li>
                              <a href="browse-books.php">Browse Books</a>
                            </li>

                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <?php } else { ?>
        <section class="menu-section">
        <div class="container">
            <div class="row ">
                <div class="col-md-12">
                    <div class="navbar-collapse collapse ">
                        <ul id="menu-top" class="nav navbar-nav navbar-right">

                             <li><a href="adminlogin.php">admin Login</a></li>
                             <li><a href="signup.php">User Signup</a></li>
                             <li><a href="index.php">User Login</a></li>


                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <?php } ?>
    <a href="messages.php" style="color:#222;">
      <div class="notification">

          <div class="notification-title">
            <strong><i class="fa fa-bell-o"></i> New message</strong>
          </div>
          <div class="notification-body">
            You have new unread message!
          </div>


      </div>
    </a>
