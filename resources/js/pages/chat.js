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
    doc, 
    getDoc,
    serverTimestamp,
    increment
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
        this.loadRepresentatives(); // Load reps first as requested
        
        try {
            const tokenResponse = await fetch('/admin/chat/firebase-token');
            const data = await tokenResponse.json();
            
            if (data.token) {
                const userCredential = await signInWithCustomToken(auth, data.token);
                this.currentUid = data.uid;
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
                .sort((a, b) => {
                    const aTime = a.last_time?.seconds || 0;
                    const bTime = b.last_time?.seconds || 0;
                    return bTime - aTime; // Sort descending
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
            
            return `
                <div class="message ${isMe ? 'sent' : 'received'}">
                    ${msg.text}
                    <span class="message-time">${time}</span>
                </div>
            `;
        }).join('');
        
        container.scrollTop = container.scrollHeight;
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
                is_read: false
            };

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
            // Switch to chats tab
            const chatBtn = document.getElementById('chats-tab');
            if (chatBtn) chatBtn.click();

            // Check if chat already exists
            let existingChat = this.chats.find(c => c.participants.includes(repId));
            
            if (existingChat) {
                this.selectChat(existingChat.id);
            } else {
                // Create new chat in Firestore
                const chatData = {
                    participants: [this.currentUid, repId],
                    names: {
                        [this.currentUid]: 'الإدارة',
                        [repId]: name
                    },
                    last_message: '',
                    last_time: serverTimestamp(),
                    [`unread_${this.currentUid}`]: 0,
                    [`unread_${repId}`]: 0
                };

                const docRef = await addDoc(collection(db, 'chats'), chatData);
                this.selectChat(docRef.id);
            }
        } catch (error) {
            console.error('Start New Chat Error:', error);
        }
    }
}

// Global instance for onclick events in HTML
document.addEventListener('DOMContentLoaded', () => {
    window.chatApp = new AdminChat();
});
