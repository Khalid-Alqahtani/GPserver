// import module
const express = require('express');
const  http = require('http');
const WebSocket = require('ws');
var mysql = require('mysql');
var listAllObjects = require('s3-list-all-objects');

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
        try {
            printerArr[i].send(message);
        } catch (e) {
            console.log(e);
        }
    }
}

/*defind the message info for printer*/
function defJSONMessageForPrinter(ws,message) {
    let data = JSON.parse(message);
    if(data["request"] === "update"){
        let index = searchForPrinterByWS(ws);
        UsersArr[ws.IDOB].printerArr[index].data = {
            "request":"update",
            "type":ws.PID,
            "ID" : ws.PID,
            "headtemp" : data["temperature"]["tool0"]["actual"],
            "headtemptarget" :data["temperature"]["tool0"]["target"],
            "bedtemptarget" : data["temperature"]["bed"]["target"],
            "bedtemp" : data["temperature"]["bed"]["actual"],
            "jobname" : "test",
            "jobstate" : "ready",
            "progresspersent" : "-",
            "progresstimeleft" : "-"
        };

        if (UsersArr[ws.IDOB].ws !== null){
            try {
                UsersArr[ws.IDOB].ws.send(JSON.stringify(UsersArr[ws.IDOB].printerArr[index].data));
            } catch (e) {
                console.log(e);
            }
        }

    } else if (data["request"] === "get"){

    } else if (data["request"] === "set"){

    }
}

/send the temp for the printer from user/
function setTemp(ws,type,ID,value) {
    let data = {"request" : "set",
        "type" : "temp",
        "temptype" : type,
        "value" : value,
    };
    let index = searchForPrinterByID(ws,ID);
    try {
        UsersArr[ws.IDOB].printerArr[index].send(JSON.stringify(data));

    } catch (e) {
        console.log(e);
    }
}

/*defind the message info for user*/
function defJSONMessageForUSer(ws,message) {
    let data = JSON.parse(message);
    if (data["request"] === "get"){
        if(data["live"] === "on"){
            let index = searchForPrinterByID(ws,data["printer"]);
            let printerData = {"request":"set","live":"on"};
            try {
                UsersArr[ws.IDOB].printerArr[index].send(JSON.stringify(printerData));

            } catch (e) {
                console.log(e);
            }
            let userData = {"request":"stream","ID":data["printer"],"url":""};
            try {
                ws.send(JSON.stringify(userData));
            } catch (e) {
                console.log(e);
            }
        } else if(data["live"] === "off"){
            let index = searchForPrinterByID(ws,data["printer"]);
            let dataJSON = {"request":"set","live":"off"};
            try {
                UsersArr[ws.IDOB].printerArr[index].send(JSON.stringify(dataJSON));

            } catch (e) {
                console.log(e);
            }
        }
    } else if (data["request"] === "set"){
        if(data["type"] === "temp"){
            setTemp(ws,data["temptype"],data["ID"],data["value"]);
        } else if (data["type"] === "job"){
            setJob(ws,data["jobtype"],data["jobname"],data["ID"]);
        } else if (data["type"] === "stop"){
            let index = searchForPrinterByID(ws,data["ID"]);
            try {
                UsersArr[ws.IDOB].printerArr[index].send(JSON.stringify({"request":"set","type":"stop"}));

            } catch (e) {
                console.log(e);
            }
        } else if (data["type"] === "setTime"){
            setTimeout(scheduleJob,data["time"],[ws,data["ID"],data["fileName"]]);
        }
    }
}
/*search for the printer's index by ws */
function searchForPrinterByWS(ws) {
    let printers = UsersArr[ws.IDOB].printerArr;
    for(let i =0; i < printers.length; i++){
        if (printers[i] !== null){
            if (printers[i].PID === ws.PID){
                return i;
            }
        }

    }
    return -1;
}

/*search for the printer's index by ws and ID */
function searchForPrinterByID(ws,ID) {
    let printers = UsersArr[ws.IDOB].printerArr;
    for(let i =0; i < printers.length; i++){
        if (printers[i] !== null){
            if (printers[i].PID === parseInt(ID)){
                return i;
            }
        }

    }
    return -1;
}

function setJob(ws,jobtype,jobname,ID) {
    let data = {
        "request" : "set",
        "type" : "job",
        "jobtype" : jobtype,
        "jobname" : jobname,
    };
    let index = searchForPrinterByWS(ws);
try {
    UsersArr[ws.IDOB].printerArr[index].ws.send(data);

}catch (e) {
    console.log(e);
}
}


