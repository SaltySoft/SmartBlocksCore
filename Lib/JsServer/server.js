var WebSocketServer = require('websocket').server;
var http = require('http');


function start(route, handle) {

    var httpServer = http.createServer(function (request, response) {

        route(handle, request, response, connections);
    });


    port = process.argv[2];
    console.log(port);

    httpServer.listen(port != undefined ? port : 10001, function () {
        console.log((new Date()) + ' Websocket is listening on port 10001');
    });


    var wsServer = new WebSocketServer({
        httpServer: httpServer,
        autoAccceptConnections: false
    });

    function originIsAllowed(origin) {
        return true;
    }



    var connections = new Array();

    wsServer.on('request', function (request) {
        if (!originIsAllowed(request.origin)) {
            request.reject();

            return;
        }


        var connection = request.accept("muffin-protocol", request.origin);


        console.log("Opened connection");



        connection.on('message', function (message) {
           
            var base = this;
            var session_id = JSON.parse(message.utf8Data).identification;
            if (session_id) {
                if (!(session_id in connections) || !(connection in connections[session_id])) {
                    if (!(session_id in connections)) {
                        connections[session_id] = new Array();
                        base.session = session_id;
                    }
                    connections[session_id].push(connection);
                }
            }
            else {
                var pmessage = JSON.parse(message.utf8Data);
                console.log(pmessage);
                if (pmessage.session_ids !== undefined) {
                    for (kk in pmessage.session_ids) {
                        if (connections[pmessage.session_ids[kk]]) {
                            for (var con in  connections[pmessage.session_ids[kk]]) {
                                pmessage.data.origin = base.session;
                                connections[pmessage.session_ids[kk]][con].sendUTF(JSON.stringify(JSON.stringify(pmessage.data)));
                            }
                        }
                    }
                } else {
                    if (pmessage.broadcast) {
                        for (var kk in connections) {
                            if (connections[kk]) {
                                for (var con in  connections[kk]) {
                                    pmessage.data.origin = base.session;
                                    connections[kk][con].sendUTF(JSON.stringify(JSON.stringify(pmessage.data)));
                                }
                            }
                        }
                    }
                }
            }
        });

        connection.on('close', function (message) {
            console.log("---------------------------------------close");
            connectionsb = new Array();

            for (key in connections) {
                con2 = new Array();
                console.log(key + " : " + connections[key]);
                for (k in  connections[key]) {
                    if (connections[key][k] == connection) {
                        connections[key].splice(k, 1);
                    }
                }
                console.log(key + " : " + connections[key]);
                if (connections[key].length == 0) {
                    delete connections[key];
                }

            }

        });

        connection.on('error', function (message) {
            console.log("---------------------------------------error");
            connectionsb = new Array();

            for (key in connections) {
                con2 = new Array();
                console.log(key + " : " + connections[key]);
                for (k in  connections[key]) {
                    if (connections[key][k] == connection) {
                        connections[key].splice(k, 1);
                    }
                }
                console.log(key + " : " + connections[key]);
                if (connections[key].length == 0) {
                    delete connections[key];
                }

            }

        });
    });
}

exports.start = start;