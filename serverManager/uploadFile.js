/*const path = require('path');
var config = require('./config');

Path = "/home/pi/Desktop/test.gode";
let Printer = require('./Printer.js');
Printer.printerState(function (response) {

    response["request"]="update";
    console.log(JSON.stringify(response));
});*/
const AWS = require('aws-sdk');
const fs = require('fs');
const path = require('path');
var express = require('express');
var multer  = require('multer');
var upload = multer({ dest: 'uploads/' });
var app = express();

// For dev purposes only




//configuring the AWS environment
AWS.config.update({
  accessKeyId: "",
  secretAccessKey: ""
});

var s3 = new AWS.S3();



//configuring parameters






app.get('/',function(req,res){
  res.sendFile(__dirname + "/index.html");
  console.log(req.param('id'));
});

var id ;
app.post('/api/file', upload.single('node'),function (req, res, next) {

  var filePath = req.file["path"];

  let data = req["originalUrl"];

  let parse = data.split("=");
  id = parse[1];

  var params = {
    Bucket: 'gcodes',
    Body : fs.createReadStream(filePath),
    Key : ""+id+"/"+req.file["originalname"],
    ACL: 'public-read'
  };


  s3.upload(params, function (err, data) {
    //handle error
    if (err) {
      console.log("Error", err);
    }

    //success
    if (data) {
      console.log("Uploaded in:", data.Location);

    }
  });

});

app.listen(3030,function(){
  console.log("Working on port 3000");
});
