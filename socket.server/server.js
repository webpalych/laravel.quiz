var io = require('socket.io')(6560);
var request = require('request');
var Redis = require('ioredis');

redis = new Redis;

redis.psubscribe('*', function (error, count) {

});

redis.on('pmessage', function(subscribed, channel, message) {
    message = JSON.parse(message);
    var room = 'room-' + message.data.room;
    io.sockets.in(room).emit(message.event, message.data.data);
    //console.log(message);
});

io.on('connection', function(socket) {


    socket.on('joinRoom', function (data) {
        request.get({
            url: 'http://quiz.loc/room/join/' + data.room,
            json: true,
            auth: {
                'bearer': data.user
            }
        }, function (error, response, json) {
            if (json.message == 'success') {

                var room = 'room-' + data.room;

                socket.join(room , function () {
                    //console.log(socket.rooms);
                })

            } else {

                socket.emit('errors', json);

            }
        })

    });

    socket.on('getQuestion', function (data) {
        request.get({
            url: 'http://quiz.loc/quiz/get_question/' + data.room,
            json: true,
            auth: {
                'bearer': data.user
            }
        }, function (error, response, json) {})

    });

});




