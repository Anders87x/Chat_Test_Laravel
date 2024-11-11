<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat en Tiempo Real</title>
    @vite('resources/js/app.js')
</head>
<body>
    <h1>Chat en Tiempo Real</h1>
    <div id="chat-box" style="border: 1px solid #ccc; padding: 10px; height: 300px; overflow-y: scroll;"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.Echo.channel('chat')
                .listen('.message.sent', (e) => {
                    console.log('Evento recibido:', e);
                    const messageElement = document.createElement('p');
                    messageElement.textContent = `${e.username}: ${e.message}`;
                    document.getElementById('chat-box').appendChild(messageElement);
                });
        });
    </script>
</body>
</html>
