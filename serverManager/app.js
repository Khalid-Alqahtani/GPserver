// import module
const express = require('express');
const  http = require('http');
const WebSocket = require('ws');
// create module
const app = express();

//initialize a simple http server
const server = http.createServer(app);

//initialize the WebSocket server instance
const wss = new WebSocket.Server({ server });


let UsersArr = [];

//bordcast message from user to all printer
function brodcastAllprinters(ws,message) {

    let printerArr = UsersArr[ws.IDOB].printerArr;
    for(let i = 0 ; i < printerArr.length; i++){
            printerArr[i].send(message);
    }
}

/*defind the message info for printer*/
function defJSONMessageForPrinter(ws,message) {
    let data = JSON.parse(message);
    if(data["request"] === "update"){
        let index = searchForprinter(ws);
        UsersArr[ws.IDOB].printerArr[index].data = {
                                            "request":"update",
                                            "type":ws.PID,
                                            "ID" : ws.PID,
                                            "headtemp" : data["headtemp"],
                                            "bedtemp" : data["bedtemp"],
                                            "jobname" : data["jobname"],
                                            "jobstate" : data["jobstate"],
                                            "progresspersent" : data["progresspersent"],
                                            "progresstimeleft" : data["progresstimeleft"]
                                            };
        if (UsersArr[ws.IDOB].ws !== null){
            UsersArr[ws.IDOB].ws.send(JSON.stringify(UsersArr[ws.IDOB].printerArr[index].data));
        }

    } else if (data["request"] === "get"){

    } else if (data["request"] === "set"){

    }
}

/*send the temp for the printer from user*/
function setTemp(ws,type,ID,value) {
    let data = {"request" : "set",
                "type" : "temp",
                "temptype" : type,
                "value" : value,
                };
    let index = searchForprinter(ws);
    UsersArr[ws.IDOB].printerArr[index].ws.send(data);
}

/*defind the message info for printer*/
function defJSONMessageForUSer(ws,message) {
    let data = JSON.parse(message);
    if (data["request"] === "get"){

    } else if (data["request"] === "set"){
                if(data["type"] === "temp"){
                        setTemp(ws,data["temptype"],data["ID"],data["value"]);
                } else if (data["type"] === "job"){
                            setJob(ws,data["jobtype"],data["jobname"],data["ID"]);
                }
    }
}
/*search for the printer's index */
function searchForprinter(ws) {
    let printers = UsersArr[ws.IDOB].printerArr;
    for(let i =0; i < printers.length; i++){
        if (printers[i] !== null){
            if (printers[i].PID === ws.PID){
                return i;
            }
        }

    }
}

/*change the jon state for the printer from user*/
function setJob(ws,jobtype,jobname,ID) {
    let data = {
        "request" : "set",
        "type" : "job",
        "jobtype" : jobtype, /*start,pause,stop,new*/
        "jobname" : jobname,
    };
    let index = searchForprinter(ws);
    UsersArr[ws.IDOB].printerArr[index].ws.send(data);
}


//spilt the protocol to array to be easy to use
function spilt(ws){

    //the info. from the coming connection
    let protocol = ws.upgradeReq.headers['sec-websocket-protocol'];
    //spilt the info
    let a = protocol.split(",");
    //change the info from String to Integer
    for(let i = 1 ; i < a.length; i++){
        a[i] = parseInt(a[i]);
    }

    //defind the connection is't from user or printer
    ws.ConType = a[0];
    //defind the ID
    ws.IDOB = a[1];

    if(ws.ConType === "printer"){
        ws.PID = a[2];
    }

}
/*add the coming connection to the server*/
function addConnectionToArray(ws){
    spilt(ws);
    if (UsersArr[ws.IDOB] == null) {
        UsersArr[ws.IDOB] = {printerArr: []};
        if (ws.ConType === "user") {
            UsersArr[ws.IDOB].ws = ws;
            UsersArr[ws.IDOB].ID = ws.IDOB;
        } else if (ws.ConType === "printer") {
            UsersArr[ws.IDOB].ws = null;
            UsersArr[ws.IDOB].ID = ws.IDOB;
            UsersArr[ws.IDOB].printerArr.push(ws);
        }
    } else {
        if (ws.ConType === "user") {
            UsersArr[ws.IDOB].ws = ws;
            UsersArr[ws.IDOB].ID = ws.IDOB;
        } else if (ws.ConType === "printer") {
            UsersArr[ws.IDOB].ID = ws.IDOB;
            UsersArr[ws.IDOB].printerArr.push(ws);
        }
    }
}

/*update the printers for the user*/
function updatePrintersForUser(ws){
    let printerArr = UsersArr[ws.IDOB].printerArr;
    let data = {"request":"update","type":"all",};
    for(let i = 0 ; i < printerArr.length; i++){
            data[i] = printerArr[i].data;
    }
    if (printerArr.length > 0){
        ws.send(JSON.stringify(data));
    }

}

// connection is coming
wss.on('connection', (ws,rq) => {
    // upgrade the connection with the request
    ws.upgradeReq = rq;

    // add the coming connection to the arr
    addConnectionToArray(ws);
    if(ws.ConType === "user") {
        updatePrintersForUser(ws);
        let data = {"name":"m"};
        ws.send(JSON.stringify(data));
    }


    //connection is up, let's add a simple simple event
    ws.on('message', (message) => {

        if (ws.ConType === "printer") {
            defJSONMessageForPrinter(ws,message);
        } else if (ws.ConType === "user"){
            defJSONMessageForUSer(ws,message);
        }

    });

});

//start our server
server.listen(process.env.PORT || 8080, () => {
    console.log(`Server started on port ${server.address().port} :)`);
});