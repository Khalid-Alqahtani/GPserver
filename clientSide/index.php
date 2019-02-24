



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
    <script type = "text/javascript">



    var PrintersIDs=[]; // all printer selected by user to do action
    var i=0;

    function PrinterSlected(ele) {
      var state = document.getElementById("ST"+ele).textContent;

      if(document.getElementById("SA"+ele).checked){
          //printer selected , SA+ele is the id for ptinter cheeckbox
        PrintersIDs.push(ele);
        if(state =="printing")
        document.getElementById("startPrint").disabled = true;
        //user must not be able to do strat printing when he select any printer have printing state
      }else{
          //user unselect printer , will remove it form arry
        for(var i=0 ; i<PrintersIDs.length ; i++)
          if(PrintersIDs[i]===ele){
            PrintersIDs.pop();
            if(state =="printing")
            document.getElementById("startPrint").disabled = false;
            //enable start printing button

    }
  }}



          var ws ;
          if ("WebSocket" in window) {
            if(ws == null ){
              ws = new WebSocket("ws://localhost:8080/",["user","2"]);
              alert("connected");
            }
          } else {
             // The browser doesn't support WebSocket
             alert("WebSocket NOT supported by your Browser!");
          }

           ws.onopen = function() {
           // will be use later
           };

          ws.onmessage = function (evt) {
              //recive json contain all printer information from server
             var received_msg = evt.data
             var data = JSON.parse(received_msg);
             var keys = Object.keys(data);//get all keys
             //alert(data["17"]["ID"]);
             alert("ff");
             var printerID=data["ID"];
             alert(printerID);
            // for(var x=0; x<keys.length ;x++){
            // var printerID=keys[x]; //json key represernt printer id

          if(data[printerID] == "offline"){
             //change card style if offline

            document.getElementById("TB"+printerID).classList.add("badge-dark");//bed tempruture
            document.getElementById("TH"+printerID).classList.add("badge-dark");// head temperature
            document.getElementById("ST"+printerID).classList.add("badge-dark");//state
            document.getElementById("RT"+printerID).classList.add("badge-dark");//remaining time
            document.getElementById("TP"+printerID).classList.add("badge-dark");//total printing time

            document.getElementById("SPAN"+printerID).classList.remove("fa-spin");

            document.getElementById("SL"+printerID).disabled = true; //live stream disable
            document.getElementById("SA"+printerID).disabled = true;//live cheekbox disable

            //set offline to all card
            document.getElementById("TB"+printerID).innerHTML=data[printerID];
            document.getElementById("TH"+printerID).innerHTML=data[printerID];
            document.getElementById("ST"+printerID).innerHTML=data[printerID];
            document.getElementById("RT"+printerID).innerHTML=data[printerID];
            document.getElementById("TP"+printerID).innerHTML=data[printerID];





               } else {
                 //printer online , set all information about printer
                 document.getElementById("SL"+printerID).disabled = false;
                 document.getElementById("SA"+printerID).disabled = false;


                 document.getElementById("TB"+printerID).innerHTML=data["headtemp"];
                 document.getElementById("TH"+printerID).innerHTML=data["bedtemp"];
                 document.getElementById("ST"+printerID).innerHTML=data["jobstate"];

                 document.getElementById("PR"+printerID).style.width = data["progresspersent"];
                 document.getElementById("PR"+printerID).innerHTML=data["progresspersent"];


                document.getElementById("RT"+printerID).innerHTML=data["remainingTime"];
                 document.getElementById("TP"+printerID).innerHTML=data["progresstimeleft"];

                 document.getElementById("HID"+printerID).innerHTML=data["jobname"];



             }


          };

          ws.onclose = function() {
             // websocket is closed.
             alert("Connection is closed...");
          };









          function setTemp(){



          if(document.getElementById("head-temp").checked)
          var head = document.getElementById("head-temp-number").value;


          if(document.getElementById("bed-temp").checked)
          var bed =  document.getElementById("bed-temp-number").value;


            var printer ;//selected
            for(var i=0 ; i<PrintersIDs.length ; i++){
            printer = PrintersIDs[i] ;

           if(bed==null || bed==""){
            bed=document.getElementById("TB"+printer).textContent;
            }

          if(head==null || head==""){
           head=document.getElementById("TH"+printer).textContent;
           }

            var jo = { "request": "set","ID":printer ,"type": "temp" , "typetemp":" head&bed" ,"HeadTemp":head,"BedTemp":bed };
            var a = JSON.stringify(jo); //send to server to change temp
            ws.send(a);

          }

            ws.onopen = function() {

            };

            ws.onmessage = function (evt) {
                //get response from server after cahnge temp and show it in card
               var received_msg = evt.data
               var a = JSON.parse(received_msg);
               var id =a["ID"]; // which printer got change
               document.getElementById("TB"+id).innerHTML=a["BedTemp"];
               document.getElementById("TH"+id).innerHTML=a["HeadTemp"];

            };
            ws.onclose = function() {
               // websocket is closed.
               alert("Connection is closed...");
            };
          }


              </script>

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

                            <a class="nav-link" href="../Logout.php"><i class="fa fa-power-off"></i> Logout</a>
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
                <div class="card-header ">
                    <i class="fa fa-print"></i><strong class="card-title pl-2">Printers Information</strong>
                </div>



                    <div class="card-body" >
                        <div class="mx- d-block">

                            <?php

