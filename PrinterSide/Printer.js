var config = require('./config');
const dns = require('dns');
var http = require('http');
var fs = require('fs');
const path = require('path');
var FormData = require('form-data');
var request = require("request");

// --------------------------------------------------------------------------
// This is the section for helper function(s).
// --------------------------------------------------------------------------

// Returns the response from the printer, given the path
function queryPrinter(method, path, body, callback) {
    if (config.apiKey == '') {
        return callback('{"status": "Config error: No apiKey set in node_modules/octo-client/config.js file"}');
    }
    var options;
    if (body) {
        var postData = JSON.stringify(body);
        options = {
            hostname: config.hostName, port: config.port, path: path, method: method, timeout: 5000,
            headers: {
                'X-Api-Key': config.apiKey,
                'Content-Type': 'application/json',
                'Content-Length': Buffer.byteLength(postData)
            }
        };  // options = ...
    } else {
        options = {
            hostname: config.hostName, port: config.port, path: path, method: method, timeout: 5000,
            headers: {
                'X-Api-Key': config.apiKey,
                'Content-Type': 'application/json'
            }
        };  // options = ...
    }     // if (body)
    dns.lookup(config.hostName, function (err, address) {
        if (err) {
            callback('{"status": "' + err + '"}');
            return;
        }

        if (method == "GET") {
            http.get(options, function (resp) {
                resp.setEncoding('utf8');
                var data = '';
                resp.on('data', function (chunk) {
                    data += chunk;
                });
                resp.on('error', function (err) {
                    console.log('queryPrinter(): ' + err);
                });
                resp.on('end', function () {
                    return callback(data);
                });
            });
        } else if (method == "POST") {
            var req = http.request(options, function (resp) {
                resp.setEncoding('utf8');
                var data = '';
                resp.on('data', function (chunk) {
                    data += chunk;
                });
                resp.on('end', function () {
                    if (resp.statusCode == 204) return callback('{"status": "Success"}');
                    return callback(data);
                });  // resp.on('end')
            });    // var req = ...
            req.on('error', function (err) {
                console.log('queryPrinter(): ' + err);
            });
            req.write(postData);
            req.end();
        } else {
            // Unsupported method sent in
            callback({"status": "Unknown method sent to queryPrinter(): " + method});
        }  // if (method...)
    });  // dns.lookup()
}      // function queryPrinter()

// --------------------------------------------------------------------------
// The exported interface consists of several methods for querying the
// printer directly. The try/catch behavior is necessary since some of the
// OctoPrint error conditions come back as plain text.
// --------------------------------------------------------------------------
module.exports = {
    setToolTemp: function (target, callback) {
        var body = {
            "command": "target", "targets": {
                "tool0": target
            }
        };
        queryPrinter('POST', '/api/printer/tool', body, function (response) {
            try {
                return callback(JSON.parse(response));
            } catch (e) {
                console.log('connect(): ' + e.message);
                return callback({"status": response});
            }
        });
    },
    startPrint: function (Path, callback) {
        console.log("here");
        var form ={
            file: {

                value: fs.createReadStream(Path),
                options: { filename: path.basename(Path)}
            },
            select :"true",
            print :"true"
        };
        console.log(Path);
        console.log(path.basename(Path));

        var options = {
            method: 'POST',
            url: "http://"+config.hostName+":"+config.port+"/api/files/local",
            headers: { 'x-api-key': config.apiKey},
            formData: form
        };
        var req = request(options, function (error, response, body) {
            if (error) throw new Error(error);
            console.log(body);
        });
    },
    setBedTemp: function (target, callback) {
        var body = {
            "command": "target", "target": target
        };
        queryPrinter('POST', '/api/printer/bed', body, function (response) {
            try {
                return callback(JSON.parse(response));
            } catch (e) {
                console.log('connect(): ' + e.message);
                return callback({"status": response});
            }
        });
    },
    // Version information - http://docs.octoprint.org/en/master/api/version.html
    version: function (callback) {
        queryPrinter('GET', '/api/version', null, function (response) {
            try {
                return callback(JSON.parse(response));
            } catch (e) {
                console.error('version(): ' + e.message);
                return callback({"status": response});
            }
        });
    },
    // Connection handling - http://docs.octoprint.org/en/master/api/connection.html
    connection: function (callback) {
        queryPrinter('GET', '/api/connection', null, function (response) {
            try {
                return callback(JSON.parse(response));
            } catch (e) {
                console.error('connection(): ' + e.message);
                return callback({"status": response});
            }
        });
    },
    connect: function (callback) {
        var body = {"command": "connect"};
        queryPrinter('POST', '/api/connection', body, function (response) {
            try {
                return callback(JSON.parse(response));
            } catch (e) {
                console.log('connect(): ' + e.message);
                return callback({"status": response});
            }
        });
    },
    disconnect: function (callback) {
        var body = {"command": "disconnect"};
        queryPrinter('POST', '/api/connection', body, function (response) {
            try {
                return callback(JSON.parse(response));
            } catch (e) {
                console.log('disconnect(): ' + e.message);
                return callback({"status": response});
            }
        });
    },
    // Job operations - http://docs.octoprint.org/en/master/api/job.html
    job: function (callback) {
        queryPrinter('GET', '/api/job', null, function (response) {
            try {
                return callback(JSON.parse(response));
            } catch (e) {
                console.error('job(): ' + e.message);
                return callback({"status": response});
            }
        });
    },

    jobCancel: function (callback) {
        var body = {"command": "cancel"};
        queryPrinter('POST', '/api/job', body, function (response) {
            try {
                return callback(JSON.parse(response));
            } catch (e) {
                console.error('jobCancel(): ' + e.message);
                return callback({"status": response});
            }
        });
    },

    // Printer operations - http://docs.octoprint.org/en/master/api/printer.html
    printerState: function (callback) {
        queryPrinter('GET', '/api/printer', null, function (response) {
            try {
                return callback(JSON.parse(response));
            } catch (e) {
                console.log('printerState(): ' + e.message);
                return callback({"status": response});
            }
        });
    },

    // ------------------------------------------------------------------------
    // End of exported function list
    // ------------------------------------------------------------------------
};
