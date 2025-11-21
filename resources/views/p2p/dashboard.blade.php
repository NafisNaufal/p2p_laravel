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
                    <h3 class="font-semibold mb-2">Files Sent by Me</h3>
                    <div id="recentFiles" class="space-y-2 max-h-40 overflow-y-auto">
                        <p class="text-gray-500 text-sm">No files sent yet...</p>
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
            let displayedMessageIds = new Set(); // Track displayed messages
            let pollingInterval = null;

            // Global event listener for delete buttons
            document.addEventListener('click', function(e) {
                console.log('Click detected on:', e.target.className, e.target.tagName);

                if (e.target.classList.contains('delete-btn')) {
                    e.preventDefault();
                    const messageId = e.target.getAttribute('data-message-id');
                    const deleteType = e.target.getAttribute('data-delete-type');
                    console.log('Delete button clicked via event listener:', {
                        messageId,
                        deleteType,
                        element: e.target
                    });
                    deleteMessage(messageId, deleteType);
                } else {
                    console.log('Click was not on delete button');
                }
            });

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

                    // Clear displayed messages tracking
                    displayedMessageIds.clear();

                    // Clear existing polling interval
                    if (pollingInterval) {
                        clearInterval(pollingInterval);
                    }

                    // Start polling for messages
                    pollMessages();

                    // Load recent files after connection
                    setTimeout(loadRecentFiles, 500);
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
                                // Track this message as displayed to prevent duplication in polling
                                displayedMessageIds.add(data.message.id);
                                const currentUsername = '{{ $userPin->username ?? 'You' }}';
                                appendMessage(currentUsername, message, 'sent', data.message);
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

            // File upload form submission
            document.getElementById('fileUploadForm').addEventListener('submit', function(e) {
                e.preventDefault();

                if (!connectedPeerSessionId) {
                    alert('Please connect to a peer first');
                    return;
                }

                const fileInput = document.getElementById('fileInput');
                const file = fileInput.files[0];

                if (!file) {
                    alert('Please select a file');
                    return;
                }

                const formData = new FormData();
                formData.append('file', file);
                formData.append('receiver_session_id', connectedPeerSessionId);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                // Show uploading status
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Uploading...';
                submitBtn.disabled = true;

                fetch('{{ route('files.upload') }}', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('File sent successfully!');
                            document.getElementById('fileInput').value = '';
                            document.getElementById('fileUploadForm').style.display = 'none';
                            loadRecentFiles();
                        } else {
                            alert('Failed to send file: ' + (data.error || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to send file');
                    })
                    .finally(() => {
                        submitBtn.textContent = originalText;
                        submitBtn.disabled = false;
                    });
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

            function appendMessage(sender, message, type, messageData = null) {
                const container = document.getElementById('messagesContainer');
                const messageDiv = document.createElement('div');
                messageDiv.className = `mb-2 p-2 rounded ${type === 'sent' ? 'bg-blue-100 ml-8' : 'bg-gray-100 mr-8'} relative`;

                // Add unique ID for easier identification
                if (messageData && messageData.id) {
                    messageDiv.setAttribute('data-message-id', messageData.id);
                }
                messageDiv.onmouseenter = function() {
                    const deleteButtons = this.querySelector('.delete-buttons');
                    if (deleteButtons) deleteButtons.style.opacity = '1';
                };
                messageDiv.onmouseleave = function() {
                    const deleteButtons = this.querySelector('.delete-buttons');
                    if (deleteButtons) deleteButtons.style.opacity = '0.3';
                };

                let messageContent = `<strong>${sender}:</strong> ${message}`;

                // Add download link for file messages
                if (messageData && messageData.file_download_url) {
                    let downloadUrl = messageData.file_download_url;
                    
                    // Handle both relative and absolute URLs
                    if (downloadUrl.startsWith('http')) {
                        // If it's an absolute URL (legacy data), replace the origin with current one
                        try {
                            const urlObj = new URL(downloadUrl);
                            downloadUrl = window.location.origin + urlObj.pathname;
                        } catch (e) {
                            console.error('Invalid URL:', downloadUrl);
                            // Fallback to original if parsing fails, though likely broken
                        }
                    } else if (!downloadUrl.startsWith('/')) {
                         // Ensure relative path starts with /
                         downloadUrl = '/' + downloadUrl;
                    }
                    
                    // If it's a relative path (new data), prepend origin (or let browser handle it)
                    // We'll just use the relative path directly in href, browser handles the rest
                    
                    messageContent += ` <a href="${downloadUrl}" class="text-blue-600 hover:text-blue-800 underline ml-2" download>[Download]</a>`;
                }

                // Add delete options (only show on hover)
                let deleteOptions = '';
                if (messageData && messageData.id) {
                    const currentSessionId = '{{ session('user_session_id') }}';
                    const canDeleteForEveryone = messageData.sender_session_id === currentSessionId;

                    // Debug logging
                    ('Delete button check:', {
                        messageId: messageData.id,
                        messageSender: messageData.sender_session_id,
                        messageSenderLength: messageData.sender_session_id ? messageData.sender_session_id.length : 'null',
                        currentSession: currentSessionId,
                        currentSessionLength: currentSessionId ? currentSessionId.length : 'null',
                        exactMatch: messageData.sender_session_id === currentSessionId,
                        canDeleteForEveryone: canDeleteForEveryone,
                        hasMessageId: !!messageData.id
                    });

                    deleteOptions = `
                        <div class="absolute top-1 right-1 flex space-x-1 delete-buttons" style="opacity: 0.3; transition: opacity 0.2s;">
                            <button data-message-id="${messageData.id}" data-delete-type="me" class="delete-btn text-xs bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600" title="Delete for me">
                                🗑️
                            </button>
                            ${canDeleteForEveryone ? `<button data-message-id="${messageData.id}" data-delete-type="everyone" class="delete-btn text-xs bg-red-700 text-white px-2 py-1 rounded hover:bg-red-800" title="Delete for everyone">❌</button>` : ''}
                        </div>
                    `;
                }

                messageDiv.innerHTML = messageContent + deleteOptions;
                container.appendChild(messageDiv);
                container.scrollTop = container.scrollHeight;
            }

            function pollMessages() {
                if (!connectedPeerSessionId) return;

                pollingInterval = setInterval(() => {
                    fetch('{{ route('messages.fetch') }}?peer_session_id=' + connectedPeerSessionId)
                        .then(response => response.json())
                        .then(data => {
                            // Get IDs of messages that still exist in the database
                            const existingMessageIds = new Set(data.messages.map(msg => msg.id));

                            // Remove messages from UI that no longer exist in database (deleted for everyone)
                            displayedMessageIds.forEach(msgId => {
                                if (!existingMessageIds.has(msgId)) {
                                    const messageElement = document.querySelector(
                                        `[data-message-id="${msgId}"]`);
                                    if (messageElement) {
                                        console.log('Removing deleted message from UI:', msgId);
                                        messageElement.remove();
                                    }
                                    displayedMessageIds.delete(msgId);
                                }
                            });

                            // Handle new messages - only append messages we haven't displayed yet
                            data.messages.forEach(msg => {
                                if (!displayedMessageIds.has(msg.id)) {
                                    displayedMessageIds.add(msg.id);

                                    if (msg.sender_session_id === currentUserSessionId) {
                                        // Our own message (shouldn't happen in polling, but just in case)
                                        const senderName = msg.sender ? msg.sender.username : 'You';
                                        appendMessage(senderName, msg.message, 'sent', msg);
                                    } else {
                                        // Peer's message or file notification
                                        const peerName = msg.sender ? msg.sender.username : 'Peer';
                                        appendMessage(peerName, msg.message, 'received', msg);
                                    }
                                }
                            });
                        })
                        .catch(error => console.error('Error polling messages:', error));

                    // Also refresh file list to catch new file transfers
                    loadRecentFiles();
                }, 2000); // Poll every 2 seconds
            }

            function loadRecentFiles() {
                const container = document.getElementById('recentFiles');
                if (!container) {
                    return; // Silently return if not on dashboard
                }

                // Build URL with peer_session_id parameter if connected
                let url = '{{ route('files.index') }}';
                if (connectedPeerSessionId) {
                    url += '?peer_session_id=' + connectedPeerSessionId;
                }

                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to fetch files');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.files && Array.isArray(data.files) && data.files.length > 0) {
                            container.innerHTML = data.files.map(file => {
                                // Ensure file has required properties
                                if (!file.id || !file.original_name) return '';

                                const downloadUrl = window.location.origin + '/files/' + file.id + '/download';
                                // Only show sent files (received files appear as chat notifications)
                                const transferType = 'Sent to';
                                const peerName = file.receiver ? file.receiver.username : 'Unknown';

                                return `
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium">${file.original_name}</p>
                                            <p class="text-xs text-gray-500">${formatFileSize(file.file_size || 0)} • ${transferType} ${peerName}</p>
                                            <p class="text-xs text-gray-400">${new Date(file.created_at).toLocaleString()}</p>
                                        </div>
                                        <a href="${downloadUrl}" class="text-blue-600 hover:text-blue-800 text-sm">Download</a>
                                    </div>
                                `;
                            }).filter(html => html !== '').join('');
                        } else {
                            container.innerHTML = '<p class="text-gray-500 text-sm">No files sent yet...</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading files:', error);
                        container.innerHTML = '<p class="text-red-500 text-sm">Error loading files...</p>';
                    });
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            function deleteMessage(messageId, deleteType) {
                console.log('deleteMessage called with:', {
                    messageId,
                    deleteType
                });

                const confirmMessage = deleteType === 'everyone' ?
                    'Delete this message for everyone? This cannot be undone.' :
                    'Delete this message for yourself?';

                console.log('Showing confirm dialog...');
                const confirmed = confirm(confirmMessage);
                console.log('User confirmed:', confirmed);

                if (!confirmed) {
                    console.log('User cancelled delete');
                    return;
                }

                const endpoint = deleteType === 'everyone' ?
                    `/messages/${messageId}/delete-for-everyone` :
                    `/messages/${messageId}/delete-for-me`;

                console.log('Attempting to delete message:', {
                    messageId,
                    deleteType,
                    endpoint
                });

                fetch(endpoint, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log('Delete response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Delete response data:', data);
                        if (data.success) {
                            console.log('Delete successful!');

                            // Remove message from UI immediately for both types
                            const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
                            if (messageElement) {
                                console.log('Removing message element from DOM');
                                messageElement.remove();
                                displayedMessageIds.delete(messageId);
                            }

                            // For "delete for everyone", polling will also remove it from peer's UI automatically
                            if (deleteType === 'everyone') {
                                console.log('Message deleted for everyone - peer will see update via polling');
                            } else {
                                console.log('Message deleted for me only - marked in database');
                            }

                            console.log('Delete operation completed successfully!');
                        } else {
                            console.error('Delete failed:', data);
                            alert('Failed to delete message: ' + (data.error || 'Unknown error'));
                            if (data.debug) {
                                console.log('Debug info:', data.debug);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting message:', error);
                        alert('Failed to delete message: ' + error.message);
                    });
            }

            function refreshMessages() {
                if (!connectedPeerSessionId) return;

                // Clear displayed messages and reload
                displayedMessageIds.clear();
                document.getElementById('messagesContainer').innerHTML =
                    '<p class="text-green-600 text-center">Connected to peer: ' + connectedPeerSessionId + '</p>';

                fetch('{{ route('messages.fetch') }}?peer_session_id=' + connectedPeerSessionId)
                    .then(response => response.json())
                    .then(data => {
                        data.messages.forEach(msg => {
                            displayedMessageIds.add(msg.id);

                            if (msg.sender_session_id === currentUserSessionId) {
                                const senderName = msg.sender ? msg.sender.username : 'You';
                                appendMessage(senderName, msg.message, 'sent', msg);
                            } else {
                                const peerName = msg.sender ? msg.sender.username : 'Peer';
                                appendMessage(peerName, msg.message, 'received', msg);
                            }
                        });
                    })
                    .catch(error => console.error('Error refreshing messages:', error));
            }
        </script>
    @endpush
@endsection
