<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Lista de Usuarios</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/js/app.js')
</head>
<body>
    <div class="container">
        <h1>Lista de Usuarios</h1>

        <form id="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Cerrar sesión</button>
        </form>

        <ul id="user-list">
            @foreach ($users as $user)
                <li id="user-{{ $user->id }}">
                    <a href="#" onclick="loadChat({{ $user->id }}, '{{ $user->name }}')">{{ $user->name }}</a>
                    @if ($user->is_online)
                        <span style="color: green;">(Online)</span>
                    @endif
                </li>
            @endforeach
        </ul>

        <div id="chat-container" >
            <h2>Chat con <span id="chat-username"></span></h2>
            <div id="chat-messages" style="border: 1px solid #ccc; padding: 10px; height: 300px; overflow-y: auto;">
                <!-- Mensajes cargados dinámicamente -->
            </div>
            <input type="text" id="chat-input" placeholder="Escribe un mensaje..." style="width: 80%;">
            <button id="send-message" onclick="sendMessage()">Enviar</button>
        </div>

    </div>

    <script>
        let currentReceiverId  = null;
        const currentUserId = {{ auth()->id() }};

        function loadChat(userId, userName) {
            currentReceiverId = userId;

            console.log('Cargando chat con:', userName, 'ID del usuario:', currentReceiverId);

            document.getElementById('chat-container').style.display = 'block';
            document.getElementById('chat-username').innerText = userName;

            fetch(`/chat/${userId}`)
                .then(response => response.json()) // Convierte la respuesta a JSON
                .then(data => {
                    console.log('Mensajes:', data.messages); // Imprime específicamente la propiedad `messages`

                    const chatMessages = document.getElementById('chat-messages');
                    chatMessages.innerHTML = ''; // Limpiar los mensajes anteriores
                    data.messages.forEach(message => {
                        const li = document.createElement('div');
                        li.textContent = `${message.user.name}: ${message.content}`;
                        chatMessages.appendChild(li);
                    });

                    chatMessages.scrollTop = chatMessages.scrollHeight; // Desplazar al último mensaje

                    if (window.currentChatChannel) {
                        window.currentChatChannel.stopListening('.message.sent');
                    }

                    console.log('Laravel Echo conectado:', window.Echo.connector.pusher.connection.state);

                    window.currentChatChannel = window.Echo.private(`chat.${currentReceiverId}`)
                        .listen('.message.sent', (e) => {
                            console.log('Evento recibido:', e); // Asegúrate de que este log aparezca
                            const li = document.createElement('div');
                            li.textContent = `${e.user_id === currentUserId ? 'Tú' : e.user_name}: ${e.content}`;
                            chatMessages.appendChild(li);
                            chatMessages.scrollTop = chatMessages.scrollHeight;
                        })
                        .error((error) => {
                            console.error('Error al suscribirse al canal:', error);
                        });

                });
        }

        function sendMessage() {

            const input = document.getElementById('chat-input');
            const content = input.value;

            if (content.trim() === '') return;

            fetch('/send-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ content: content, receiver_id: currentReceiverId }) // Pasa `receiver_id` en lugar de `chat_id`
            }).then(response => response.json())
            .then(data => {
                console.log(data);
                input.value = ''; // Limpiar el input después de enviar
            });
        }

        document.addEventListener('DOMContentLoaded', function () {

            console.log('Laravel Echo conectado:', window.Echo);

            window.Echo.channel('user-status')
                .listen('.UserStatusChanged', (e) => {
                    /* console.log('Evento recibido:', e); */
                    const userElement = document.getElementById(`user-${e.id}`);
                    if (userElement) {
                        if (e.is_online) {
                            userElement.innerHTML = `${e.name} <span style="color: green;">(Online)</span>`;
                        } else {
                            userElement.innerHTML = e.name;
                        }
                    }
                });

        });
    </script>
</body>
</html>
