<!-- Chat Widget Button -->
<button type="button" 
        class="chat-widget-btn" 
        id="chatWidgetBtn"
        title="Chat dengan {{ Auth::user()->isAdmin() || Auth::user()->isStaff() ? 'User' : 'Admin' }}">
    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
        <path d="M16 8c0 3.866-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.584.296-1.925.864-4.181 1.234-.2.032-.352-.176-.273-.362.354-.836.674-1.95.77-2.966C.744 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7zM7.194 6.766a1.688 1.688 0 0 0-.227-.272 1.467 1.467 0 0 0-.469-.324l-.008-.004A1.47 1.47 0 0 0 4.932 6c-.447 0-.88.13-1.246.37a.5.5 0 0 0-.154.67l.001.001c.29.466.69.83 1.15 1.054.46.224.97.29 1.45.186.48-.104.91-.35 1.24-.69.33-.34.56-.76.66-1.22.1-.46.03-.94-.19-1.37z"/>
    </svg>
    @if(Auth::check())
        @php
            $user = Auth::user();
            if ($user->isAdmin() || $user->isStaff()) {
                $unreadCount = \App\Models\Chat::where('sender', 'user')
                    ->where('is_read', false)
                    ->count();
            } else {
                $unreadCount = \App\Models\Chat::where('user_id', $user->id)
                    ->where('sender', 'admin')
                    ->where('is_read', false)
                    ->count();
            }
        @endphp
        @if($unreadCount > 0)
            <span class="chat-badge" id="chatBadge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
        @endif
    @endif
</button>

<!-- Chat Window -->
<div class="chat-window" id="chatWindow" style="display: none;">
    <div class="chat-header">
        <div class="chat-header-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path d="M16 8c0 3.866-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.584.296-1.925.864-4.181 1.234-.2.032-.352-.176-.273-.362.354-.836.674-1.95.77-2.966C.744 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7zM7.194 6.766a1.688 1.688 0 0 0-.227-.272 1.467 1.467 0 0 0-.469-.324l-.008-.004A1.47 1.47 0 0 0 4.932 6c-.447 0-.88.13-1.246.37a.5.5 0 0 0-.154.67l.001.001c.29.466.69.83 1.15 1.054.46.224.97.29 1.45.186.48-.104.91-.35 1.24-.69.33-.34.56-.76.66-1.22.1-.46.03-.94-.19-1.37z"/>
            </svg>
            <span id="chatTitle">
                @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                    Chat dengan User
                @else
                    Chat dengan Admin
                @endif
            </span>
        </div>
        <div class="chat-header-actions">
            @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
            <!-- Tombol Kembali ke Lobi untuk Admin -->
            <button type="button" class="chat-lobby-btn" id="chatLobbyBtn" title="Kembali ke Daftar User" style="display: none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="M4.407 3.607a.5.5 0 0 1 .707 0L8 6.493l2.886-2.886a.5.5 0 1 1 .708.707L8.707 7.293l2.886 2.886a.5.5 0 1 1-.708.707L8 8l-2.886 2.886a.5.5 0 1 1-.708-.707L7.293 7.293 4.407 4.314a.5.5 0 0 1 0-.707z"/>
                </svg>
            </button>
            @endif
            <button type="button" class="chat-close-btn" id="chatCloseBtn">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                </svg>
            </button>
        </div>
    </div>
    
    <!-- User List (Lobi - Only for Admin) -->
    @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
    <div class="user-list" id="userList">
        <div class="user-list-header">
            <h6 class="mb-0">
                <i class="bi bi-people me-2"></i>Daftar User
            </h6>
            <small class="text-muted">Pilih user untuk chat</small>
        </div>
        <div class="user-list-body" id="userListBody">
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Chat Messages Area -->
    <div class="chat-body" id="chatBody" style="display: none;">
        <div class="chat-messages" id="chatMessages">
            <!-- Messages will be loaded here -->
        </div>
    </div>
    
    <div class="chat-footer" id="chatFooter" style="display: none;">
        <form id="chatForm">
            @csrf
            <input type="hidden" name="user_id" id="chatUserId" value="{{ Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isStaff()) ? '' : Auth::id() }}">
            <div class="chat-input-group">
                <input type="text" 
                       name="message" 
                       id="chatMessage" 
                       class="chat-input" 
                       placeholder="Ketik pesan..." 
                       required 
                       autocomplete="off">
                <button type="submit" class="chat-send-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.023l-5.819-14.547a.5.5 0 0 1 .109-.54L8 3.427l5.854-3.281zM4.646 5.354a.5.5 0 0 0 .708 0L8 8.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 9l2.647 2.646a.5.5 0 0 1-.708.708L8 9.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 9 4.646 6.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Chat Widget Styles */
