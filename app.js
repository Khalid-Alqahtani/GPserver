const WebSocket = require('ws');

const wss = new WebSocket.Server({port: 1000});

wss.on('connection', function connection(ws) {

    ws.on('message', function incoming(message) {
        console.log(message);
        //hahahaha
        ws.send(message);
    });



});