document.addEventListener("DOMContentLoaded",()=>{const n=document.getElementById("chat_container"),d=document.getElementById("msgr_input"),g=document.getElementById("send_button"),y=document.querySelectorAll(".emoji"),p=document.querySelectorAll(".story-circle"),v=["That's interesting. Tell me more.","I completely understand what you mean.","I hadn't thought about it that way before.","That's great news!","LOL 😂 That's hilarious!","Really? I'm surprised to hear that.","I'm not sure I agree, but I see your point.","Let's discuss this further when we meet.","Thanks for letting me know!","Sorry to hear that. Is there anything I can do to help?","Can we talk about this tomorrow? I need some time to think.","Wow! That's amazing! 👍","So how are you liking SmartAdmin?"],h=["./img/demo/gallery/1.jpg","./img/demo/gallery/2.jpg","./img/demo/gallery/3.jpg","./img/demo/gallery/4.jpg","./img/demo/gallery/5.jpg"];p.length&&p.forEach(e=>{e.addEventListener("click",()=>{p.forEach(t=>t.classList.remove("active")),e.classList.add("active")})}),y.length&&y.forEach(e=>{e.addEventListener("click",t=>{t.preventDefault();const i=e.classList.contains("emoji--like")?"👍":e.classList.contains("emoji--love")?"❤️":e.classList.contains("emoji--haha")?"😂":e.classList.contains("emoji--yay")?"🎉":e.classList.contains("emoji--wow")?"😮":e.classList.contains("emoji--sad")?"😢":e.classList.contains("emoji--angry")?"😡":"";i&&r(i)})});function r(e=null){const t=e||d.value.trim();if(!t)return;e||(d.value="");const i=new Date,s=i.getHours().toString().padStart(2,"0")+":"+i.getMinutes().toString().padStart(2,"0");c(t,"sent",s),m(),j();const l=1e3+Math.random()*2e3;setTimeout(()=>{_(),w()},l)}function w(){const e=Math.random();if(e<.6){const t=v[Math.floor(Math.random()*v.length)];c(t,"get",o())}else if(e<.7){const t=h[Math.floor(Math.random()*h.length)];f(t,"get",o())}else if(e<.9){const t=["👍","❤️","😂","🎉","😮","😢","😡"],i=t[Math.floor(Math.random()*t.length)];c(i,"get",o())}m()}function j(){const e=document.createElement("div");e.className="chat-segment chat-segment-get typing-indicator",e.innerHTML=`
            <div class="chat-message">
                <div class="typing">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        `,n.appendChild(e),m()}function _(){const e=document.querySelector(".typing-indicator");e&&e.remove()}function c(e,t,i){const s=document.createElement("div");s.className=`chat-segment chat-segment-${t}`;const l=new RegExp("^(\\p{Emoji}\\uFE0F?)+$","u").test(e),a=e==="👍"?"like":e==="❤️"?"love":e==="😂"?"haha":e==="🎉"?"yay":e==="😮"?"wow":e==="😢"?"sad":e==="😡"?"angry":null;let u;a?u=`
                <div class="emoji emoji--${a}">
                    ${a==="like"?`<div class="emoji__hand">
                            <div class="emoji__thumb"></div>
                        </div>`:a==="love"?'<div class="emoji__heart"></div>':a==="haha"?`<div class="emoji__face">
                            <div class="emoji__eyes"></div>
                            <div class="emoji__mouth">
                                <div class="emoji__tongue"></div>
                            </div>
                        </div>`:a==="yay"?`<div class="emoji__face">
                            <div class="emoji__eyebrows"></div>
                            <div class="emoji__mouth"></div>
                        </div>`:a==="wow"||a==="sad"||a==="angry"?`<div class="emoji__face">
                            <div class="emoji__eyebrows"></div>
                            <div class="emoji__eyes"></div>
                            <div class="emoji__mouth"></div>
                        </div>`:""}
                </div>
                <div class="${t==="sent"?"text-end":""} fw-300 text-muted mt-1 fs-xs">
                    ${i}
                </div>
            `:u=`
                <div class="chat-message ${l?"emoji-only":""}">
                    <p>${e}</p>
                </div>
                <div class="${t==="sent"?"text-end":""} fw-300 text-muted mt-1 fs-xs">
                    ${i}
                </div>
            `,s.innerHTML=u,n.appendChild(s)}function f(e,t,i){const s=document.createElement("div");s.className=`chat-segment chat-segment-${t}`,s.innerHTML=`
            <div class="chat-message">
                <p><img src="${e}" class="img-fluid rounded" alt="Shared image" style="max-height: 200px;"></p>
            </div>
            <div class="${t==="sent"?"text-end":""} fw-300 text-muted mt-1 fs-xs">
                ${i}
            </div>
        `,n.appendChild(s)}function b(e,t,i){const s=document.createElement("div");s.className=`chat-segment chat-segment-${t}`;const l=e.type==="pdf"?"file-pdf text-danger":e.type==="doc"?"file-word text-primary":e.type==="xls"?"file-excel text-success":e.type==="ppt"?"file-powerpoint text-warning":"file text-muted";s.innerHTML=`
            <div class="chat-message">
                <div class="d-flex align-items-center p-2 rounded bg-white">
                    <i class="sa sa-${l} fs-2x me-2"></i>
                    <div class="flex-grow-1">
                        <div class="text-truncate fw-500">${e.name}</div>
                        <small class="text-muted">${e.size}</small>
                    </div>
                    <a href="javascript:void(0);" class="btn btn-sm btn-icon">
                        <i class="sa sa-download"></i>
                    </a>
                </div>
            </div>
            <div class="${t==="sent"?"text-end":""} fw-300 text-muted mt-1 fs-xs">
                ${i}
            </div>
        `,n.appendChild(s)}function o(){const e=new Date;return e.getHours().toString().padStart(2,"0")+":"+e.getMinutes().toString().padStart(2,"0")}function m(){n.scrollTop=n.scrollHeight}d&&d.addEventListener("keydown",e=>{e.key==="Enter"&&!e.shiftKey&&(e.preventDefault(),r())}),g&&g.addEventListener("click",()=>{r()});function k(){n.querySelectorAll(".chat-segment:not(.d-flex)").forEach(s=>s.remove());const t=[{text:"Hi there! How's your day going?",type:"get",delay:0},{text:"Pretty good, thanks for asking! Just finished a big project.",type:"sent",delay:300},{text:"That's great to hear! Is this the same one you mentioned last week?",type:"get",delay:600},{text:"Yes, finally wrapped it up. The client was really happy with the results.",type:"sent",delay:900},{text:"Thanks for sharing the document! I'll take a look at it.",type:"get",delay:1500},{text:"Let me know if you need any clarification.",type:"sent",delay:1800},{image:h[2],type:"get",isImage:!0,delay:2100},{text:"That looks amazing! Is that from the project?",type:"sent",delay:2400},{text:"Yes, it's the final design we went with 😊",type:"get",delay:2700}];let i=0;t.forEach(s=>{setTimeout(()=>{s.isImage?f(s.image,s.type,o()):s.isFile?b(s.file,s.type,o()):c(s.text,s.type,o()),m()},i),i+=s.delay})}const x=document.createElement("style");if(x.textContent=`
        .typing {
            display: flex;
            align-items: center;
            height: 17px;
        }
        .typing span {
            background-color: #90949c;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            margin: 0 2px;
            display: block;
            animation: typing 1.3s infinite ease-in-out;
        }
        .typing span:nth-child(1) {
            animation-delay: 0s;
        }
        .typing span:nth-child(2) {
            animation-delay: 0.2s;
        }
        .typing span:nth-child(3) {
            animation-delay: 0.4s;
        }
        @keyframes typing {
            0%, 60%, 100% {
                transform: translateY(0);
            }
            30% {
                transform: translateY(-5px);
            }
        }
        .emoji-only {
            font-size: 3rem;
        }
        
        /* Improved conversation list styling */
        #js-slide-right .list-group-item {
            transition: background-color 0.2s ease;
            border-radius: 8px;
            margin: 4px 8px;
            border: none;
        }
        
                            
               
        .unread-badge {
            background-color: var(--primary-500);
            color: white;
            font-size: 0.7rem;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Story circles */
        .story-circle {
            cursor: pointer;
        }
        
        .story-circle.active .profile-image {
            border-color: var(--primary-500);
        }
        
        .story-circle .profile-image {
            border: 2px solid var(--bs-body-bg);
        }
        
        .story-circle.has-story .profile-image {
            border: 2px solid var(--primary-500);
        }
        
        /* Enhanced Emoji Styles */
        .chat-segment .emoji {
            transform: scale(2);
            margin: 16px;
            display: inline-block;
        }
        
        .chat-segment-sent .emoji {
            margin-left: auto;
        }
        
        .chat-segment-get .emoji {
            margin-right: auto;
        }
        
    `,document.head.appendChild(x),k(),!g){const e=d.parentElement,t=document.createElement("button");t.id="send_button",t.className="btn btn-icon fs-xl width-1 flex-shrink-0",t.setAttribute("type","button"),t.setAttribute("data-bs-toggle","tooltip"),t.setAttribute("data-bs-original-title","Send"),t.setAttribute("data-bs-placement","top"),t.innerHTML='<svg class="sa-icon sa-bold sa-icon-subtlelight"><use href="/icons/sprite.svg#send"></use></svg>',e.appendChild(t),t.addEventListener("click",()=>{r()})}});
