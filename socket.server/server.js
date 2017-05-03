var io =require('socket.io')(6560);

io.on('connection', function(socket) {
    console.log('hello');
})