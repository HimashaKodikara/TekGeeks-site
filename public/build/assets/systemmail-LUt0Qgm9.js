let l,h,d,f,p,i,g,v,a,o;document.addEventListener("DOMContentLoaded",function(){k(),x(),L(),M()});function k(){l=document.getElementById("js-emails"),h=document.getElementById("js-msg-select-all"),d=document.querySelector(".js-refresh").closest("button"),f=document.querySelector(".js-delete").closest("button"),p=document.querySelector(".js-spam").closest("button"),i=document.getElementById("default-example-modal-lg-center"),g=i?.querySelector(".btn-primary"),v=i?.querySelector(".btn-secondary"),a=document.querySelector("a.fs-xs.text-secondary"),o=document.getElementById("message-attachments")}function L(){l.addEventListener("click",S),h?.addEventListener("change",w),d?.addEventListener("click",I),f?.addEventListener("click",A),p?.addEventListener("click",q),g?.addEventListener("click",T),v?.addEventListener("click",$),a?.addEventListener("click",y)}function S(e){const t=e.target.closest(".mail-starred");if(t){const s=t.closest("li"),n=s.querySelector(".form-check-input").id.replace("msg-","");s.classList.toggle("starred");const c=JSON.parse(localStorage.getItem("emails")||"[]"),r=c.find(u=>u.id===n);r&&(r.starred=!r.starred,localStorage.setItem("emails",JSON.stringify(c))),e.stopPropagation();return}e.target.closest(".js-email-content")}function w(e){l.querySelectorAll(".form-check-input").forEach(s=>{s.checked=e.target.checked})}function I(){l.classList.add("refreshing"),d.querySelector("i").classList.add("fa-spin"),setTimeout(()=>{x(),l.classList.remove("refreshing"),d.querySelector("i").classList.remove("fa-spin")},1e3)}function A(){const e=l.querySelectorAll(".form-check-input:checked");if(e.length===0)return;const t=e.length;e.forEach(s=>{const n=s.closest("li");n.style.overflow="hidden",n.classList.add("deleting"),setTimeout(()=>{n.remove()},300)}),m(`${t} message${t>1?"s":""} deleted`)}function q(){const e=l.querySelectorAll(".form-check-input:checked");if(e.length===0)return;const t=e.length;e.forEach(s=>{const n=s.closest("li");n.style.overflow="hidden",n.classList.add("deleting"),setTimeout(()=>{n.remove()},300)}),m(`${t} message${t>1?"s":""} moved to spam`)}function T(e){e.preventDefault();const t=document.getElementById("message-to").value,s=document.querySelector('input[placeholder="Subject"]').value,n=document.getElementById("fake_textarea").innerHTML;if(!t||!s||!n){alert("Please fill in all required fields");return}bootstrap.Modal.getInstance(i).hide(),m("Email sent successfully!"),B(!0)}function $(e){e.preventDefault(),bootstrap.Modal.getInstance(i).hide(),m('<i class="fas fa-check me-2"></i> Draft saved',"success")}function m(e,t="success"){const s=document.createElement("div");s.className=`mail-toast bg-${t}-500`,s.innerHTML=e,document.body.appendChild(s),setTimeout(()=>s.classList.add("show"),100),setTimeout(()=>{s.classList.remove("show"),setTimeout(()=>s.remove(),300)},3e3)}function B(e=!1){if(document.getElementById("message-to").value="",document.getElementById("message-to-cc").value="",document.querySelector('input[placeholder="Subject"]').value="",o)if(e){o.innerHTML="";const t=document.createElement("a");t.href="#",t.className="fs-xs text-secondary",t.textContent="show 3 more",t.addEventListener("click",y),o.appendChild(t),o.classList.add("if-empty-display-none")}else o.querySelectorAll(".alert").forEach((s,n)=>{n>1?s.remove():s.classList.remove("hidden-attachment")}),a&&(a.style.display="none");document.getElementById("fake_textarea").innerHTML=`
    <p><br></p>
    <p><br></p>
    <p>Best regards,</p>
    <div class="d-flex d-column align-items-start mb-3 gap-2">
        <img src="/img/demo/avatars/avatar-admin.png" alt="SmartAdmin WebApp" class="me-3 mt-1 rounded-circle width-2">
        <div class="border-left pl-3">
            <span class="fw-500 fs-lg d-block l-h-n">Sunny A.</span>
            <span class="fw-400 fs-nano d-block l-h-n mb-1">Software Engineer</span>
        </div>
    </div>
    <div class="text-muted fs-nano">
        PRIVATE AND CONFIDENTIAL. This e-mail, its contents and attachments are private and confidential and is intended for the recipient only. Any disclosure, copying or unauthorized use of such information is prohibited. If you receive this message in error, please notify us immediately and delete the original and any copies and attachments.
    </div>
    `}function y(e){e.preventDefault();const t=document.querySelectorAll(".hidden-attachment");t.length>0?(t.forEach(s=>{s.classList.remove("hidden-attachment")}),e.target.textContent="hide attachments"):(o.querySelectorAll(".alert").forEach((n,c)=>{c>1&&n.classList.add("hidden-attachment")}),b())}function M(){if(!o)return;[{name:"report.docx",type:"primary"},{name:"presentation.pptx",type:"primary"},{name:"data.xlsx",type:"primary"}].forEach((t,s)=>{const n=document.createElement("div");n.className=`alert m-0 p-0 badge bg-${t.type}-50 border-${t.type} ps-2 ${s>0?"hidden-attachment":""}`,n.innerHTML=`${t.name} <button data-bs-dismiss="alert" class="btn btn-icon btn-xs ms-1 rounded-0 border border-${t.type} border-top-0 border-bottom-0 border-end-0" type="button">
            <i class="fas fa-times"></i>
        </button>`,o.insertBefore(n,a)}),b()}function b(){if(!a)return;const e=document.querySelectorAll(".hidden-attachment");e.length>0?(a.textContent=`show ${e.length} more`,a.style.display=""):a.style.display="none"}function x(){fetch("/json/MOCK_MAIL.json").then(e=>{if(!e.ok)throw new Error("Network response was not ok "+e.statusText);return e.json()}).then(e=>{if(!l){console.error("Email list container not found!");return}localStorage.setItem("emails",JSON.stringify(e)),l.innerHTML="",e.forEach((t,s)=>{const n=document.createElement("li");n.className=`${t.read?"":"unread"} ${t.starred?"starred":""}`.trim(),n.style.cursor="pointer";const c=`msg-${t.id}`,r=new Date(t.timestamp).toLocaleTimeString([],{hour:"numeric",minute:"2-digit",hour12:!0});n.innerHTML=`
                <div class="d-flex align-items-center px-3 px-sm-4 py-2 py-lg-0 height-4 height-mobile-auto gap-2">
                    <div class="form-check form-check-hitbox me-2 order-1 mb-0">
                        <input type="checkbox" class="form-check-input" id="${c}">
                        <label class="form-check-label" for="${c}"></label>
                    </div>
                    <div class="d-flex align-self-end align-self-lg-center order-3 order-lg-2 me-lg-3 me-0 mb-1 mb-lg-0 flex-shrink-0">
                        <svg class="mail-starred sa-icon">
                            <use href="/icons/sprite.svg#star"></use>
                        </svg>
                    </div>
                    <div class="js-email-content d-flex flex-column flex-lg-row flex-grow-1 align-items-stretch order-2 order-lg-3" style="min-width: 0;">
                        <div class="mail-sender flex-shrink-0 align-self-start align-self-lg-center width-sm width-max-sm text-truncate">${t.sender}</div>
                        <div class="d-flex flex-column flex-lg-row flex-grow-1 w-100 overflow-hidden">
                            <div class="mail-subject flex-shrink-0 align-self-start align-self-lg-center me-2 text-truncate width-max-100">${t.subject}</div>
                            <div class="d-flex align-items-center flex-grow-1 w-100 overflow-hidden">
                                <div class="mail-body d-block text-truncate w-100 pe-lg-5 text-muted">
                                    <span class="hidden-sm">-</span> ${t.bodyPreview}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="fs-sm text-muted ms-auto hide-on-hover-parent order-4 position-on-mobile-absolute pos-top pos-right pt-1 pt-lg-0 mt-2 me-3 me-sm-4 mt-lg-0 me-lg-0 flex-shrink-0">${r}</div>
                </div>
            `,n.querySelector(".form-check-input").addEventListener("click",E=>{E.stopPropagation()}),l.appendChild(n)})}).catch(e=>console.error("Error loading emails:",e))}
