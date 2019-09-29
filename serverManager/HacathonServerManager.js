// import module
const express = require('express');
const  http = require('http');
const WebSocket = require('ws');
var mysql = require('mysql');


// create module
const app = express();

//initialize a simple http server
const server = http.createServer(app);

//initialize the WebSocket server instance
const wss = new WebSocket.Server({ server });


var con = mysql.createConnection({
    host: "160.153.133.150",
    user: "Meshal",
    password: "12341234",
    database: "tamerZad2"
});

function connectToDB(){
    con.connect(function(err) {
        if (err) throw err;
        console.log("Connected!");
    });
    con.query("select * from product where state = ?",["open"], function (err, result) {
        if (err) throw err;
        result.forEach(function (row) {
            console.log(row);
        });
    });
    con.end();
}


let UsersArr = [];


function getMarkets() {
    con.connect(function(err) {
        if (err) throw err;
        console.log("Connected!");
    });
    con.query("select * from saller", function (err, result) {
        if (err) throw err;
         for(var row in result){
            console.log(result[row]["id"]);
            con.query("select * from product where sallerID = '"+ result[row]["id"]+ "'", function (err,results,fields) {
                if (err) throw err;
               console.log(results);
               console.log("---==---");
            });
        }
         con.end();
    });

}

/*add the coming connection to the server*/
function Connection(ws){
    try {
        //the info. from the coming connection
        let protocol = ws.upgradeReq.headers['sec-websocket-protocol'];
        //spilt the info
        let a = protocol.split(",");
        if (a[0] === "Bayer") {
            ws.ConType = "Bayer";
        } else if (a[0] === "User") {
            ws.ConType = "User";
        } else {
            ws.terminate();
        }
        if (a[0] === "Bayer" || a[0] === "User") {
            //defined the ID
            ws.ID = parseInt(a[1]);
            if (ws.ConType === "Bayer") {
                ws.BayerID = ws.ID = parseInt(a[1]);
                ws.MarketsID = parseInt(a[2]);
                ws.MarketsPrice = parseInt(a[3]);
                ws.MarketsState = "open";
                console.log(ws.MarketsPrice);
            }
            if (ws.ConType === "User") {
                ws.UserID = ws.ID = parseInt(a[1]);
                ws.BayerID = parseInt(a[2]);
                ws.MarketsID = parseInt(a[3]);
                sendDataToNewUser(ws);
            }
        }
    } catch (e) {
        console.log(e.message);
        ws.terminate();
    }
}


function brodcastDataToAll(WS,message){
    wss.clients.forEach(function each(ws) {
        if (ws.BayerID === WS.BayerID && ws.MarketsID === WS.MarketsID) {
            ws.send(JSON.stringify(message));
        }
    });
}


function updatePrice(WS,price,callback) {
    wss.clients.forEach(function each(ws) {
        if (ws.ConType === "Bayer") {
            if (ws.BayerID === WS.BayerID && ws.MarketsID === WS.MarketsID) {
                ws.MarketsPrice = ws.MarketsPrice + price;
                callback(ws.MarketsPrice);
            }
        }
    });
}


function getPrice(WS,callback) {
    wss.clients.forEach(function each(ws) {
        if (ws.ConType === "Bayer") {
            if (ws.BayerID === WS.BayerID && ws.MarketsID === WS.MarketsID) {
                callback(ws.MarketsPrice);
            }
        }
    });
}

async function dataPreparation(ws,message) {
    let messageJSON = JSON.parse(message);
    if(messageJSON["get"] === "update") {
        await updatePrice(ws, messageJSON["price"], function (results) {

            let data = {"get": "update", "price": results};
            console.log(data);
            brodcastDataToAll(ws, data);

        });
    } else  if (messageJSON["get"] === "finish") {
        let data = {"get":"finish"};
        brodcastDataToAll(ws,data);
    } else if (messageJSON["get"] === "close"){
        let data = {"get":"close"};
        brodcastDataToAll(ws,data);
    }
}

function sendDataToNewUser(ws) {
    getPrice(ws,function (results){
        let data = {"get": "update", "price":results};
        ws.send(JSON.stringify(data));
    });
}





function routing(ws,message){
    dataPreparation(ws,message);
}






// connection is coming
wss.on('connection', (ws,rq) => {
    // upgrade the connection with the request
    ws.upgradeReq = rq;


    ws.isAlive = true;
    ws.on('pong', heartbeat);

    interval;

    Connection(ws);


    //connection is up, let's add a simple simple event
    ws.on('message', (message) => {


        console.log(message);
        routing(ws,message);


    });

    ws.on('close', function() {
        ws.isAlive = false;
    });

});

function heartbeat() {
    this.isAlive = true;
}


function noop() {}


const interval = setInterval(function ping() {
    wss.clients.forEach(function each(ws) {
        if (ws.isAlive === false) {
            return ws.terminate();
        }

        ws.isAlive = false;
        ws.ping(noop);
    });
}, 1000);



//start our server
server.listen(process.env.PORT || 8080, () => {
    console.log(`Server started on port ${server.address().port} :)`);
    getMarkets();
});