//spilt the protocol to array to be easy to use
function spilt(ws){

    //the info. from the coming connection
    let protocol = ws.upgradeReq.headers['sec-websocket-protocol'];
    //spilt the info
    let a = protocol.split(",");

    if (a[0] === "signIn"){
        ws.ConType = "signIn";
    } else if (a[0] === "signUp"){
        ws.ConType = "signUp";
    } else if (a[0] === "user" || a[0] === "printer"){


        //defind the connection is't from user or printer
        ws.ConType = a[0];
        //defind the ID
        ws.IDOB = parseInt(a[1]);

        if(ws.ConType === "printer"){
            ws.PID = parseInt(a[2]);
        }

        if (ws.ConType === "user" && a[2] !== null && a[2] === "flutter") {
            try {
                printersInfoForMoplie(ws);
            } catch (e) {
                console.log(e);
            }
        }
    }



}

function checkForUSer(ws) {
    if(UsersArr[ws.IDOB].ws !== null){
        let data = JSON.stringify({"request":"updateAll"});
        try {
            ws.send(data);
        } catch (e) {
            console.log(e);
        }
    }
}
/add the coming connection to the server/
function addConnectionToArray(ws){
    spilt(ws);
    if(ws.ConType === "user" || ws.ConType === "printer") {
        if (UsersArr[ws.IDOB] == null) {
            UsersArr[ws.IDOB] = {printerArr: []};
            if (ws.ConType === "user") {
                UsersArr[ws.IDOB].ws = ws;
                UsersArr[ws.IDOB].ID = ws.IDOB;
                let data = JSON.stringify({"request": "updateAll"});
                brodcastAllprinters(ws, data);
            } else if (ws.ConType === "printer") {
                UsersArr[ws.IDOB].ws = null;
                UsersArr[ws.IDOB].ID = ws.IDOB;
                UsersArr[ws.IDOB].printerArr.push(ws);
                checkForUSer(ws);
            }
        } else {
            if (ws.ConType === "user") {
                UsersArr[ws.IDOB].ws = ws;
                UsersArr[ws.IDOB].ID = ws.IDOB;
                let data = JSON.stringify({"request": "updateAll"});
                brodcastAllprinters(ws, data);
            } else if (ws.ConType === "printer") {
                let index = searchForPrinterByWS(ws);
                if (index !== -1) {
                    UsersArr[ws.IDOB].ID = ws.IDOB;
                    UsersArr[ws.IDOB].printerArr[index] = ws;
                } else {
                    UsersArr[ws.IDOB].ID = ws.IDOB;
                    UsersArr[ws.IDOB].printerArr.push(ws);
                }
                checkForUSer(ws);

            }
        }
    }
}

function conecctToDataBase() {
    let con = mysql.createConnection({
        host: "160.153.133.170",
        user: "nn",
        password: "12345",
        database:"RCM3D"
    });

    return con;
}
/*check database for sign in*/
function signIn(ws,message) {

    let data = JSON.parse(message);
    let userName = data["userName"];
    let passWord = data["passWord"];
    let con = conecctToDataBase();

    try {
        con.connect(function(err) {
            if (err) throw err;
            let searchQuery = "SELECT * FROM Account WHERE UserName = '"+userName+"' AND Pass = '"+passWord+"'";
            con.query(searchQuery, function (err, result, fields) {
                if (err) throw err;
                if(result.length === 0){
                    try {
                        ws.send(JSON.stringify({"state":"user name or password not correct"}));

                    } catch (e) {
                        console.log(e);
                    }
                    con.end();
                } else {
                    let data = result[0];
                    try {
                        ws.send(JSON.stringify({"state":"found" , "username": result[0].UserName ,"ID" :result[0].ID , "email":result[0].email}));

                    } catch (e) {
                        console.log(e);
                    }
                    con.end();

                    //ws.close();
                }
            });
        });
    } catch (e) {
        console.log(e);
    }
}


function myFunc(arg) {
    console.log(`arg was => ${arg}`);
}

function scheduleJob(arg) {
    let index = searchForPrinterByID(arg[0],arg[1]);
    if(index !== -1){
        if (UsersArr[ws.IDOB].printerArr[index].data["jobstate"] === "ready"){
            try {
                UsersArr[ws.IDOB].printerArr[index].send(JSON.stringify({"request":"set","type":"setJob","fileUrl":""}));
            } catch (e) {
                console.log(e);
            }
        }
    }

}

