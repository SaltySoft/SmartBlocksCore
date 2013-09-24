var url = require("url");

function route(handle, request, response, connections) {
    var pathname = url.parse(request.url).pathname;
    //  console.log("About to route a request for " + pathname);
    if (typeof handle[pathname] === 'function') {
        handle[pathname](request, response, connections);
    }
    else
    {
        //   console.log("No request handler found for " + pathname);
        response.writeHead(404, {"Content-type" : "text/plain"});
        response.write("404 Not Found");
        response.end();
    }
}

exports.route = route;