$query = "SELECT * FROM Printer where printerOwned='".$_SESSION['uderId']."' ";
$result= mysqli_query($conn,$query);

if(mysqli_num_rows($result)>=1){


  while (  $row=mysqli_fetch_assoc($result)) {

  echo"
  <div class='col-md-4' >


                                  <aside class='profile-nav alt'>
                                      <section class='card'>";

                                      echo "<div class='card-header user-header alt bg-dark' style='background-image: url(\"images/bg-01.jpg\");'>";

                                      echo"
                                              <div class='media'>
                                                  <a href='#'>
                                                      <img class='align-self-center rounded-circle mr-3' style='width:85px; height:85px;' alt='' src='images/admin.jpg'>
                                                  </a>
                                                  <div class='media-body'>
                                                      <h2  class='text-light display-6 '>ID: ".$row['ID']."  </h2>
                                                      <p style='color:white;' id='HID".$row['ID']."' >JobName: </p>
                                                  </div>
                                              </div>
                                          </div>
                                          <ul class='list-group list-group-flush'>
                                              <li class='list-group-item'>
                                                  <a href='#'> <i class='fa fa-arrow-down'></i> Current Bed Temperature : <span id='TB".$row['ID']."' class='badge badge-primary pull-right'>connecting...</span></a>
                                              </li>
                                              <li class='list-group-item'>
                                                  <a href='#'> <i class='fa fa-arrow-up'></i> Current Head Temperature :  <span id='TH".$row['ID']."' class='badge badge-danger pull-right'>connecting...</span></a>
                                              </li>
                                              <li class='list-group-item'>
                                                  <a href='#'> <i class='fa fa-chain'></i> State :<span id='ST".$row['ID']."' class='badge badge-success pull-right'>connecting...</span></a>
                                              </li>
                                              <li class='list-group-item'>
                                                  <a href='#'> <i  id='SPAN".$row['ID']."'  class='fa fa-spinner fa-spin'></i> Progress :
                                                      <div class='progress mb-2'>
                                                          <div id='PR".$row['ID']."' class='progress-bar bg-danger progress-bar-striped progress-bar-animated' role='progressbar' style='width:0px ' aria-valuenow='90' aria-valuemin='0' aria-valuemax='100'> </div>
                                                      </div>
                                                  </a>
                                              </li>
                                              <li class='list-group-item'>
                                                  <a href='#'> <i class='fa fa-hourglass-half'></i> Remaining Time : <span id='RT".$row['ID']."' class='badge badge-info pull-right r-activity'>connecting...</span></a>
                                              </li>
                                              <li class='list-group-item'>
                                              <a href='#'> <i class='fa fa-clock-o'></i> Total Printing Time : <span id='TP".$row['ID']."'  class='badge badge-warning pull-right r-activity'>connecting...</span></a>
                                              </li>


                                              <li class='remove-li-dot'>
                                              <button id='SL".$row['ID']."' type='' class='btn btn-lg  btn-block ' disabled >
                                                  <div class='container-login100-form-btn' >
                                                      <div class='wrap-login100-form-btn' style='width: 100%; height: 50px;'>
                                                          <div class='login100-form-bgbtn '></div>
                                                          <i class='fa fa-video-camera fa-lg mt-3' style='color: aliceblue'></i>&nbsp;
                                                          <span id='payment-button-amount' style='color: aliceblue'>Show Live Stream</span>
                                                      </div>
                                                  </div>
                                              </button>
                                              </li>




                                              <li class='list-group-item'>
                                                  <a href='#'> <i id='printer-check-icon' class='fa fa-square-o'></i> Select printer to do action :<span class='badge       badge-success pull-right'></span></a>
                                                  <label class='switch switch-text switch-info'>
                                                      <input type='checkbox' class='switch-input'  onchange='PrinterSlected(".$row['ID'].")'   disabled id='SA".$row['ID']."'>
                                                      <span data-off='Off' data-on='on' class='switch-label'></span>
                                                      <span class='switch-handle'></span></label>
                                              </li>
                                          </ul>
                                      </section>
                                  </aside>
                              </div>
  ";
}







}









                             ?>



                        </div>
                </div>
            </div>
            <br>


                <div class="card" >
                    <div class="card-header" >
                    <i class="fa fa-print"></i><strong class="card-title pl-2">Printers Controling</strong>
                    </div>
                    <div class="card-body" >
                    <div class="card-body card-block">
                        <form action="" method="post" >






                            <div class="form-group row ml-1">
                                <input id="head-temp" type="checkbox" class="mt-2" onclick="bedTempCheckbox()">
                                <label for="" class="px-2 form-control-label mt-1">Head Temperature :</label>
                                <div id="head-temp-value" class="input-group col" style="display: none">
                                    <div class="input-group-prepend ">
                                        <span class="input-group-text ml-2" id=""><i class="fa fa-thermometer-three-quarters fa-fw" aria-hidden="true"></i></span>
                                    </div>
                                        <input id="head-temp-number" type="number" class="form-control col-sm-1" max="100" min="0" maxlength="3" placeholder="0-100" >
                                </div>
                            </div>

                            <div class="form-group row ml-1">
                                <input id="bed-temp" type="checkbox" class="mt-2" onclick="bedTempCheckbox()">
                                <label for="" class="px-2 form-control-label mt-1">Bed Temperature :</label>
                                <div id="bed-temp-value" class="input-group col" style="display: none">
                                    <div class="input-group-prepend ">
                                        <span class="input-group-text ml-3" id=""><i class="fa fa-thermometer-three-quarters fa-fw" aria-hidden="true"></i></span>
                                    </div>
                                        <input id="bed-temp-number" type="number" class="form-control col-sm-1" max="100" min="0" maxlength="1" placeholder="0-100" >
                                </div>
                            </div>




                            <div class="container">

                                    <div class="ml-3">
                                        <button onclick="setTemp()" type="button"class=" btn btn-primary ml-5 bg3" style="border-radius: 8px"><i class="px-2 fa fa-download"></i> Set Temperature</button>
                                    </div>
                            </div>

                        </form>
                    </div>
                    <div class="container">
                        <div class="card-body card-block row ">
                            <span class="mr-3 mt-1"><i class="menu-icon fa fa-file-text-o"></i></span>
                            <div class="row col-12">
                                <div class="mt-1 col-2"><label for="select" class=" form-control-label">Select Printer Job</label></div>
                                <div class=" col-6">
                                    <select name="select" id="select" class="form-control">
                                        <option value="0">Please select</option>
                                        <option value="1">Option #1</option>
                                        <option value="2">Option #2</option>
                                        <option value="3">Option #3</option>
                                    </select>                                </div>
                            </div>
                        </div>
                    </div>

                       <br>

                        <div class="col row" >
                                <div class="col-lg-4"></div>
                                <div class="col-lg-4">
                                    <button type="button" id="startPrint" class="btn btn-success col-sm-9" style="border-radius: 8px"><i class="fa fa-check fa-lg"></i>&nbsp; Start Printing </button>
                                </div>
                            </div>



                            <div class="col row mt-1">
                                <div class="col-lg-4"></div>
                                <div class="col-lg-4">
                                <button type="button" class="btn btn-danger col-sm-9" style="border-radius: 8px"><i class="fa fa-stop"></i>&nbsp; Stop Printing</button>
                                </div>
                            </div>


                    <div class="card-body card-block">
                        <input type="checkbox" id="scheduling-checkbox" style="margin-right: 5px" onclick="ScheduleArea()"> <span> <i class="fa fa-calendar"></i>&nbsp; Schedule </span>
<br>


<br>
                        <form action="" method="post" >
                            <div id="scheduling-form" style=" display: none" >
                            <div class="form-group row">

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
                            <br>
                            <div class="col row">
                                <div class="col-lg-4"></div>
                                <div class="col-lg-4">
                                <button type="button" class="btn btn-primary col-sm-9" style="border-radius: 8px"><i class="fa fa-check"></i>&nbsp; Done Scheduling </button>
                                </div>
                            </div>
                            </div>

                        </form>
                    </div>
<br>



            </div>
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
