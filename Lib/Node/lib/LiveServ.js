/*
 * LiveServ
 * https://github.com/SaltySoft/SmartBlocksCore
 *
 * Copyright (c) 2013 Antoine Jackson
 * Licensed under the MIT license.
 */

'use strict';


exports.init = function () {

    var router = require("./router");
    var requestHandlers = require("./request_handlers");

    var handle = {};
    handle["/send"] = requestHandlers.send;

    var app = require('http').createServer(handler), io = require('socket.io').listen(app), fs = require('fs');

    app.listen(10001);

    function handler(req, res) {
        router.route(handle, req, res, io);
    };

    io.sockets.on('connection', function (socket) {
        socket.emit('news', {hello: "world"});

        socket.on('my other event', function (data) {
            console.log(data);
        });

        socket.on('set id', function (php_session_id) {
            socket.set('id', php_session_id);
        });
    });



    return "test";
};