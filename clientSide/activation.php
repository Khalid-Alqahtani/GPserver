



<?php
session_start();
include '../connection.php';

if($_SESSION['username'] == "" ){
header("location:../login.php");
}


?>







<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en">
<!--<![endif]-->

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Sufee Admin - HTML5 Admin Template</title>
    <meta name="description" content="Sufee Admin - HTML5 Admin Template">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" href="apple-icon.png">
    <link rel="shortcut icon" href="favicon.ico">

    <link rel="stylesheet" href="vendors/bootstrap/dist/css/bootstrap.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="vendors/font-awesome/css/font-awesome.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="vendors/themify-icons/css/themify-icons.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="vendors/selectFX/css/cs-skin-elastic.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="vendors/jqvmap/dist/jqvmap.min.css">
    <!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assets/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assets/fonts/iconic/css/material-design-iconic-font.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="assets/css/style.css">
    <!--===============================================================================================-->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>
    <!--===============================================================================================-->
   

</head>

<body >


    <!-- Left Panel -->

    <aside id="left-panel" class="left-panel bg1" style="background-image: url('images/bg-01.jpg');">
        <nav class="navbar navbar-expand-sm navbar-default ">

            <div class="navbar-header">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="index.html"><img src="images/logo.png" alt="Logo"></a>
                <a class="navbar-brand hidden" href="index.html"><img src="images/logo2.png" alt="Logo"></a>
            </div>

            <div id="main-menu" class="main-menu collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="active">
                        <a href="index.php"> <i class="menu-icon fa fa-user"></i>My Account </a>
                        <a href="activation.php"> <i class="menu-icon fa fa-bolt"></i>Activation New Printer </a>
                        <a href="index.html"> <i class="menu-icon fa fa-money"></i>Rentprinter  </a>
                        <a href="index.html"> <i class="menu-icon fa fa-file-text-o"></i>Upload Gcode File  </a>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </nav>
    </aside><!-- /#left-panel -->

    <!-- Left Panel -->

    <!-- Right Panel -->

    <div id="right-panel limiter container-login100" class="right-panel" >

        <!-- Header-->

        <header id="header" class="header" style="background: #9659ff">

            <div class="header-menu">

                <div class="col-sm-7">
                    <a id="menuToggle" class="menutoggle pull-left"><i class="fa fa fa-tasks "></i></a>
                    <div class="header-left">



                        <div class="dropdown for-notification">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="notification" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bell"></i>
                                <span class="count bg-danger">5</span>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="notification">
                                <p class="red">You have 3 Notification</p>
                                <a class="dropdown-item media bg-flat-color-1" href="#">
                                <i class="fa fa-check"></i>
                                <p>Server #1 overloaded.</p>
                            </a>
                                <a class="dropdown-item media bg-flat-color-4" href="#">
                                <i class="fa fa-info"></i>
                                <p>Server #2 overloaded.</p>
                            </a>
                                <a class="dropdown-item media bg-flat-color-5" href="#">
                                <i class="fa fa-warning"></i>
                                <p>Server #3 overloaded.</p>
                            </a>
                            </div>
                        </div>


                    </div>
                </div>

                <div class="col-sm-5">
                    <div class="user-area dropdown float-right" >
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="user-avatar rounded-circle" src="images/admin.jpg" alt="User Avatar" style="width: 38px; height: 38px;">
                        </a>

                        <div class="user-menu dropdown-menu" style="border-color: black;">
                            <a class="nav-link" href="#"><i class="fa fa-user"></i> My Profile</a>

                            <a class="nav-link" href="#"><i class="fa fa-user"></i> Notifications <span class="count">13</span></a>

                            <a class="nav-link" href="#"><i class="fa fa-cog"></i> Settings</a>

                            <a class="nav-link" href="#"><i class="fa fa-power-off"></i> Logout</a>
                        </div>
                    </div>



                </div>
            </div>

        </header><!-- /header -->
        <!-- Header-->

        <div class="breadcrumbs" >
            <div class="col-sm-4">
                <div class="page-header float-left" >
                    <div class="page-title">
                        <h1>Dashboard</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8" >
                <div class="page-header float-right" >
                    <div class="page-title" >
                        <ol class="breadcrumb text-right" >
                            <li class="active" >Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>



        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-print"></i><strong class="card-title pl-2">Printers activeatioon</strong>
                </div>



                    <div class="card-body">
                        <div class="mx- d-block">



<form action="activation.php" method="post">

                          <div class="col col-md-12">
                          <label for="exampleInputEmail2" class="px-2  form-control-label">Enter activation code  :</label>
                          </div>




<?php


