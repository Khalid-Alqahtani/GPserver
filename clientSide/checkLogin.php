<?php

session_start();
include 'connection.php';
$user= $_POST["name1"];
$pass= $_POST["pass"];




$query = "SELECT * FROM Account where UserName='$user' and Pass='$pass'";
$result= mysqli_query($conn,$query);

echo mysqli_num_rows($result);
if (mysqli_num_rows($result) >0  ){
  $row=mysqli_fetch_assoc($result);

if(($user==$row['UserName'] ||$user==$row['email']) && $pass==$row['Pass']){
session_unset();
//session_destroy();
$_SESSION['username']=$user;
 $_SESSION['uderId']=$row['ID'];

header("location:mainPage_alpha_v2/index.php");


die();
}


}
//wrong pass or user

 $_SESSION['wrongpass']='user name or password not correct';
header("location:login.php");




 ?>
