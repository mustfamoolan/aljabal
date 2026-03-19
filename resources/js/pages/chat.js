import { db, auth } from '../firebase/config.js';
import { signInWithCustomToken } from "firebase/auth";
import { 
    collection, 
    query, 
    where, 
    orderBy, 
    onSnapshot, 
    addDoc, 
    updateDoc,
    setDoc,
    deleteDoc,
    doc, 
    getDoc,
    serverTimestamp,
    increment,
    arrayUnion
} from "firebase/firestore";

class AdminChat {
    constructor() {
        this.activeChatId = null;
        this.currentUid = null;
        this.chats = [];
        this.unsubscribeChats = null;
        this.unsubscribeMessages = null;
        this.allReps = [];

        this.init();
    }

    async init() {
        // Show loading state initially
        console.log('Chat Initializing...');
        this.loadRepresentatives(); 
        this.loadStaff(); 
        
        try {
            const tokenResponse = await fetch('/admin/chat/firebase-token');
            const data = await tokenResponse.json();
            
            if (data.token) {
                const userCredential = await signInWithCustomToken(auth, data.token);
                this.currentUid = 'u_' + data.uid; // Unified ID with prefix
                console.log('Logged into Firebase as:', this.currentUid);
                
                this.listenToChats();
                this.bindEvents();
            } else {
                console.error('No Firebase token received');
                this.showError('خطأ في الاتصال بنظام الرسائل');
            }
        } catch (error) {
            console.error('Chat Init Error:', error);
            this.showError('حدث خطأ أثناء تحميل نظام الدردشة');
        }
    }

    showError(msg) {
        const list = document.getElementById('conversations-list');
        if (list) list.innerHTML = `<div class="text-center p-4 text-danger">${msg}</div>`;
    }

    listenToChats() {
        const chatsRef = collection(db, 'chats');
        // Use only arrayContains without orderBy to avoid requiring a composite index
        const q = query(
            chatsRef, 
            where('participants', 'array-contains', this.currentUid)
        );

        this.unsubscribeChats = onSnapshot(q, (snapshot) => {
            this.chats = snapshot.docs
                .map(doc => ({ id: doc.id, ...doc.data() }))
                .filter(chat => {
                    const deletedBy = chat.deleted_by || [];
                    return !deletedBy.includes(this.currentUid);
                })
                .sort((a, b) => {
                    const aTime = a.last_time?.seconds || 0;
                    const bTime = b.last_time?.seconds || 0;
                    return bTime - aTime;
                });
            this.renderChatList();
        });
    }

    renderChatList() {
        const listContainer = document.getElementById('conversations-list');
        if (!listContainer) return;

        if (this.chats.length === 0) {
            listContainer.innerHTML = '<div class="text-center p-4 text-muted">لا يوجد محادثات سابقة</div>';
            return;
        }

        listContainer.innerHTML = this.chats.map(chat => {
            const otherParticipantId = chat.participants.find(p => p !== this.currentUid);
            const name = chat.names ? chat.names[otherParticipantId] : 'مستخدم';
            const isActive = this.activeChatId === chat.id ? 'active' : '';
            const unread = chat[`unread_${this.currentUid}`] || 0;
            const lastMsg = chat.last_message || 'محادثة جديدة';
            
            return `
                <div class="chat-item ${isActive}" onclick="window.chatApp.selectChat('${chat.id}')">
                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(name)}" class="chat-avatar">
                    <div class="chat-info">
                        <div class="d-flex justify-content-between">
                            <div class="chat-name">${name}</div>
                            ${unread > 0 ? `<span class="unread-badge">${unread}</span>` : ''}
                        </div>
                        <div class="chat-last-msg">${lastMsg}</div>
                    </div>
                </div>
            `;
        }).join('');
    }