.chat-widget-btn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 50%;
    color: white;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    z-index: 9998;
}

.chat-widget-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 30px rgba(102, 126, 234, 0.7);
}

.chat-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ef4444;
    color: white;
    font-size: 12px;
    font-weight: bold;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.2); }
}

.chat-window {
    position: fixed;
    bottom: 90px;
    right: 20px;
    width: 380px;
    max-width: calc(100vw - 40px);
    height: 550px;
    max-height: calc(100vh - 110px);
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
    z-index: 9999;
    overflow: hidden;
}

.chat-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.chat-header-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    font-size: 16px;
}

.chat-header-actions {
    display: flex;
    gap: 8px;
}

.chat-lobby-btn,
.chat-close-btn {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    cursor: pointer;
    padding: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 5px;
    transition: background 0.2s;
}

.chat-lobby-btn:hover,
.chat-close-btn:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* User List (Lobi) Styles */
.user-list {
    flex: 1;
    overflow-y: auto;
    background: #f8f9fa;
}

.user-list-header {
    padding: 15px 20px;
    background: white;
    border-bottom: 1px solid #e5e7eb;
}

.user-list-header h6 {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: #333;
}

.user-list-header small {
    font-size: 12px;
}

.user-list-body {
    padding: 10px;
}

.user-item {
    padding: 12px 15px;
    background: white;
    border-radius: 10px;
    margin-bottom: 8px;
    cursor: pointer;
    transition: all 0.2s;
    border: 2px solid transparent;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.user-item:hover {
    background: #f0f9ff;
    border-color: #667eea;
    transform: translateX(5px);
}

.user-item.active {
    background: #e0f2fe;
    border-color: #667eea;
}

.user-item-info {
    flex: 1;
    overflow: hidden;
}

.user-item-name {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 2px;
    color: #333;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-item-email {
    font-size: 12px;
    color: #666;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-item-meta {
    text-align: right;
    margin-left: 10px;
}

.user-item-time {
    font-size: 11px;
    color: #999;
    margin-bottom: 4px;
}

.user-item-unread {
    background: #ef4444;
    color: white;
    font-size: 11px;
    font-weight: bold;
    padding: 2px 8px;
    border-radius: 10px;
    min-width: 20px;
    text-align: center;
    display: inline-block;
}

.user-item-unread.zero {
    display: none;
}

.chat-body {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background: #f8f9fa;
}

.chat-messages {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.chat-message {
    max-width: 85%;
    padding: 12px 16px;
    border-radius: 15px;
    word-wrap: break-word;
    animation: slideIn 0.3s ease;
    position: relative;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.chat-message.user {
    align-self: flex-end;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom-right-radius: 5px;
}

.chat-message.admin {
    align-self: flex-start;
    background: white;
    color: #333;
    border-bottom-left-radius: 5px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.chat-message-sender {
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 4px;
    opacity: 0.9;
}

.chat-message.user .chat-message-sender {
    color: rgba(255, 255, 255, 0.9);
}

.chat-message.admin .chat-message-sender {
    color: #667eea;
}

.chat-message-text {
    font-size: 14px;
    line-height: 1.4;
    margin-bottom: 4px;
}

.chat-message-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 4px;
}

.chat-message-time {
    font-size: 11px;
    opacity: 0.7;
}

.chat-footer {
    padding: 15px 20px;
    background: white;
    border-top: 1px solid #e5e7eb;
}

.chat-input-group {
    display: flex;
    gap: 10px;
}

.chat-input {
    flex: 1;
    padding: 10px 15px;
    border: 2px solid #e5e7eb;
    border-radius: 25px;
    outline: none;
    transition: border-color 0.2s;
}

.chat-input:focus {
    border-color: #667eea;
}

.chat-send-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s;
}

.chat-send-btn:hover {
    transform: scale(1.1);
}

.chat-send-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #999;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
}

.empty-state p {
    margin: 0;
    font-size: 14px;
}

/* Responsive */
@media (max-width: 768px) {
    .chat-window {
        bottom: 80px;
        right: 10px;
        left: 10px;
        width: auto;
        max-width: none;
        height: calc(100vh - 100px);
    }
    
    .chat-widget-btn {
        bottom: 15px;
        right: 15px;
        width: 55px;
        height: 55px;
    }
}

/* Scrollbar */
.chat-body::-webkit-scrollbar,
.user-list::-webkit-scrollbar {
    width: 6px;
}

.chat-body::-webkit-scrollbar-track,
.user-list::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.chat-body::-webkit-scrollbar-thumb,
.user-list::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.chat-body::-webkit-scrollbar-thumb:hover,
.user-list::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>

<script>
// Chat Widget Functionality
document.addEventListener('DOMContentLoaded', function() {
    const chatWidgetBtn = document.getElementById('chatWidgetBtn');
    const chatWindow = document.getElementById('chatWindow');
    const chatCloseBtn = document.getElementById('chatCloseBtn');
    const chatLobbyBtn = document.getElementById('chatLobbyBtn');
    const chatForm = document.getElementById('chatForm');
    const chatMessages = document.getElementById('chatMessages');
    const chatBody = document.getElementById('chatBody');
    const chatFooter = document.getElementById('chatFooter');
    const userList = document.getElementById('userList');
    const userListBody = document.getElementById('userListBody');
    const chatMessageInput = document.getElementById('chatMessage');
    const chatUserIdInput = document.getElementById('chatUserId');
    const chatTitle = document.getElementById('chatTitle');
    const chatBadge = document.getElementById('chatBadge');
    
    const isUser = {{ Auth::user()->isAdmin() || Auth::user()->isStaff() ? 'false' : 'true' }};
    const isAdmin = {{ Auth::user()->isAdmin() || Auth::user()->isStaff() ? 'true' : 'false' }};
    let selectedUserId = null;
    let selectedUserName = '';

    // Toggle chat window
    chatWidgetBtn.addEventListener('click', function() {
        if (chatWindow.style.display === 'none') {
            chatWindow.style.display = 'flex';
            if (isAdmin) {
                showUserList();
            } else {
                showChatArea();
                loadMessages();
                markAllAsRead();
            }
        } else {
            chatWindow.style.display = 'none';
        }
    });

    chatCloseBtn.addEventListener('click', function() {
        chatWindow.style.display = 'none';
    });

    // Show User List (Lobi) - Admin Only
    function showUserList() {
        if (userList) {
            userList.style.display = 'block';
            chatBody.style.display = 'none';
            chatFooter.style.display = 'none';
            if (chatLobbyBtn) chatLobbyBtn.style.display = 'none';
            if (chatTitle) chatTitle.textContent = 'Chat dengan User';
            loadUserList();
        }
    }

    // Show Chat Area
    function showChatArea() {
        if (userList) userList.style.display = 'none';
        chatBody.style.display = 'block';
        chatFooter.style.display = 'block';
        if (chatLobbyBtn) chatLobbyBtn.style.display = 'block';
    }

    // Back to Lobby - Admin Only
    if (chatLobbyBtn) {
        chatLobbyBtn.addEventListener('click', function() {
            showUserList();
            selectedUserId = null;
            selectedUserName = '';
        });
    }

    // Load User List (for Admin)
    function loadUserList() {
        if (!isAdmin) return;
        
        fetch('{{ route("chat.users") }}')
            .then(response => response.json())
            .then(data => {
                if (userListBody && data.users && data.users.length > 0) {
                    userListBody.innerHTML = '';
                    data.users.forEach(user => {
                        const userItem = document.createElement('div');
                        userItem.className = 'user-item';
                        if (selectedUserId === user.user_id) {
                            userItem.classList.add('active');
                        }
                        userItem.onclick = () => selectUser(user.user_id, user.name);
                        
                        userItem.innerHTML = `
                            <div class="user-item-info">
                                <div class="user-item-name">${escapeHtml(user.name)}</div>
                                <div class="user-item-email">${escapeHtml(user.email)}</div>
                            </div>
                            <div class="user-item-meta">
                                ${user.last_message ? `<div class="user-item-time">${user.last_message}</div>` : ''}
                                ${user.unread_count > 0 ? `<div class="user-item-unread">${user.unread_count > 9 ? '9+' : user.unread_count}</div>` : '<div class="user-item-unread zero">0</div>'}
                            </div>
                        `;
                        
                        userListBody.appendChild(userItem);
                    });
                } else if (userListBody) {
                    userListBody.innerHTML = `
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>Belum ada user yang chat</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading user list:', error);
                if (userListBody) {
                    userListBody.innerHTML = `
                        <div class="empty-state">
                            <i class="bi bi-exclamation-triangle text-danger"></i>
                            <p class="text-danger">Gagal memuat daftar user</p>
                        </div>
                    `;
                }
            });
    }

    // Select User (for Admin)
    function selectUser(userId, userName) {
        selectedUserId = userId;
        selectedUserName = userName;
        chatUserIdInput.value = userId;
        
        // Update title
        if (chatTitle) {
            chatTitle.textContent = 'Chat dengan ' + userName;
        }
        
        // Show chat area
        showChatArea();
        
        // Load messages for selected user
        loadMessages();
        
        // Mark as read
        markAsRead(userId);
        
        // Refresh user list to update active state
        loadUserList();
    }

    // Load messages
    function loadMessages() {
        const params = new URLSearchParams();
        if (selectedUserId) {
            params.append('user_id', selectedUserId);
        }
        
        fetch('{{ route("chat.messages") }}' + (params.toString() ? '?' + params.toString() : ''))
            .then(response => response.json())
            .then(data => {
                chatMessages.innerHTML = '';
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        appendMessage(msg);
                    });
                    scrollToBottom();
                } else {
                    chatMessages.innerHTML = `
                        <div class="empty-state">
                            <i class="bi bi-chat-text"></i>
                            <p>Belum ada pesan. Mulai percakapan!</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading messages:', error);
                chatMessages.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-exclamation-triangle text-danger"></i>
                        <p class="text-danger">Gagal memuat pesan</p>
                    </div>
                `;
            });
    }

    // Append message to chat
    function appendMessage(msg) {
        const div = document.createElement('div');
        div.className = `chat-message ${msg.sender}`;
        
        const time = new Date(msg.created_at);
        const timeString = time.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});
        
        // Determine sender name
        let senderName = '';
        if (msg.sender === 'user') {
            senderName = msg.user_name || 'User';
        } else {
            senderName = msg.admin_name || 'Admin';
        }
        
        div.innerHTML = `
            <div class="chat-message-sender">${escapeHtml(senderName)}</div>
            <div class="chat-message-text">${escapeHtml(msg.message)}</div>
            <div class="chat-message-footer">
                <span class="chat-message-time">${timeString}</span>
            </div>
        `;
        chatMessages.appendChild(div);
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // Scroll to bottom
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Mark as read
    function markAsRead(userId) {
        fetch('{{ route("chat.mark-read") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ user_id: userId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateUnreadCount();
                // Reload user list to update unread count
                if (isAdmin) loadUserList();
            }
        })
        .catch(error => console.error('Error marking as read:', error));
    }

    // Mark all as read (for user)
    function markAllAsRead() {
        if (isAdmin) return;
        
        fetch('{{ route("chat.mark-read") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateUnreadCount();
            }
        })
        .catch(error => console.error('Error marking as read:', error));
    }

    // Handle form submit
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = chatForm.querySelector('.chat-send-btn');
        const messageInput = chatForm.querySelector('#chatMessage');
        const message = messageInput.value.trim();
        
        if (!message) return;
        
        // Check if admin has selected user
        if (isAdmin && !selectedUserId) {
            alert('Silakan pilih user terlebih dahulu!');
            showUserList();
            return;
        }
        
        // Disable button while sending
        submitBtn.disabled = true;
        
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('message', message);
        
        // If admin, include user_id
        const userId = chatUserIdInput.value;
        if (userId) {
            formData.append('user_id', userId);
        }
        
        fetch('{{ route("chat.store") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageInput.value = '';
                loadMessages();
                updateUnreadCount();
                
                // Reload user list for admin to update last message time
                if (isAdmin) {
                    loadUserList();
                }
            } else {
                alert(data.message || 'Gagal mengirim pesan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan. Coba lagi.');
        })
        .finally(() => {
            submitBtn.disabled = false;
            messageInput.focus();
        });
    });

    // Update unread count
    function updateUnreadCount() {
        fetch('{{ route("chat.unread") }}')
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    if (!chatBadge) {
                        const newBadge = document.createElement('span');
                        newBadge.className = 'chat-badge';
                        newBadge.id = 'chatBadge';
                        newBadge.textContent = data.count > 9 ? '9+' : data.count;
                        chatWidgetBtn.appendChild(newBadge);
                    } else {
                        chatBadge.textContent = data.count > 9 ? '9+' : data.count;
                        chatBadge.style.display = 'flex';
                    }
                } else if (chatBadge) {
                    chatBadge.style.display = 'none';
                }
            })
            .catch(error => console.error('Error updating unread count:', error));
    }

    // Poll for new messages every 5 seconds
    setInterval(() => {
        if (chatWindow.style.display !== 'none') {
            if (isAdmin && selectedUserId) {
                loadMessages();
            } else if (!isAdmin && chatBody.style.display !== 'none') {
                loadMessages();
            }
        }
        updateUnreadCount();
        
        // Reload user list for admin
        if (isAdmin && userList && userList.style.display !== 'none') {
            loadUserList();
        }
    }, 5000);
    
    // Initial load
    updateUnreadCount();
});
</script>