if(isset($_POST["activeNumber"])){
    //retrive activation number form db 
  $query = "SELECT * FROM Activation where code='".$_POST["activeNumber"]."'";
  $result= mysqli_query($conn,$query);
  $code=$_POST["activeNumber"]; //entered by user 
 

  if( mysqli_num_rows($result)>=1 ){
    //exsist number

$row=mysqli_fetch_assoc($result);
 $printerID=$row['printer'];
if($row['state']=='DONE'){
//already used
echo"
  <div class='col-lg-12'>
  <input type='text' id='exampleInputEmail2'   name='activeNumber' class='form-control is-valid
  '>


    <br>
      <div class='alert alert-primary py-2' role='alert'>
        This '  $code  ' already active !
      </div>




  </div> ";



}else{
    // correct number

$query = "UPDATE Activation SET state='DONE'   where code='$code' ";
$result= mysqli_query($conn,$query);

$query = "UPDATE Printer SET printerOwned='".$_SESSION['uderId']."'   where ID='$printerID'";
$result= mysqli_query($conn,$query);

echo"
  <div class='col-lg-12'>
  <input type='text' id='exampleInputEmail2'   name='activeNumber' class='form-control is-valid
  '>


    <br>
      <div class='alert alert-success py-2' role='alert'>
        You are successfuly active your printer
      </div>




  </div> ";

die();



}



}elseif($code==""){
    //nothing input 
  echo"
  <div class='col-lg-12'>
  <input type='text' id='exampleInputEmail2'  name='activeNumber' class='form-control
  '>
<br>
  <div class='alert alert-secondary py-2' role='alert'>
    You did not enter any activation code
  </div>

  </div> ";

}else{
    //not found in db 
  echo"
  <div class='col-lg-12'>
  <input type='text' id='exampleInputEmail2'   name='activeNumber' class='form-control is-invalid
  '>


  <br>
    <div class='alert alert-danger py-2' role='alert'>
      This '  $code  '  activation code not correct !
    </div>



  </div> ";



}}else{
    
  echo"
  <div class='col-lg-12'>
  <input type='text' id='exampleInputEmail2'   name='activeNumber' class='form-control
  '>

  </div> ";



}


?>



                          <div class="col-lg-12 py-5">
                              <button type="submit" class="btn btn-success col-sm-12 "><i class="fa fa-check fa-lg"></i>&nbsp; Check </button>
                          </div>

</form>

                        </div>
                </div>
            </div>
            <br>



        </div>












        <div class="content mt-3">


                <!-- /# card -->
            </div>


        </div> <!-- .content -->
<!-- /#right-panel -->

    <!-- Right Panel -->

    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <script src="vendors/popper.js/dist/umd/popper.min.js"></script>
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="assets/js/main.js"></script>


    <script src="vendors/chart.js/dist/Chart.bundle.min.js"></script>
    <script src="assets/js/dashboard.js"></script>
    <script src="assets/js/widgets.js"></script>
    <script src="vendors/jqvmap/dist/jquery.vmap.min.js"></script>
    <script src="vendors/jqvmap/examples/js/jquery.vmap.sampledata.js"></script>
    <script src="vendors/jqvmap/dist/maps/jquery.vmap.world.js"></script>
    <script>
        (function($) {
            "use strict";

            jQuery('#vmap').vectorMap({
                map: 'world_en',
                backgroundColor: null,
                color: '#ffffff',
                hoverOpacity: 0.7,
                selectedColor: '#1de9b6',
                enableZoom: true,
                showTooltip: true,
                values: sample_data,
                scaleColors: ['#1de9b6', '#03a9f5'],
                normalizeFunction: 'polynomial'
            });
        })(jQuery);

        function ScheduleArea(){
            var checkBox = document.getElementById("scheduling-checkbox");
            var schedulingArea = document.getElementById("scheduling-form");
            if(checkBox.checked == true){
                schedulingArea.style.display = "block";
            } else {
                schedulingArea.style.display = "none";

            }
        }

        function toggleCheck(){
            var trigger = document.getElementById("printer-check");
            var icon = document.getElementById("printer-check-icon");
            if(trigger.checked == true){
                icon.classList.add("fa-check-square-o");
                icon.classList.remove("fa-square-o");
            } else {
                icon.classList.remove("fa-check-square-o");
                icon.classList.add("fa-square-o");

            }
        }

        function bedTempCheckbox(){

            var headTrigger = document.getElementById("head-temp");
            var headValueBox = document.getElementById("head-temp-value");
            var headValue = document.getElementById("head-temp-number");

            var bedTrigger = document.getElementById("bed-temp");
            var bedValueBox = document.getElementById("bed-temp-value");
            var bedValue = document.getElementById("bed-temp-number");

            if(bedTrigger.checked == true){
                bedValueBox.style.display = " contents";
            } else {
                bedValueBox.style.display = "none";
                bedValue.value="";
            }

            if(headTrigger.checked == true){
                headValueBox.style.display = " contents";
            } else {
                headValueBox.style.display = "none";
                headValue.value="";
            }
        }

    </script>

</body>

</html>
