const WebSocket = require('ws');
var OctoPrint = require('octo-client');

const request = require('request');

OctoPrint.setToolTemp(0,function(response){
    console.log(response);
});
OctoPrint.setBedTemp(0,function(response){
    console.log(response);
});
