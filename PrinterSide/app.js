let Printer = require('./Printer.js');
var config = require('./config');
let Test = require('./testcalss.js');
const WebSocket = require('ws');
state = false;
var fs = require('fs');
const download = require('download');
var FormData = require('form-data');
var request = require("request");
const path = require('path');
const spawn = require('child_process').spawn;
const {exec} = require('child_process');
const processExists = require('process-exists');
const find = require('find-process');
var ps = require('ps-node');

var trackData = {
    update: false,
    userOnline: false,
    printing: false,
    printerApi: true,
    stream: false
};
var printerData = {};
printerConnect();
serverConnect();
runPrinterApi();
//serverTimeout = setInterval(serverConnect,8000);

//printerTimeout = setInterval(printerConnect, 5000);
printerAPITimeOut = null;
streamTimeOut = null;
//printer = new Printer("0.0.0.0","B2125B8AE6D24507952E64B8A8366D73");
//console.log(printer.ip);
//global gsdf =4 ;
//var filecontent;

function printerConnect() {
    Printer.printerState(function (response) {
        if (response["status"] === "Printer is not operational") {
            console.log(response["status"] === "Printer is not operational");
            console.log("Printer not connected");
            Printer.connect(function (response) {
                console.log("Connect fail")
            });
            setTimeout(printerConnect, 5000);
        } else {
            console.log("Printer connected");
        }


    });

}

function serverConnect() {

    var options = {
        handshakeTimeout: 5000
    };
    //ec2-18-191-141-188.us-east-2.compute.amazonaws.com
    //ws = new WebSocket('ws://192.168.2.9:8080', Headers = "printer,2,17", options);
    headers = "printer," + config.user + "," + config.printer;
    console.log(headers);
    ws = new WebSocket('ws://ec2-18-191-141-188.us-east-2.compute.amazonaws.com:8080', Headers = headers, options);
    //ws = new WebSocket('ws://192.168.2.9:8080', Headers = "printer,2,17", options)
    ws.on('open', function open() {
        console.log("Server connected");

    });
    ws.on('message', function incoming(data) {
        let newData = JSON.parse(data);
        console.log(data);
        console.log(newData);
        //Temp
        try{
        if (JSON.parse(data)["request"] === "updateAll") {
            trackData.userOnline = true;
            //startStream(30000);
            startStream(60000);
            printerJobState();
        }
        if (JSON.parse(data)["request"] === "set") {
            if (newData["type"] === "stop") {
                jobCancel();
            }
        }
        if (JSON.parse(data)["request"] === "set") {
            if (newData["type"] === "start") {
                startPrint(newData["url"]);
            }
        }

        if (newData["request"] === "set") {
            if (newData["temptype"] === "head") {
                if (parseInt(newData["value"]) >= 0 && parseInt(newData["value"]) <= 250) {
                    Printer.setToolTemp(parseInt(newData["value"]), function (res) {
                        console.log(res);
                    });
                }
            }
        }
        if (newData["request"] === "set") {
            if (newData["temptype"] === "bed") {
                if (parseInt(newData["value"]) >= 0 && parseInt(newData["value"]) <= 100) {
                    Printer.setBedTemp(parseInt(newData["value"]), function (res) {
                        console.log(res);
                    });
                }
            }
        }
        if (JSON.parse(data)["request"].trim() === "userOffline") {
            trackData.userOnline = false;
            trackData.stream = false;
        }
        if (JSON.parse(data)["request"].trim() === "userOnline") {
            trackData.userOnline = true;
            console.log("1");
            printerJobState();
        }}
        catch (e) {
            printerConnect;
        }
        /*
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
        console.log(JSON.parse(data)["order"].trim().length);*/
        if (JSON.parse(data)["request"] === "set") {
            if (newData["live"] === "on") {
                startStream(60000);
            }
        }
        if (JSON.parse(data)["request"] === "set") {
            if (newData["live"] === "off") {
                //startStream();
                stopStream();
            }
        }
    });
    ws.on('error', function error(error) {
        console.log("Error with server");
        //jobState();
        setTimeout(serverConnect,5000);
    });
    ws.on('close', function error(error) {
        console.log("Server closed");
        setTimeout(serverConnect, 10000);
    });
}

function jobState() {
    Printer.job(function (response) {
        senfToServer(JSON.stringify(response));
    });
}

function jobCancel() {
    Printer.jobCancel(function (response) {

            console.log(response);

    });
}

function printerJobState() {
    Printer.printerState(function (printer) {
        Printer.job(function (job) {
            marge = {printer, job};
            marge["request"] = "update";
            senfToServer(JSON.stringify(marge));

                if (printer["state"]["flags"]["printing"] != trackData.printing) {
                    trackData.printing = printer["state"]["flags"]["printing"];
                }

        });

    });
    if (trackData.userOnline === true || trackData.printing) {
        setTimeout(printerJobState, 5000)
    }
}

function startPrint(url) {
    if (trackData.printing == false) {
        download(url, '/home/pi/Desktop/gcode/tmp').then(() => {
            Printer.startPrint('/home/pi/Desktop/gcode/tmp/' + path.basename(url), function (response) {
                fs.unlink('/home/pi/Desktop/gcode/tmp/' + path.basename(url), function (err) {
                    if (err) {
                        console.log("failed to delete local image:" + err);
                    } else {
                        console.log("Upload Done");
                        console.log("Print started");
                        console.log("File deleted locally");
                        console.log('done!');
                        trackData.printing = true;
                    }
                });

            });

        });
    } else {
        console.log("Can not start new job")
    }
}

function startStream(time) {
    if (trackData.stream == true) {
        console.log(streamTimeOut);
        try {
            clearTimeout(streamTimeOut);
        } catch (e) {
        }
        streamTimeOut = setTimeout(stopStream, time);
        return;
    }
    //
    trackData.stream = true;
    streamTimeOut = setTimeout(stopStream, time);
    let streamPath = 'rtmp://rcm3dstream.com/hls/u' + config.user + "p" + config.printer;
    ffmpeg = spawn('ffmpeg', ['-f', `video4linux2`, '-framerate', '10', `-video_size`, '640x480', '-i', '/dev/video0', `-c:v`, 'libx264', '-an', `-f`, 'flv', `${streamPath}`]);
//ffmpeg -f video4linux2 -framerate 10  -video_size 640x480 -i /dev/video0 -c:v libx264 -an -f flv rtmp://rcm3dstream.com/hls/mystream
    ffmpeg.on('exit', (statusCode) => {
        if (statusCode === 0) {
            console.log('conversion successful')
        }
    });
    ffmpeg
        .stderr
        .on('data', (err) => {
            console.log('err:', new String(err))
        });

}

function stopStream() {
    trackData.stream = false;
    try {


        processExists("ffmpeg").then(exists => {
            if (exists == true) {
                find('name', 'ffmpeg', false)
                    .then(function (list) {

                        ps.kill(list[0].pid, function (err) {
                            if (err) {
                                throw new Error(err);
                            } else {
                                console.log('ffmpeg has been killed!');
                            }
                        });

                    });
            }

        });
    } catch (e) {
        console.log(e);
    }
}

process.on('uncaughtException', function (err) {

});

function runPrinterApi() {
    processExists("octoprint").then(exists => {
        if (false == exists) {
            trackData.printerApi = false;

        } else {
            trackData.printerApi = true;
        }
    });
}

function senfToServer(message) {
    try {
        ws.send(message);
    } catch (e) {
        console.log(e);
    }
}

