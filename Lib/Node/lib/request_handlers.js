var exec = require("child_process").exec;
var fs = require('fs');
var url = require('url');
var querystring = require("querystring");

function send(request, response, io) {
    var postData = "";
    request.setEncoding("utf8");

    request.addListener("data", function (postDataChunk)  {
        postData += postDataChunk;
    });

    request.addListener("end", function () {
        var post = querystring.parse(postData);
        var session_id = post.target_session_id;

        var clients = io.sockets.clients();
        for (var k in clients) {
            var client = clients[k];
            client.get('id', function (err, id) {
                if (id == session_id) {
                    var elt = {};
                    elt = JSON.parse(post.data);
                    elt.origin = post.origin;
                    client.emit("msg", elt);
                }
            });
        }
        response.writeHead(200, { "Content-Type":"text/plain" });
        response.end();
    });
}
exports.send = send;