<?php
session_start();
error_reporting(E_ALL);
include('includes/config.php');
if(!isset($_SESSION['login']))
{
  header('location:index.php');
}

if(isset($_POST['send']))
{
  $name = $_POST['name'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];
  $message = trim(htmlspecialchars(htmlentities($_POST['message'])));
  $toEmail = $_POST['toEmail'];
  $book_ref = $_POST['book_ref'];
  $added_at = date('Y-m-d h:i:s');
  $status = 0;

  if(!filter_var($email, FILTER_VALIDATE_EMAIL))
  {
    echo "Invalid email";
    exit;
  }
  else if(!filter_var($toEmail, FILTER_VALIDATE_EMAIL)
          and !empty($toEmail)
          and !empty($toEmail)
  )
  {
    echo "Something went wrong, please try again!";
    exit;
  }
  else
  {
    $sql = "INSERT into messages (toEmail, fromEmail, name, phone, message, book_ref, added_at,status)
    VALUES (:toEmail, :fromEmail, :name, :phone, :message, :book_ref, :added_at, :status)";
    $query = $dbh->prepare($sql);
    $query->bindparam(':toEmail', $toEmail, PDO::PARAM_STR);
    $query->bindparam(':fromEmail', $email, PDO::PARAM_STR);
    $query->bindparam(':name', $name, PDO::PARAM_STR);
    $query->bindparam(':phone', $phone, PDO::PARAM_STR);
    $query->bindparam(':message', $message, PDO::PARAM_STR);
    $query->bindparam(':book_ref', $book_ref, PDO::PARAM_STR);
    $query->bindparam(':added_at', $added_at, PDO::PARAM_STR);
    $query->bindparam(':status', $status, PDO::PARAM_STR);
    if($query->execute())
    {
      echo "your request for book rent has been sent";
      //$id = $dbh->lastInsertId();
    }
  }
  exit;
}

if(isset($_POST['message']) and isset($_POST['id']))
{
  $message = trim(htmlspecialchars(htmlentities($_POST['message'])));
  $id = $_POST['id'];
  $name = $_POST['name'];

  $sql = "SELECT * from messages where id = :id";
  $query = $dbh->prepare($sql);
  $query->bindparam(':id', $id, PDO::PARAM_STR);
  $query->execute();
  if($query->rowCount())
  {
    $message_details = $query->fetchObject();
    $fromEmail = $message_details->toEmail;
    $toEmail = $message_details->fromEmail;
    $phone = $message_details->phone;
    $added_at = date('Y-m-d h:i:s');
    $status = 0;

    $sql = "INSERT INTO reply (message_id, fromEmail, toEmail, name, phone, message,  added_at, status)
    VALUES(:message_id, :fromEmail, :toEmail, :name, :phone, :message,  :added_at, :status)";

    $query = $dbh->prepare($sql);
    $query->bindparam(':message_id', $id, PDO::PARAM_STR);
    $query->bindparam(':fromEmail', $fromEmail, PDO::PARAM_STR);
    $query->bindparam(':toEmail', $toEmail, PDO::PARAM_STR);
    $query->bindparam(':name', $name, PDO::PARAM_STR);
    $query->bindparam(':phone', $phone, PDO::PARAM_STR);
    $query->bindparam(':message', $message, PDO::PARAM_STR);
    $query->bindparam(':added_at', $added_at, PDO::PARAM_STR);
    $query->bindparam(':status', $status, PDO::PARAM_STR);

    $query->execute();
    exit;
  }
}
