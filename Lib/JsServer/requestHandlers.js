var exec = require("child_process").exec;
var fs = require('fs');
var url = require('url');
var querystring = require("querystring");

function send(request, response, connections) {
    var postData = "";
    request.setEncoding("utf8");

    request.addListener("data", function (postDataChunk)  {
        postData += postDataChunk;
    });

    request.addListener("end", function () {
        var post = querystring.parse(postData);
        var session_id = post.target_session_id;

        if (connections[session_id] != undefined)
        {
            for (key in connections[session_id])
            {
                console.log(post.data);
                connections[session_id][key].sendUTF(JSON.stringify(post.data));
               // console.log("Sent data to pages : " + JSON.stringify(post.data) + " to " + session_id);
            }


        }
        else
        {
           // console.log("Couldn't send data to page for " + session_id);
        }
        response.writeHead(200, { "Content-Type":"text/plain" });
        response.end();
    });


}


exports.send = send;