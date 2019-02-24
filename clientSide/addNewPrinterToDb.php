<?php

session_start();
include '../connection.php';




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
    <link rel="stylesheet" href="vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="vendors/themify-icons/css/themify-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/selectFX/css/cs-skin-elastic.css">
    <link rel="stylesheet" href="vendors/jqvmap/dist/jqvmap.min.css">


    <link rel="stylesheet" href="assets/css/style.css">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>

</head>

<body>


    <!-- Left Panel -->

    <aside id="left-panel" class="left-panel">
        <nav class="navbar navbar-expand-sm navbar-default">

            <div class="navbar-header">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="./"><img src="images/logo.png" alt="Logo"></a>
                <a class="navbar-brand hidden" href="./"><img src="images/logo2.png" alt="Logo"></a>
            </div>

            <div id="main-menu" class="main-menu collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="active">
                        <a href="index.html"> <i class="menu-icon fa fa-user"></i>My Account </a>
                        <a href="index.html"> <i class="menu-icon fa fa-bolt"></i>Activation New Printer </a>
                        <a href="index.html"> <i class="menu-icon fa fa-money"></i>Rentprinter  </a>
                        <a href="index.html"> <i class="menu-icon fa fa-file-text-o"></i>Upload Gcode File  </a>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </nav>
    </aside><!-- /#left-panel -->

    <!-- Left Panel -->

    <!-- Right Panel -->

    <div id="right-panel" class="right-panel">

        <!-- Header-->
        <header id="header" class="header">

            <div class="header-menu">

                <div class="col-sm-7">
                    <a id="menuToggle" class="menutoggle pull-left"><i class="fa fa fa-tasks"></i></a>
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
                    <div class="user-area dropdown float-right">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="user-avatar rounded-circle" src="images/admin.jpg" alt="User Avatar">
                        </a>

                        <div class="user-menu dropdown-menu">
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

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1><?php echo "Welcome ". $_SESSION['username'] . " ID:" . $_SESSION['uderId']?> </h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li class="active">Dashboard</li>
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



<form action="addNewPrinterToDb.php" method="post">






<?php
//generate activation number 

if(isset($_POST["Generate"])){
  $result=false;
  while(!$result){
  //while loop until generate number and insert it to db     
  echo $key = md5(microtime().rand());
  echo "<br>";


  $query = "INSERT INTO Printer () VALUES () ";
  $result= mysqli_query($conn,$query);


 $lastprinterID= $conn->insert_id; //get last id added 

  $query = "INSERT INTO Activation (printer,code) VALUES ('$lastprinterID','$key') ";
  $result= mysqli_query($conn,$query);
  $conn->insert_id;
  }
 

}





?>



                          <div class="col-lg-12">
                              <button type="submit" name='Generate'class="btn btn-success col-sm-12 "><i class="fa fa-check fa-lg"></i>&nbsp; Generate new printer with activation code </button>
                          </div>

</form>

                        </div>
                </div>
            </div>
            <br>


                <div class="card">
                    <div class="card-header">
                    <i class="fa fa-print"></i><strong class="card-title pl-2">Printers Controling</strong>
                    </div>

                    <div class="card-body card-block">
                        <form action="" method="post" >







                            <div class="form-group row ml-1">
                                <input type="checkbox" class="mt-2">
                                <label for="" class="px-2 form-control-label mt-1">Bed Temperature :</label>
                                <div class="input-group col">
                                    <div class="input-group-prepend ">
                                        <span class="input-group-text ml-3" id=""><i class="fa fa-thermometer-three-quarters fa-fw" aria-hidden="true"></i></span>
                                    </div>
                                        <input type="number" class="form-control col-sm-1" max="100" min="0" maxlength="1" id="" placeholder="0-100" >
                                </div>
                            </div>

                            <div class="form-group row ml-1">
                                <input type="checkbox" class="mt-2">
                                <label for="" class="px-2 form-control-label mt-1">Head Temperature :</label>
                                <div class="input-group col">
                                    <div class="input-group-prepend ">
                                        <span class="input-group-text ml-2" id=""><i class="fa fa-thermometer-three-quarters fa-fw" aria-hidden="true"></i></span>
                                    </div>
                                        <input type="number" class="form-control col-sm-1" max="100" min="0" maxlength="3" id="" placeholder="0-100" >
                                </div>
                            </div>


                            <div class="container">
                                    <div class="ml-3">
                                        <button type="button" class=" btn btn-primary ml-5"><i class="px-2 fa fa-download"></i>&nbsp; Set Temperature</button>
                                    </div>
                            </div>

                        </form>
                    </div>
                    <div class="container">
                        <div class="card-body card-block row ">
                            <span class="mr-3"><i class="menu-icon fa fa-file-text-o"></i></span>
                            <div class="row">
                                <div class="col col-md-4"><label for="select" class=" form-control-label">Select Gcode File</label></div>
                                <div class="col-12 col-md-7">
                                    <input type="file" accept=".gcode">
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="card-body card-block">
                        <input type="checkbox" style="margin-right: 5px"> <span> <i class="fa fa-calendar"></i>&nbsp; Schedule </span>
<br>


<br>
                        <form action="" method="post">
                            <div class="form-group row ">

                                <div class="col col-md-1">
                                <label for="exampleInputName2" class="  form-control-label">Date :</label>
                                </div>
                                <div class="col-12 col-md-2">
                                <input type="date" id="exampleInputName2" required="" class="form-control ">
                                </div>

                                <div class="col col-md-1">
                                <label for="exampleInputEmail2" class="px-2  form-control-label">Time :</label>
                                </div>

                                <div class="col-12 col-md-2">
                                <input type="time" id="exampleInputEmail2" required="" class="form-control">
                                </div>
                            </div>

                            <div class="col row" >
                                <div class="col-lg-4"></div>
                                <div class="col-lg-4">
                                    <button type="button" class="btn btn-success col-sm-9 "><i class="fa fa-check fa-lg"></i>&nbsp; Start Printing </button>
                                </div>
                            </div>

                            <div class="col row">
                                <div class="col-lg-4"></div>
                                <div class="col-lg-4">
                                <button type="button" class="btn btn-danger col-sm-9"><i class="fa fa-stop"></i>&nbsp; Stop Printiong</button>
                                </div>
                            </div>
                        </form>
                    </div>
<br>




            </div>
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
    </script>

</body>

</html>
