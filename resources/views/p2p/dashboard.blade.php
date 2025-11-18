@extends('layouts.app')

@section('title', 'P2P Dashboard')

@section('content')
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">P2P Communication Dashboard</h1>
                    <p class="text-gray-600 mt-2">Welcome, {{ $userPin->username ?? 'Anonymous' }}!</p>
                </div>
                <form action="{{ route('p2p.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                        Logout
                    </button>
                </form>
            </div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-blue-800">Session ID</h3>
                    <p class="text-sm font-mono text-blue-600">{{ $userPin->session_id }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-green-800">IP Address</h3>
                    <p class="text-sm font-mono text-green-600">{{ $userPin->ip_address }}</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-purple-800">Status</h3>
                    <p class="text-sm font-semibold text-purple-600">{{ $userPin->is_active ? 'Active' : 'Inactive' }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Messaging Section -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">💬 Messaging</h2>

                <!-- Connection Form -->
                <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-semibold mb-2">Connect to Peer</h3>
                    <form id="connectForm" class="space-y-3">
                        @csrf
                        <input type="text" id="peerSessionId" placeholder="Masukkan Session ID peer"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg">
                            Connect
                        </button>
                    </form>
                </div>

                <!-- Message Display Area -->
                <div class="mb-4">
                    <div id="messagesContainer"
                        class="h-64 overflow-y-auto border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <p class="text-gray-500 text-center">Connect to a peer to start messaging...</p>
                    </div>
                </div>

                <!-- Message Input -->
                <form id="messageForm" class="flex space-x-2">
                    @csrf
                    <input type="hidden" id="receiverSessionId" name="receiver_session_id">
                    <input type="text" id="messageInput" name="message" placeholder="Type your message..."
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        disabled>
                    <button type="submit" id="sendButton"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg disabled:bg-gray-400"
                        disabled>
                        Send
                    </button>
                </form>
            </div>

            <!-- File Transfer Section -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">📁 File Transfer</h2>

                <!-- File Upload Area -->
                <div class="mb-4">
                    <div id="dropZone"
                        class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-500 transition-colors">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" stroke="currentColor" fill="none"
                            viewBox="0 0 48 48">
                            <path
                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p class="text-gray-600">Drag & drop files here, or
                            <button type="button" id="browseFiles"
                                class="text-blue-600 hover:text-blue-800 font-medium">browse</button>
                        </p>
                        <p class="text-sm text-gray-500 mt-1">Images only (max 10MB)</p>
                        <input type="file" id="fileInput" accept="image/*" class="hidden">
                    </div>
                </div>

                <!-- File Upload Form -->
                <form id="fileUploadForm" class="mb-4" style="display: none;">
                    @csrf
                    <input type="hidden" id="fileReceiverSessionId" name="receiver_session_id">
                    <div class="flex items-center space-x-2">
                        <span id="selectedFileName" class="flex-1 text-sm text-gray-600"></span>
                        <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">
                            Send File
                        </button>
                        <button type="button" id="cancelFileUpload"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                            Cancel
                        </button>
                    </div>
                </form>

                <!-- Recent File Transfers -->
                <div>
                    <h3 class="font-semibold mb-2">Recent Transfers</h3>
                    <div id="recentFiles" class="space-y-2 max-h-40 overflow-y-auto">
                        <p class="text-gray-500 text-sm">No recent transfers...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Network Analysis Info -->
        <div class="mt-6 bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">🔍 Network Analysis Info</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Protocol Information:</h3>
                    <ul class="space-y-1 text-gray-600">
                        <li>• Transport Layer: TCP (HTTP/HTTPS)</li>
                        <li>• Application Layer: HTTP/WebSocket</li>
                        <li>• Data Format: JSON payloads</li>
                        <li>• Encryption: HTTPS (TLS/SSL)</li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Wireshark Analysis:</h3>
                    <ul class="space-y-1 text-gray-600">
                        <li>• Monitor pada interface aktif</li>
                        <li>• Filter: <code class="bg-gray-100 px-1 rounded">http</code> atau <code
                                class="bg-gray-100 px-1 rounded">tcp.port == 8000</code></li>
                        <li>• Perhatikan TCP handshake</li>
                        <li>• Analisis HTTP request/response</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let connectedPeerSessionId = null;
            let currentUserSessionId = '{{ $userPin->session_id }}';

            // Connection handling
            document.getElementById('connectForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const peerSessionId = document.getElementById('peerSessionId').value.trim();

                if (peerSessionId) {
                    connectedPeerSessionId = peerSessionId;
                    document.getElementById('receiverSessionId').value = peerSessionId;
                    document.getElementById('fileReceiverSessionId').value = peerSessionId;
                    document.getElementById('messageInput').disabled = false;
                    document.getElementById('sendButton').disabled = false;

                    document.getElementById('messagesContainer').innerHTML =
                        '<p class="text-green-600 text-center">Connected to peer: ' + peerSessionId + '</p>';

                    // Start polling for messages
                    pollMessages();
                }
            });

            // Message sending
            document.getElementById('messageForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const message = document.getElementById('messageInput').value.trim();

                if (message && connectedPeerSessionId) {
                    fetch('{{ route('messages.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                receiver_session_id: connectedPeerSessionId,
                                message: message
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                appendMessage('You', message, 'sent');
                                document.getElementById('messageInput').value = '';
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            });

            // File handling
            document.getElementById('browseFiles').addEventListener('click', function() {
                document.getElementById('fileInput').click();
            });

            document.getElementById('fileInput').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    document.getElementById('selectedFileName').textContent = file.name;
                    document.getElementById('fileUploadForm').style.display = 'block';
                }
            });

            document.getElementById('cancelFileUpload').addEventListener('click', function() {
                document.getElementById('fileInput').value = '';
                document.getElementById('fileUploadForm').style.display = 'none';
            });

            // Drag and drop
            const dropZone = document.getElementById('dropZone');
            dropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('border-blue-500');
            });

            dropZone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('border-blue-500');
            });

            dropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('border-blue-500');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const file = files[0];
                    if (file.type.startsWith('image/')) {
                        document.getElementById('fileInput').files = files;
                        document.getElementById('selectedFileName').textContent = file.name;
                        document.getElementById('fileUploadForm').style.display = 'block';
                    }
                }
            });

            function appendMessage(sender, message, type) {
                const container = document.getElementById('messagesContainer');
                const messageDiv = document.createElement('div');
                messageDiv.className = `mb-2 p-2 rounded ${type === 'sent' ? 'bg-blue-100 ml-8' : 'bg-gray-100 mr-8'}`;
                messageDiv.innerHTML = `<strong>${sender}:</strong> ${message}`;
                container.appendChild(messageDiv);
                container.scrollTop = container.scrollHeight;
            }

            function pollMessages() {
                if (!connectedPeerSessionId) return;

                setInterval(() => {
                    fetch('{{ route('messages.fetch') }}?peer_session_id=' + connectedPeerSessionId)
                        .then(response => response.json())
                        .then(data => {
                            // Handle new messages
                            data.messages.forEach(msg => {
                                if (msg.sender_session_id !== currentUserSessionId) {
                                    appendMessage('Peer', msg.message, 'received');
                                }
                            });
                        })
                        .catch(error => console.error('Error polling messages:', error));
                }, 2000); // Poll every 2 seconds
            }
        </script>
    @endpush
@endsection
