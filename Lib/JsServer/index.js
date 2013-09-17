var server = require('./server');
var router = require("./router");
var requestHandlers = require("./requestHandlers");

var handle = {};
handle["/send"] = requestHandlers.send;

server.start(router.route, handle);