<?php
session_start();
error_reporting(E_ALL);

include('includes/config.php');


if(isset($_GET['owner']) and isset($_GET['book']))
{
    $owner = $_GET['owner'];
    $book  = $_GET['book'];
    
    //updating return status to 1 
    
    $sql = "Update tblissuedbookdetails set RetrunStatus = 1 where StudentId_owner = :owner and BookId = :book";
    $query = $dbh->prepare($sql);
    
    $query->bindParam(':owner',$owner);
    $query->bindParam(':book',$book);
    
    if($query->execute())
    {
        //updating availability to 1
        
        $sql = "Update tblbooks set availability = 1 where id = :book";
        $query = $dbh->prepare($sql);

        $query->bindParam(':book',$book);
        $query->execute();
        
        //setting up notification for owner
        $book_ref = book_ref($book)->ISBNNumber;
        $sql = "insert into notifications (send_to, book_ref) values (:owner, :book)";
        
        $query = $dbh->prepare($sql);
    
        $query->bindParam(':owner',$owner);
        $query->bindParam(':book',$book_ref);
        
        $query->execute();
        
        header("location:issued-books.php?return_book=1");
        exit;
    }
    
    
}


function book_ref($id)
{
  global $dbh;
  $sql = "select ISBNNumber from tblbooks where id = :id";

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
