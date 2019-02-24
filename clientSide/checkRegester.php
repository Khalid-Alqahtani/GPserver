<?php

session_start();
include 'connection.php';
 echo $useeMail= $_POST["email"];
 echo $userName= $_POST["username"];
 echo $firstname= $_POST["first-name"];
 echo $lastname= $_POST["last-name"];
 echo $pass1= $_POST["pass1"];
 echo $pass2= $_POST["pass2"];


$query = "SELECT * FROM Account where UserName='$userName' || email='$useeMail' ";
$result= mysqli_query($conn,$query);



if (mysqli_num_rows($result) >0  ){
$row=mysqli_fetch_assoc($result);
if( $useeMail==$row['email'] ){
$_SESSION['usedUserMail']= "This Email is already used" ;
}else{
  $_SESSION['usedUserMail']=null;
}

if(   $userName==$row['UserName'] ){
  $_SESSION['usedUserName']="This username is already used  ";
}else{
  $_SESSION['usedUserName']=null;
}if($firstname==""  ){
  $_SESSION['NoFairstName']="You did not enter first name";
}else{
  $_SESSION['NoFairstName']=null;

}if($lastname==""  ){
  $_SESSION['NolastName']="You did not enter first name";
}else{
  $_SESSION['NolastName']=null;

}
if($pass1==$pass2){
    $_SESSION['passWrong']=null;
}
header("location:reg/regester.php");


}else{
  $_SESSION['usedUserMail']=null;
  $_SESSION['usedUserName']=null;



}

if($pass1==$pass2){
  $query = "INSERT INTO Account  (UserName,email,Pass,Fname,Lname) VALUES ('$userName', '$useeMail', '$pass1', '$firstname','$lastname'  ) ";
  $result= mysqli_query($conn,$query);
  header("location:login.php");





}else{
  $_SESSION['passWrong']="password does not match ";
  header("location:reg/regester.php");

}




/*




if (mysqli_num_rows($result) >0  ){
if(($user==$row['UserName'] ||$user==$row['email']) && $pass==$row['Pass']){
echo "Sucsess";
session_unset();
session_destroy();



die();
}


}
//wrong pass or user

 $_SESSION['wrongpass']='user name or password not correct';
header("location:login.php");


*/

 ?>
