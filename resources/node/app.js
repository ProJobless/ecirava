// app.js located in /resources/node/

var http = require('http');
var url = require('url');
var fs = require('fs');
var io = require('socket.io');
var mysql      = require('mysql');
var connection = mysql.createConnection({
  host     : 'localhost',
  port	   : '8889',
  user     : 'root',
  password : 'Adhack2010',
  database : 'main'
});

connection.connect();

connection.query('SELECT username FROM users WHERE id = 7', function(err, rows, fields) {
  if (err) throw err;

  console.log('The solution is: ', rows[0].username);
});

connection.end();

var server = http.createServer(function(request, response) {
	console.log('Connection Established');
	var path = url.parse(request.url).pathname;
 
    switch(path){
        case '/':
            response.writeHead(200, {'Content-Type': 'text/html'}); 
            response.write('No Direct Directory Access Allowed');
            break;
        case '/socketio-test.html':
            fs.readFile('socketio-test.html', 'utf8', function(error, data){
                if (error){
                    response.writeHead(404);
                    response.write("404 - The Test Page Was Not Found");
                }
                else{
                    response.writeHead(200, {"Content-Type": "text/html"});
                    response.write(data);
                    response.end();
                }
                return;
            });
            break;
        default:
            response.writeHead(404);
            response.write("404 - This Page doesn't Exist");
            break;
    }
});

server.listen(8080);

var io = io.listen(server);
io.set('log level', 1); 


io.sockets.on('connection', function(socket){
    socket.emit('message', {'message': 'hello world'});

    socket.on('test', function(){
		io.sockets.socket(socket.id).emit('message', {'message': socket.id});
	});
});