function signUp(ws,message) {
    let data = JSON.parse(message);
    let userName = data["userName"];
    let passWord = data["passWord"];
    let confirmPassWord = data["confirmPassWord"];
    let email = data["email"];
    let fName = data["fName"];
    let lName = data["lName"];


    if (passWord === confirmPassWord){
        let con = conecctToDataBase();
       try {
           con.connect(function(err) {
               if (err) throw err;
               let searchQuery = "SELECT * FROM Account WHERE email = '"+email+"' or UserName = '"+userName+"'";
               con.query(searchQuery, function (err, result, fields) {
                   if (err) throw err;
                   if(result.length === 0){
                       let addQuery = "INSERT INTO Account (userName, email, Pass, Fname, Lname) VALUES ('"+userName+"', '"+email+"','"+passWord+"','"+fName+"','"+lName+"')";
                       con.query(addQuery, function (err, result, fields) {
                           if (err) throw err;
                           try {
                               ws.send(JSON.stringify({"state":"Done"}));

                           } catch (e) {
                               console.log(e);
                           }
                           con.end();
                       });
                   } else {
                       try {
                           ws.send(JSON.stringify({"state":"userExists"}));

                       } catch (e) {
                           console.log(e);
                       }
                       con.end();
                       //علمني من اللي تكرر اليوزر ولا الايميل
                   }

               });
           });
       } catch (e) {
           console.log(e);
       }


    } else {
        try {
            ws.send(JSON.stringify({"state":"password Not Match"}));

        } catch (e) {
            console.log(e);
        }
    }

}


function getListFileName(ws) {

}

/*update the printers for the user*/
function updatePrintersForUser(ws){
    let printerArr = UsersArr[ws.IDOB].printerArr;
    let data = {"request":"update","type":"all",};
    for(let i = 0 ; i < printerArr.length; i++){
        data[i] = printerArr[i].data;
    }
    if (printerArr.length > 0){
        data["count"] = printerArr.length;
        try {
            ws.send(JSON.stringify(data));

        } catch (e) {
            console.log(e);
        }
    }

}



function printersInfoForMoplie(ws){
    let con = conecctToDataBase();

    let printerOwned = ws.IDOB;
    try {
        con.connect(function(err) {
            if (err) throw err;
            let searchQuery = "SELECT ID FROM Printer WHERE printerOwned = '"+printerOwned+"'";
            con.query(searchQuery, function (err, result, fields) {
                if (err) throw err;
                if(result.length > 0){
                    let data = {"request":"PrintersNum","length":result.length};
                    for(var i =0; i<result.length;i++){
                        data[i] = result[i]["ID"];
                    }
                    try {
                        ws.send(JSON.stringify(data));

                    }catch (e) {
                        console.log(e);
                    }
                    console.log(data);
                    con.end();
                } else {
                con.end();
                }

            });
        });
    } catch (e) {
        console.log(e);
    }

}

// connection is coming
wss.on('connection', (ws,rq) => {

    // upgrade the connection with the request
    ws.upgradeReq = rq;
    setTimeout(myFunc, 3000, 'con1');


    setTimeout(myFunc, 10000, 'con2');




    // add the coming connection to the arr
    addConnectionToArray(ws);
    if(ws.ConType === "user") {
        updatePrintersForUser(ws);

    }


    //connection is up, let's add a simple simple event
    ws.on('message', (message) => {

        try {
            if (ws.ConType === "printer") {
                defJSONMessageForPrinter(ws,message);
            } else if (ws.ConType === "user"){
                defJSONMessageForUSer(ws,message);
                console.log(message);
            } else if (ws.ConType === "signIn") {
                try {
                    signIn(ws,message);
                } catch (e) {
                  console.log(e);
                }
            } else if (ws.ConType === "signUp"){
                try {
                    signUp(ws,message);
                } catch (e) {
                    console.log(e);
                }
            }
        } catch (e) {
            console.log(e);
        }


    });

    ws.on('close', function() {
        console.log('stopping client interval');
        if (ws.ConType === "user"){
            UsersArr[ws.IDOB].ws = null;
            let data = JSON.stringify({"request":"userOffline"});
            brodcastAllprinters(ws,data)
        } else if (ws.ConType === "printer") {
            if (UsersArr[ws.IDOB].ws !== null) {
                try {
                    UsersArr[ws.IDOB].ws.send(JSON.stringify(
                        {
                            "request": "update",
                            "ID": ws.PID,
                            "jobstate": "disconnected"
                        }));
                } catch (e) {
                    console.log(e);
                }
                let index = searchForPrinterByWS(ws);
                if(UsersArr[ws.IDOB].printerArr.length === 1){
                    UsersArr[ws.IDOB].printerArr.pop();
                } else {
                    UsersArr[ws.IDOB].printerArr.splice(index, 1);
                }
            }
        }
    });

});

//start our server
server.listen(process.env.PORT || 8080, () => {
    console.log(`Server started on port ${server.address().port} :)`);
});