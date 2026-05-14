let d;const y="IconStackDB",r="savedIcons";let i=null;function h(){return new Promise((n,s)=>{const e=indexedDB.open(y,1);e.onerror=()=>s(e.error),e.onsuccess=()=>{d=e.result,n(d)},e.onupgradeneeded=o=>{const t=o.target.result;if(!t.objectStoreNames.contains(r)){const c=t.createObjectStore(r,{keyPath:"id",autoIncrement:!0});c.createIndex("name","name",{unique:!0}),c.createIndex("createdAt","createdAt",{unique:!1})}}})}async function v(){const e=d.transaction([r],"readonly").objectStore(r).getAll();return new Promise((o,t)=>{e.onsuccess=()=>o(e.result),e.onerror=()=>t(e.error)})}function a(n,s="primary"){i&&i.hide();let e=document.querySelector(".toast-container");e||(e=document.createElement("div"),e.className="toast-container position-fixed top-0 end-0 p-3",document.body.appendChild(e));const o="toast-"+Date.now(),t=document.createElement("div");t.className=`toast hide align-items-center border-0 py-2 px-3 bg-${s} text-white`,t.id=o,t.setAttribute("role","alert"),t.setAttribute("aria-live","assertive"),t.setAttribute("aria-atomic","true"),t.style.setProperty("--bs-toast-max-width","auto"),t.innerHTML=`
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center justify-content-center">
                ${n}
            </div>
            <button type="button" class="btn btn-system ms-auto" data-bs-dismiss="toast" aria-label="Close">
                <svg class="sa-icon sa-icon-light">
                    <use href="/icons/sprite.svg#x"></use>
                </svg>
            </button>
        </div>
    `,e.appendChild(t),i=new bootstrap.Toast(t,{autohide:!0,delay:3e3}),t.addEventListener("hidden.bs.toast",function(){i=null,t.remove()}),i.show()}async function f(n){try{if(confirm("Are you sure you want to delete this icon?")){const e=d.transaction([r],"readwrite").objectStore(r);await new Promise((o,t)=>{const c=e.delete(n);c.onsuccess=()=>o(),c.onerror=()=>t(c.error)}),a("Icon deleted successfully","success"),await b()}}catch(s){console.error("Error deleting icon:",s),a("Failed to delete icon","danger")}}function m(n){navigator.clipboard.writeText(n).then(()=>a("Icon copied to clipboard!","success")).catch(()=>a("Failed to copy icon","danger"))}async function b(){try{const n=await v(),s=document.getElementById("iconList");s.innerHTML="",n.sort((e,o)=>new Date(o.createdAt)-new Date(e.createdAt)),n.forEach(e=>{const o=document.createElement("li");o.className="col-4 col-sm-3 col-md-3 col-lg-2 col-xl-2 col-xxl-1 mb-g",o.innerHTML=`
                <div class="d-flex flex-column align-items-center p-2 m-0 w-100 shadow-hover-2 border rounded position-relative show-child-on-hover overflow-hidden" style="font-size: 4rem;">
                    <div class="show-on-hover-parent bg-secondary bg-opacity-50 position-absolute top-0 start-0 w-100 h-100 z-1">
                        <div class="d-flex flex-row align-items-end justify-content-center h-100 gap-1 pb-2">
                            <button type="button" class="btn btn-xs btn-success copy-btn">
                                COPY
                            </button>
                            <button type="button" class="btn btn-xs btn-danger delete-btn">
                                DEL
                            </button>
                        </div>
                    </div>
                    <div class="pb-1 d-flex icon-container">
                        <div class="stack-icon">
                            ${e.html}
                        </div>
                    </div>
                    <div class="text-muted fs-nano icon-name">
                        ${e.name}
                    </div>
                </div>
            `,s.appendChild(o);const t=o.querySelector(".copy-btn"),c=o.querySelector(".delete-btn"),p=o.querySelector(".icon-container"),u=`<div class="stack-icon">${e.html}</div>`;t.addEventListener("click",l=>{l.stopPropagation(),m(u)}),c.addEventListener("click",l=>{l.stopPropagation(),f(e.id)}),p.addEventListener("click",()=>{m(u)})}),n.length===0&&(s.innerHTML=`
                <div class="col-12 text-center text-muted py-5">
                    <h4>No saved icons found</h4>
                    <p>Create and save some icons using the <a href="stackgenerator.html">Stack Generator</a> to see them here.</p>
                </div>
            `)}catch(n){console.error("Error loading icons:",n),a("Failed to load icons","danger")}}function g(){const n=document.getElementById("searchIcons").value.toLowerCase();document.querySelectorAll("#iconList li").forEach(e=>{const t=e.querySelector(".icon-name").textContent.toLowerCase().includes(n);e.style.display=t?"":"none"})}document.addEventListener("DOMContentLoaded",async function(){try{await h(),await b(),document.getElementById("searchIcons").addEventListener("input",g),console.log("Stack Library initialized successfully")}catch(n){console.error("Error initializing Stack Library:",n),a("Failed to initialize Stack Library","danger")}});
