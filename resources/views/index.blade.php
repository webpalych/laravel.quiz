<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rooms</title>
</head>
<body>

    <button id="createRoom">Create Room</button>
    <p id="createRoomResponse"></p>

    <input id="roomId" type="text">
    <button id="joinRoom">Join Room</button>
    <p id="joinRoomResponse"></p>


    <input id="roomIdLeave" type="text">
    <button id="leaveRoom">Leave Room</button>
    <p id="leaveRoomResponse"></p>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.7.3/socket.io.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script>


    <script>
        $(document).ready(function () {
            $('#createRoom').on('click', function(){
                $.ajax({
                    url: "/room/create",
                    type: "GET",
                    success: function (data) {
                        $('#createRoomResponse').append(data.message)
                    }
                })
            });
            $('#joinRoom').on('click', function(){
                var id = $('#roomId').val();
                $.ajax({
                    url: "/room/join/" + id + "?user_id=1",
                    type: "GET",
                    success: function (data) {
                        $('#joinRoomResponse').append(data.message)
                    }
                })
            });
            $('#leaveRoom').on('click', function(){
                var id = $('#roomIdLeave').val();
                $.ajax({
                    url: "/room/leave/" + id + "?user_id=1",
                    type: "GET",
                    success: function (data) {
                        $('#leaveRoomResponse').append(data.message)
                    }
                })
            });

        })
    </script>

    <script>
        var socket = io(':6560')
    </script>
</body>
</html>