    selectChat(chatId) {
        if (this.activeChatId === chatId) return;
        
        this.activeChatId = chatId;
        const chat = this.chats.find(c => c.id === chatId);
        if (!chat) return;

        // Update UI
        document.getElementById('active-chat-header').style.display = 'flex';
        document.getElementById('input-area').style.display = 'block';
        
        const otherParticipantId = chat.participants.find(p => p !== this.currentUid);
        const name = chat.names ? chat.names[otherParticipantId] : 'مستخدم';
        
        document.getElementById('active-user-name').innerText = name;
        document.getElementById('active-user-avatar').src = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}`;
        
        // Mark as read
        const chatRef = doc(db, 'chats', chatId);
        updateDoc(chatRef, {
            [`unread_${this.currentUid}`]: 0
        });

        this.listenToMessages(chatId);
        this.renderChatList(); // Refresh active state
    }

    listenToMessages(chatId) {
        if (this.unsubscribeMessages) this.unsubscribeMessages();

        const messagesRef = collection(db, 'chats', chatId, 'messages');
        const q = query(messagesRef, orderBy('time', 'asc'));

        this.unsubscribeMessages = onSnapshot(q, (snapshot) => {
            const messages = snapshot.docs.map(doc => ({ id: doc.id, ...doc.data() }));
            this.renderMessages(messages);
        });
    }

    renderMessages(messages) {
        const container = document.getElementById('messages-list');
        if (!container) return;

        container.innerHTML = messages.map(msg => {
            const isMe = msg.sender_id === this.currentUid;
            const time = msg.time ? new Date(msg.time.seconds * 1000).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
            const replyHtml = msg.reply_to ? `
                <div class="reply-preview-in-msg mb-1 p-2 rounded bg-dark bg-opacity-10 small">
                    <div class="fw-bold">${msg.reply_to_name || ''}</div>
                    <div class="text-truncate">${msg.reply_to}</div>
                </div>
            ` : '';

            return `
                <div class="message ${isMe ? 'sent' : 'received'}" oncontextmenu="window.chatApp.showMsgMenu(event, '${msg.id}')">
                    ${replyHtml}
                    <div class="msg-text">${msg.text}</div>
                    <span class="message-time">${time}</span>
                </div>
            `;
        }).join('');
        
        container.scrollTop = container.scrollHeight;
    }

    setReplyingTo(msgId) {
        const messagesRef = collection(db, 'chats', this.activeChatId, 'messages');
        // This is tricky because we don't store all messages in memory. 
        // For simplicity, let's just find it in the DOM or pass the text.
        const msgEl = document.querySelector(`[oncontextmenu*="${msgId}"] .msg-text`);
        const chat = this.chats.find(c => c.id === this.activeChatId);
        const otherParticipantId = chat.participants.find(p => p !== this.currentUid);
        const name = chat.names[otherParticipantId];

        this.replyingTo = {
            id: msgId,
            text: msgEl ? msgEl.innerText : 'الرسالة الرئيسية',
            name: name
        };

        const preview = document.getElementById('reply-preview');
        if (preview) {
            preview.innerHTML = `
                <div class="d-flex justify-content-between align-items-center p-2 bg-light border-start border-primary border-4 rounded mb-2">
                    <div class="small text-truncate">
                        <div class="fw-bold">${name}</div>
                        <div>${this.replyingTo.text}</div>
                    </div>
                    <button class="btn btn-sm btn-link text-muted" onclick="window.chatApp.clearReply()"><i class="fas fa-times"></i></button>
                </div>
            `;
            preview.style.display = 'block';
        }
    }

    clearReply() {
        this.replyingTo = null;
        const preview = document.getElementById('reply-preview');
        if (preview) {
            preview.style.display = 'none';
            preview.innerHTML = '';
        }
    }

    showMsgMenu(e, msgId) {
        e.preventDefault();
        const menu = document.getElementById('msg-context-menu');
        if (!menu) return;

        menu.style.display = 'block';
        menu.style.left = e.pageX + 'px';
        menu.style.top = e.pageY + 'px';
        menu.dataset.msgId = msgId;

        // Hide menu on click outside
        const hide = () => {
            menu.style.display = 'none';
            document.removeEventListener('click', hide);
        };
        setTimeout(() => document.addEventListener('click', hide), 10);
    }

    async deleteMessage(forEveryone = false) {
        const msgId = document.getElementById('msg-context-menu').dataset.msgId;
        if (!msgId || !this.activeChatId) return;

        if (forEveryone) {
            await deleteDoc(doc(db, 'chats', this.activeChatId, 'messages', msgId));
        } else {
            // Logic for "Delete for me"
            const msgRef = doc(db, 'chats', this.activeChatId, 'messages', msgId);
            await updateDoc(msgRef, {
                hidden_for: arrayUnion(this.currentUid)
            });
        }
    }

    async deleteChat(forEveryone = false) {
        if (!this.activeChatId) return;
        if (!confirm('هل أنت متأكد من حذف هذه الدردشة؟')) return;

        if (forEveryone) {
            await deleteDoc(doc(db, 'chats', this.activeChatId));
            this.activeChatId = null;
            document.getElementById('active-chat-header').style.display = 'none';
            document.getElementById('messages-list').innerHTML = '';
            document.getElementById('input-area').style.display = 'none';
        } else {
            const chatRef = doc(db, 'chats', this.activeChatId);
            await updateDoc(chatRef, {
                deleted_by: arrayUnion(this.currentUid)
            });
            this.activeChatId = null;
            document.getElementById('active-chat-header').style.display = 'none';
            document.getElementById('input-area').style.display = 'none';
        }
    }

    async sendMessage(text) {
        if (!this.activeChatId || !text.trim()) return;

        try {
            const chat = this.chats.find(c => c.id === this.activeChatId);
            const otherParticipantId = chat.participants.find(p => p !== this.currentUid);

            const messageData = {
                sender_id: this.currentUid,
                text: text,
                time: serverTimestamp(),
                is_read: false,
                reply_to: this.replyingTo?.text || null,
                reply_to_name: this.replyingTo?.name || null
            };

            this.clearReply();

            // 1. Add message
            await addDoc(collection(db, 'chats', this.activeChatId, 'messages'), messageData);

            // 2. Update chat metadata
            const chatRef = doc(db, 'chats', this.activeChatId);
            await updateDoc(chatRef, {
                last_message: text,
                last_sender_id: this.currentUid,
                last_time: serverTimestamp(),
                [`unread_${otherParticipantId}`]: increment(1)
            });

            // 3. Trigger Notification via Backend
            fetch('/admin/chat/notify-new-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    chat_id: this.activeChatId,
                    receiver_id: otherParticipantId,
                    message: text,
                    sender_name: 'الإدارة'
                })
            });

        } catch (error) {
            console.error('Send Message Error:', error);
        }
    }

    bindEvents() {
        const form = document.getElementById('message-form');
        const input = document.getElementById('message-input');

        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const text = input.value;
                if (text.trim()) {
                    this.sendMessage(text);
                    input.value = '';
                }
            });
        }

        // Search reps
        const searchInput = document.getElementById('search-reps');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.renderRepsList(e.target.value);
            });
        }

        // Search staff
        const searchStaffInput = document.getElementById('search-staff');
        if (searchStaffInput) {
            searchStaffInput.addEventListener('input', (e) => {
                this.renderStaffList(e.target.value);
            });
        }

        // Conversation Filters
        const filterBtns = document.querySelectorAll('#chats-content .btn');
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                this.renderChatList(btn.innerText);
            });
        });
    }

    async loadStaff() {
        try {
            const response = await fetch('/representative/chat/support-staff');
            const data = await response.json();
            this.allStaff = data.staff;
            this.renderStaffList();
        } catch (error) {
            console.error('Load Staff Error:', error);
        }
    }

    renderStaffList(search = '') {
        const container = document.getElementById('staff-list');
        if (!container) return;

        const filtered = (this.allStaff || []).filter(s => 
            s.name.toLowerCase().includes(search.toLowerCase()) && s.id !== this.currentUid
        );

        if (filtered.length === 0) {
            container.innerHTML = '<div class="text-center p-4 text-muted small">لا يوجد نتائج</div>';
            return;
        }

        container.innerHTML = filtered.map(staff => `
            <div class="chat-item d-flex align-items-center justify-content-between p-2">
                <div class="d-flex align-items-center gap-2">
                    <img src="${staff.avatar}" class="chat-avatar" style="width: 35px; height: 35px;">
                    <div>
                        <div class="chat-name" style="font-size: 13px;">${staff.name}</div>
                        <div class="text-muted small" style="font-size: 10px;">${staff.role}</div>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-primary rounded-pill px-3" 
                        onclick="window.chatApp.startNewChat('${staff.id}', '${staff.name}')">
                    مراسلة
                </button>
            </div>
        `).join('');
    }

    async loadRepresentatives() {
        try {
            const response = await fetch('/admin/chat/representatives');
            const data = await response.json();
            this.allReps = data.representatives;
            this.renderRepsList();
        } catch (error) {
            console.error('Load Reps Error:', error);
            const container = document.getElementById('reps-list');
            if (container) container.innerHTML = '<div class="text-center p-4 text-muted">فشل تحميل المناديب</div>';
        }
    }

    renderRepsList(search = '') {
        const container = document.getElementById('reps-list');
        if (!container) return;

        const filtered = this.allReps.filter(r => 
            r.name.toLowerCase().includes(search.toLowerCase())
        );

        if (filtered.length === 0) {
            container.innerHTML = '<div class="text-center p-4 text-muted small">لا يوجد نتائج</div>';
            return;
        }

        container.innerHTML = filtered.map(rep => `
            <div class="chat-item d-flex align-items-center justify-content-between p-2">
                <div class="d-flex align-items-center gap-2">
                    <img src="${rep.avatar}" class="chat-avatar" style="width: 35px; height: 35px;">
                    <div class="chat-name" style="font-size: 13px;">${rep.name}</div>
                </div>
                <button class="btn btn-sm btn-outline-primary rounded-pill px-3" 
                        onclick="window.chatApp.startNewChat('${rep.id}', '${rep.name}')">
                    مراسلة
                </button>
            </div>
        `).join('');
    }

    async startNewChat(repId, name) {
        try {
            // Unified ID generation (same as Flutter)
            const chatId = [this.currentUid, repId].sort().join('_');
            const chatRef = doc(db, 'chats', chatId);
            const chatSnap = await getDoc(chatRef);

            if (chatSnap.exists()) {
                this.selectChat(chatId);
            } else {
                const chatData = {
                    id: chatId,
                    participants: [this.currentUid, repId],
                    names: {
                        [this.currentUid]: 'الإدارة',
                        [repId]: name
                    },
                    last_message: '',
                    last_time: serverTimestamp(),
                    [`unread_${this.currentUid}`]: 0,
                    [`unread_${repId}`]: 0,
                    deleted_by: []
                };
                await setDoc(chatRef, chatData);
                this.selectChat(chatId);
            }
            
            const chatBtn = document.getElementById('chats-tab');
            if (chatBtn) chatBtn.click();
        } catch (error) {
            console.error('Start New Chat Error:', error);
        }
    }
}

// Global instance for onclick events in HTML
document.addEventListener('DOMContentLoaded', () => {
    window.chatApp = new AdminChat();
});
