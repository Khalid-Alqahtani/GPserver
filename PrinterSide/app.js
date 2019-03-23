let Printer = require('./Printer.js');
let Test = require('./testcalss.js');
const WebSocket = require('ws');
state = false;
var fs = require('fs');
var FormData = require('form-data');
var request = require("request");
var serverDate = {
    userOnline: false
};
serverConnect();
//serverTimeout = setInterval(serverConnect,8000);
printerTimeout = setInterval(printerConnect, 5000);
//printer = new Printer("0.0.0.0","B2125B8AE6D24507952E64B8A8366D73");
//console.log(printer.ip);
//global gsdf =4 ;
//var filecontent;

function printerConnect() {
    Printer.printerState(function (response) {
        if (response["status"] === "Printer is not operational") {
            console.log("Printer not connected");
            Printer.connect(function (response) {
                /*if (response["status"] === "Success"){
                    console.log(response);
                    console.log("Connect success");
                    clearInterval(printerTimeout);
                    return;
                }*/
                console.log("Connect fail")
            });
            return;
        }
        console.log("Printer connected");
        clearInterval(printerTimeout);
    });

}

function serverConnect() {
    //console.log("try");
    var options = {
        handshakeTimeout: 5000
    };
    ws = new WebSocket('ws://192.168.2.2:8081', options);
    //ws = new WebSocket('ws://127.0.0.1:8081');
    ws.on('open', function open() {
        console.log("Server connected");
        //clearInterval(serverTimeout);
        //ws.send('something');

    });
    ws.on('message', function incoming(data) {
        //Temp
        if (JSON.parse(data)["order"].trim() === "userOnline") {
            serverDate.userOnline = true;
            printerState();
        }
        if (JSON.parse(data)["order"].trim() === "userOffline") {
            serverDate.userOnline = false;

        }
        if (JSON.parse(data)["order"].trim() === "print") {
            startPrint("/home/pi/Desktop/test.gcode");
            console.log("heeeeereeeeee");
        }
        if (JSON.parse(data)["order"].trim() === "job") {
            jobState();
        }
        if (JSON.parse(data)["order"].trim() === "cancel") {
            jobCancel();
        }
        console.log(JSON.parse(data)["order"].trim().length);
        ws.send(data);
        //Temp
    });
    ws.on('error', function error(error) {
        console.log("Error with server");
        //setTimeout(serverConnect,5000);
    });
    ws.on('close', function error(error) {
        console.log("Server closed");
        //console.log();
        setTimeout(serverConnect, 10000);
    });
}
function jobState(){
    Printer.job(function (response) {
        ws.send(JSON.stringify(response));
    });
}
function jobCancel(){
    Printer.jobCancel(function (response) {
        ws.send(JSON.stringify(response));
    });
}
function printerState() {
    Printer.printerState(function (response) {
        ws.send(JSON.stringify(response));
    });
    if (serverDate.userOnline === true) {
        setTimeout(printerState, 5000)
    }
}

function startPrint(path) {
    Printer.startPrint(path, function (response) {
        console.log(response);
    });

}


