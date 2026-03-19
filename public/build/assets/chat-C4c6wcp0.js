import{s as y,a as g,c as o,q as m,w as b,b as u,d,u as l,e as w,f as c,h as p,i as v,j as h,k as C,l as I,n as x,p as E}from"./config-Cs7-jpGb.js";class L{constructor(){this.activeChatId=null,this.currentUid=null,this.chats=[],this.unsubscribeChats=null,this.unsubscribeMessages=null,this.allReps=[],this.init()}async init(){console.log("Chat Initializing..."),this.loadRepresentatives(),this.loadStaff();try{const t=await(await fetch("/admin/chat/firebase-token")).json();if(t.token){const s=await y(g,t.token);this.currentUid="u_"+t.uid,console.log("Logged into Firebase as:",this.currentUid),this.listenToChats(),this.bindEvents()}else console.error("No Firebase token received"),this.showError("خطأ في الاتصال بنظام الرسائل")}catch(e){console.error("Chat Init Error:",e),this.showError("حدث خطأ أثناء تحميل نظام الدردشة")}}showError(e){const t=document.getElementById("conversations-list");t&&(t.innerHTML=`<div class="text-center p-4 text-danger">${e}</div>`)}listenToChats(){const e=o(c,"chats"),t=m(e,b("participants","array-contains",this.currentUid));this.unsubscribeChats=u(t,s=>{this.chats=s.docs.map(n=>({id:n.id,...n.data()})).filter(n=>!(n.deleted_by||[]).includes(this.currentUid)).sort((n,a)=>{const i=n.last_time?.seconds||0;return(a.last_time?.seconds||0)-i}),this.renderChatList()})}renderChatList(){const e=document.getElementById("conversations-list");if(e){if(this.chats.length===0){e.innerHTML='<div class="text-center p-4 text-muted">لا يوجد محادثات سابقة</div>';return}e.innerHTML=this.chats.map(t=>{const s=t.participants.find(f=>f!==this.currentUid),n=t.names?t.names[s]:"مستخدم",a=this.activeChatId===t.id?"active":"",i=t[`unread_${this.currentUid}`]||0,r=t.last_message||"محادثة جديدة";return`
                <div class="chat-item ${a}" onclick="window.chatApp.selectChat('${t.id}')">
                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(n)}" class="chat-avatar">
                    <div class="chat-info">
                        <div class="d-flex justify-content-between">
                            <div class="chat-name">${n}</div>
                            ${i>0?`<span class="unread-badge">${i}</span>`:""}
                        </div>
                        <div class="chat-last-msg">${r}</div>
                    </div>
                </div>
            `}).join("")}}selectChat(e){if(this.activeChatId===e)return;this.activeChatId=e;const t=this.chats.find(i=>i.id===e);if(!t)return;document.getElementById("active-chat-header").style.display="flex",document.getElementById("input-area").style.display="block";const s=t.participants.find(i=>i!==this.currentUid),n=t.names?t.names[s]:"مستخدم";document.getElementById("active-user-name").innerText=n,document.getElementById("active-user-avatar").src=`https://ui-avatars.com/api/?name=${encodeURIComponent(n)}`;const a=d(c,"chats",e);l(a,{[`unread_${this.currentUid}`]:0}),this.listenToMessages(e),this.renderChatList()}listenToMessages(e){this.unsubscribeMessages&&this.unsubscribeMessages();const t=o(c,"chats",e,"messages"),s=m(t,w("time","asc"));this.unsubscribeMessages=u(s,n=>{const a=n.docs.map(i=>({id:i.id,...i.data()}));this.renderMessages(a)})}renderMessages(e){const t=document.getElementById("messages-list");t&&(t.innerHTML=e.map(s=>{const n=s.sender_id===this.currentUid,a=s.time?new Date(s.time.seconds*1e3).toLocaleTimeString([],{hour:"2-digit",minute:"2-digit"}):"";if(s.hidden_for&&s.hidden_for.includes(this.currentUid))return"";const i=s.reply_to?`
                <div class="reply-preview-in-msg mb-1 p-2 rounded bg-dark bg-opacity-10 small" 
                     style="cursor: pointer" 
                     onclick="window.chatApp.scrollToMessage('${s.reply_to_id}')">
                    <div class="fw-bold text-primary">${s.reply_to_name||""}</div>
                    <div class="text-truncate" style="max-width: 250px;">${s.reply_to}</div>
                </div>
            `:"";return`
                <div id="msg-${s.id}" class="message ${n?"sent":"received"}" oncontextmenu="window.chatApp.showMsgMenu(event, '${s.id}')">
                    ${i}
                    <div class="msg-text">${s.text}</div>
                    <span class="message-time">${a}</span>
                </div>
            `}).join(""),t.scrollTop=t.scrollHeight)}scrollToMessage(e){const t=document.getElementById(`msg-${e}`);if(t){t.scrollIntoView({behavior:"smooth",block:"center"}),t.style.transition="background-color 0.5s";const s=t.classList.contains("sent")?"#007bff":"#fff";t.style.backgroundColor="#ffffcc",setTimeout(()=>{t.style.backgroundColor=s},1e3)}}setReplyingTo(e){o(c,"chats",this.activeChatId,"messages");const t=document.querySelector(`[oncontextmenu*="${e}"] .msg-text`),s=this.chats.find(r=>r.id===this.activeChatId),n=s.participants.find(r=>r!==this.currentUid),a=s.names[n];this.replyingTo={id:e,text:t?t.innerText:"الرسالة الرئيسية",name:a};const i=document.getElementById("reply-preview");i&&(i.innerHTML=`
                <div class="d-flex justify-content-between align-items-center p-2 bg-light border-start border-primary border-4 rounded mb-2">
                    <div class="small text-truncate">
                        <div class="fw-bold">${a}</div>
                        <div>${this.replyingTo.text}</div>
                    </div>
                    <button class="btn btn-sm btn-link text-muted" onclick="window.chatApp.clearReply()"><i class="fas fa-times"></i></button>
                </div>
            `,i.style.display="block")}clearReply(){this.replyingTo=null;const e=document.getElementById("reply-preview");e&&(e.style.display="none",e.innerHTML="")}showMsgMenu(e,t){e.preventDefault();const s=document.getElementById("msg-context-menu");if(!s)return;s.style.display="block",s.style.left=e.pageX+"px",s.style.top=e.pageY+"px",s.dataset.msgId=t;const n=()=>{s.style.display="none",document.removeEventListener("click",n)};setTimeout(()=>document.addEventListener("click",n),10)}async deleteMessage(e=!1){const t=document.getElementById("msg-context-menu").dataset.msgId;if(!(!t||!this.activeChatId))if(e)await p(d(c,"chats",this.activeChatId,"messages",t));else{const s=d(c,"chats",this.activeChatId,"messages",t);await l(s,{hidden_for:v(this.currentUid)})}}async deleteChat(e=!1){if(this.activeChatId&&confirm("هل أنت متأكد من حذف هذه الدردشة؟"))if(e)await p(d(c,"chats",this.activeChatId)),this.activeChatId=null,document.getElementById("active-chat-header").style.display="none",document.getElementById("messages-list").innerHTML="",document.getElementById("input-area").style.display="none";else{const t=d(c,"chats",this.activeChatId);await l(t,{deleted_by:v(this.currentUid)}),this.activeChatId=null,document.getElementById("active-chat-header").style.display="none",document.getElementById("input-area").style.display="none"}}async sendMessage(e){if(!(!this.activeChatId||!e.trim()))try{const s=this.chats.find(i=>i.id===this.activeChatId).participants.find(i=>i!==this.currentUid),n={sender_id:this.currentUid,text:e,time:h(),is_read:!1,reply_to:this.replyingTo?.text||null,reply_to_name:this.replyingTo?.name||null,reply_to_id:this.replyingTo?.id||null};this.clearReply(),await C(o(c,"chats",this.activeChatId,"messages"),n);const a=d(c,"chats",this.activeChatId);await l(a,{last_message:e,last_sender_id:this.currentUid,last_time:h(),[`unread_${s}`]:I(1)}),fetch("/admin/chat/notify-new-message",{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify({chat_id:this.activeChatId,receiver_id:s,message:e,sender_name:"الإدارة"})})}catch(t){console.error("Send Message Error:",t)}}bindEvents(){const e=document.getElementById("message-form"),t=document.getElementById("message-input");e&&e.addEventListener("submit",i=>{i.preventDefault();const r=t.value;r.trim()&&(this.sendMessage(r),t.value="")});const s=document.getElementById("search-reps");s&&s.addEventListener("input",i=>{this.renderRepsList(i.target.value)});const n=document.getElementById("search-staff");n&&n.addEventListener("input",i=>{this.renderStaffList(i.target.value)});const a=document.querySelectorAll("#chats-content .btn");a.forEach(i=>{i.addEventListener("click",()=>{a.forEach(r=>r.classList.remove("active")),i.classList.add("active"),this.renderChatList(i.innerText)})})}async loadStaff(){try{const t=await(await fetch("/admin/chat/support-staff")).json();this.allStaff=t.staff,this.renderStaffList()}catch(e){console.error("Load Staff Error:",e)}}renderStaffList(e=""){const t=document.getElementById("staff-list");if(!t)return;const s=(this.allStaff||[]).filter(n=>n.name.toLowerCase().includes(e.toLowerCase())&&n.id!==this.currentUid);if(s.length===0){t.innerHTML='<div class="text-center p-4 text-muted small">لا يوجد نتائج</div>';return}t.innerHTML=s.map(n=>`
            <div class="chat-item d-flex align-items-center justify-content-between p-2">
                <div class="d-flex align-items-center gap-2">
                    <img src="${n.avatar}" class="chat-avatar" style="width: 35px; height: 35px;">
                    <div>
                        <div class="chat-name" style="font-size: 13px;">${n.name}</div>
                        <div class="text-muted small" style="font-size: 10px;">${n.role}</div>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-primary rounded-pill px-3" 
                        onclick="window.chatApp.startNewChat('${n.id}', '${n.name}')">
                    مراسلة
                </button>
            </div>
        `).join("")}async loadRepresentatives(){try{const t=await(await fetch("/admin/chat/representatives")).json();this.allReps=t.representatives,this.renderRepsList()}catch(e){console.error("Load Reps Error:",e);const t=document.getElementById("reps-list");t&&(t.innerHTML='<div class="text-center p-4 text-muted">فشل تحميل المناديب</div>')}}renderRepsList(e=""){const t=document.getElementById("reps-list");if(!t)return;const s=this.allReps.filter(n=>n.name.toLowerCase().includes(e.toLowerCase()));if(s.length===0){t.innerHTML='<div class="text-center p-4 text-muted small">لا يوجد نتائج</div>';return}t.innerHTML=s.map(n=>`
            <div class="chat-item d-flex align-items-center justify-content-between p-2">
                <div class="d-flex align-items-center gap-2">
                    <img src="${n.avatar}" class="chat-avatar" style="width: 35px; height: 35px;">
                    <div class="chat-name" style="font-size: 13px;">${n.name}</div>
                </div>
                <button class="btn btn-sm btn-outline-primary rounded-pill px-3" 
                        onclick="window.chatApp.startNewChat('${n.id}', '${n.name}')">
                    مراسلة
                </button>
            </div>
        `).join("")}async startNewChat(e,t){try{const s=[this.currentUid,e].sort().join("_"),n=d(c,"chats",s);if((await x(n)).exists())this.selectChat(s);else{const r={id:s,participants:[this.currentUid,e],names:{[this.currentUid]:"الإدارة",[e]:t},last_message:"",last_time:h(),[`unread_${this.currentUid}`]:0,[`unread_${e}`]:0,deleted_by:[]};await E(n,r),this.selectChat(s)}const i=document.getElementById("chats-tab");i&&i.click()}catch(s){console.error("Start New Chat Error:",s)}}}document.addEventListener("DOMContentLoaded",()=>{window.chatApp=new L});
