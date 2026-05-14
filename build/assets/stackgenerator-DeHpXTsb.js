import{S as rt}from"./sortable.esm-j3KvE6KT.js";let dt="sa-thin",Y=[],V=new Set,Q=null,_=null,R={},X="",ut="sa",tt=!1,z=null,P=null,K;function ft(e){if(e=e.toLowerCase().replace(/\s+/g,"-"),R[e])return R[e];const i=Object.keys(R).filter(s=>{const a=s.toLowerCase().replace(/\s+/g,"-").replace(/^-/,"");return a.includes(e)||e.includes(a)});if(i.length>0)return i.flatMap(s=>R[s]);for(const s in R){const a=Object.keys(R[s]).filter(o=>{const m=o.toLowerCase().replace(/\s+/g,"-").replace(/^-/,"");return m.includes(e)||e.includes(m)});if(a.length>0)return a.flatMap(o=>R[s][o])}return null}function pt(e){if(!e||e.length<2)return[];e=e.replace(/\s+/g,"-").replace(/^-/,"");const i=ft(e);return i?[...new Set(i)].filter(s=>Y.includes(`-${s}`)||Y.includes(s)).map(s=>s.replace(/^-/,"")):Y.map(s=>{const a=s.startsWith("-")?s.substring(1):s;return{name:a,distance:mt(e.toLowerCase(),a.toLowerCase())}}).filter(s=>{const a=Math.min(Math.floor(e.length*.4),3);return s.distance>0&&s.distance<=a}).sort((s,a)=>s.distance-a.distance).slice(0,3).map(s=>s.name)}function mt(e,i){if(e.length===0)return i.length;if(i.length===0)return e.length;const s=Array(i.length+1).fill(null).map(()=>Array(e.length+1).fill(null));for(let a=0;a<=i.length;a++)s[a][0]=a;for(let a=0;a<=e.length;a++)s[0][a]=a;for(let a=1;a<=i.length;a++)for(let o=1;o<=e.length;o++)s[a][o]=i.charAt(a-1)===e.charAt(o-1)?s[a-1][o-1]:Math.min(s[a-1][o-1]+1,s[a][o-1]+1,s[a-1][o]+1);return s[i.length][e.length]}function gt(){document.getElementById("searchIcons").addEventListener("input",function(){X=this.value.trim().replace(/^-/,""),nt()})}function nt(){const e=X.toLowerCase().split(/\s+/).filter(a=>a.length>0).map(a=>a.replace(/^-/,""));document.querySelectorAll("#iconList li").forEach(a=>{const o=a.textContent.toLowerCase().replace(/^-/,""),m=e.every(y=>o.includes(y));a.classList.toggle("js-filter-hide",!m)}),et(),Q&&clearTimeout(Q),Q=setTimeout(()=>ht(X),1e3);const i=document.querySelectorAll("#iconList li:not(.js-filter-hide)").length,s=document.getElementById("suggestions");if(X.length>=2&&i<10){const a=pt(X);s.innerHTML=a.length>0?`<span class="suggest-title">Did you mean?</span> ${a.map(o=>`<span class="suggestion px-1" onclick="applySearch('${o}')">${o}</span>`).join(" ")}`:""}else s.innerHTML=""}async function st(e="sa"){if(tt){console.log("Already loading an icon set, please wait...");return}tt=!0;try{const i=document.getElementById("iconList");i.innerHTML='<div class="d-flex justify-content-center w-100 py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';const s=document.getElementById("searchIcons");s&&(s.value="",X=""),document.getElementById("suggestions").innerHTML="";const a={sa:{icons:"/json/sa-icons.json",mappings:"/json/sa-mappings.json",prefix:"sa"},base:{icons:"/json/sa-base.json",mappings:"/json/sa-mappings.json",prefix:"sa"},svg:{icons:"/json/sa-svg-icons.json",mappings:"/json/sa-svg-mappings.json",prefix:"svg"},fal:{icons:"/json/fa-icons.json",mappings:"/json/fa-mappings.json",prefix:"fal"},fas:{icons:"/json/fa-icons.json",mappings:"/json/fa-mappings.json",prefix:"fas"},far:{icons:"/json/fa-icons.json",mappings:"/json/fa-mappings.json",prefix:"far"},fab:{icons:"/json/fa-brands.json",mappings:"/json/fa-mappings.json",prefix:"fab"},fad:{icons:"/json/fa-duotone.json",mappings:"/json/fa-mappings.json",prefix:"fad"},material:{icons:"/json/material-icons.json",mappings:"/json/material-mappings.json",prefix:"material"}},o=a[e]||a.sa;try{const[m,y]=await Promise.all([fetch(o.icons),fetch(o.mappings)]);if(!m.ok||!y.ok)throw new Error("Failed to load resources");Y=(await m.json()).map(g=>g.replace(/^-/,"")),R=await y.json(),ut=e,yt(Y,o.prefix),document.getElementById("searchIcons").hasAttribute("data-initialized")||(gt(),document.getElementById("searchIcons").setAttribute("data-initialized","true")),console.log(`Successfully loaded icon set: ${e}`)}catch(m){console.error("Error loading icon set:",m),i.innerHTML=`<div class="alert alert-danger m-3">Failed to load icon set: ${m.message}</div>`}}finally{tt=!1}X&&nt(),et()}function yt(e,i){const s=document.getElementById("iconList");s.innerHTML=e.map(a=>{const o=a.replace(/^-/,""),m=vt(i,o),y=o;return`
            <li class="d-flex justify-content-center align-items-center" style="width: 85px;" data-icon-name="${y}">
                <a href="#" class="js-showcase-icon rounded color-fusion-300 p-0 m-0 d-flex flex-column w-100 shadow-hover-2 ${i==="svg"?"has-svg":""}">
                    <div class="icon-preview rounded-top w-100 position-relative">
                        <div class="icon-container rounded-top d-flex align-items-center justify-content-center w-100 pt-2 pb-2 pe-2 ps-2 position-absolute">
                            ${m}
                        </div>
                    </div>
                    <div class="rounded-bottom p-1 w-100 d-flex justify-content-center align-items-center text-center mt-auto">
                        <span class="nav-link-text small text-muted text-truncate">${y}</span>
                    </div>
                </a>
            </li>
        `}).join(""),et(),at()}function ht(e){e&&e.length>=2&&(V.add(e.replace(/^-/,"")),V.size>5&&V.delete([...V][0]),bt())}function bt(){const e=document.getElementById("searchHistory");e.innerHTML=[...V].map(i=>`<span class="badge bg-secondary me-1" onclick="applySearch('${i}')">
            <span class="text-truncate-xs overflow-hidden">${i}</span>
            <i class="sa sa-close ms-1" onclick="event.stopPropagation(); removeFromHistory('${i}')"></i>
        </span>`).join("")}function et(){const e=document.querySelectorAll("#iconList li:not(.js-filter-hide)").length,i=Y.length;document.querySelector(".results-count").textContent=`Showing ${e} of ${i} icons`}const vt=(e,i)=>{const s=i.replace(/^-/,"");switch(e){case"svg":return`<svg class="sa-icon ${dt}"><use href="/icons/sprite.svg#${s}"></use></svg>`;case"fal":return`<i class="fal fa-${s}"></i>`;case"fas":return`<i class="fas fa-${s}"></i>`;case"far":return`<i class="far fa-${s}"></i>`;case"fad":return`<i class="fad fa-${s}"></i>`;case"fab":return`<i class="fab fa-${s}"></i>`;case"material":return`<i class="material-icons">${s}</i>`;case"sa":return`<i class="sa sa-${s}"></i>`;case"base":return`<i class="sa base-${s}"></i>`;default:return`<i class="${e} ${e}-${s}"></i>`}};function at(){const e=document.querySelector("#example-modal-backdrop-transparent .modal-footer .btn-primary");e&&(e.disabled=!0,e.classList.add("disabled")),document.querySelectorAll(".js-showcase-icon").forEach(i=>{const s=i.cloneNode(!0);i.parentNode.replaceChild(s,i)}),document.querySelectorAll(".js-showcase-icon").forEach(i=>{i.addEventListener("click",function(s){s.preventDefault(),s.stopPropagation(),document.querySelectorAll(".js-showcase-icon").forEach(m=>{m.classList.remove("selected-icon")}),this.classList.add("selected-icon");const a=this.querySelector("svg")||this.querySelector("i"),o=this.querySelector(".icon-container");a&&o&&(z=o.innerHTML.trim(),console.log("Icon selected:",z),e&&(e.disabled=!1,e.classList.remove("disabled")))})})}function C(e,i="primary"){_&&_.hide();let s=document.querySelector(".toast-container");s||(s=document.createElement("div"),s.className="toast-container position-fixed top-0 end-0 p-3",document.body.appendChild(s));const a="toast-"+Date.now(),o=document.createElement("div");o.className=`toast hide align-items-center border-0 py-2 px-3 bg-${i} text-white`,o.id=a,o.setAttribute("role","alert"),o.setAttribute("aria-live","assertive"),o.setAttribute("aria-atomic","true"),o.style.setProperty("--bs-toast-max-width","auto"),o.innerHTML=`
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center justify-content-center">
                ${e}
            </div>
            <button type="button" class="btn btn-system ms-auto" data-bs-dismiss="toast" aria-label="Close">
                <svg class="sa-icon sa-icon-light">
                    <use href="/icons/sprite.svg#x"></use>
                </svg>
            </button>
        </div>
    `,s.appendChild(o),_=new bootstrap.Toast(o,{autohide:!0,delay:3e3}),o.addEventListener("hidden.bs.toast",function(){_=null,o.remove()}),_.show()}function ot(e){var i=document.getElementById("stackgenerator-container"),s=i.className.match(/icon-zoom-(\d+)/),a=s?parseInt(s[1],10):0;a>0&&(i.className=i.className.replace(/icon-zoom-\d+/,""));var o;if(e==="in")o=a<15?a+1:15;else if(e==="out")o=a>1?a-1:0;else return;o>0&&(i.className+=" icon-zoom-"+o)}document.getElementById("zoomInBtn").onclick=function(){ot("in")};document.getElementById("zoomOutBtn").onclick=function(){ot("out")};document.addEventListener("DOMContentLoaded",function(){const e=document.getElementById("stack-control"),i=document.getElementById("stackgenerator-container");if(!e||!i)return;const s=e.querySelector(".panel-hdr > h2");if(!s)return;s.style.cursor="move",e.style.position="absolute",e.style.zIndex="50";const a=16,o=16,m=20;let y=!1,g,E,h,$;G();function M(w=!1){const S=i.getBoundingClientRect(),L=e.getBoundingClientRect();let I=parseInt(e.style.left)||0,j=parseInt(e.style.top)||0;const W=S.width-L.width-a,O=S.height-L.height-o,F=a,d=o;let p=Math.max(F,Math.min(W,I)),l=Math.max(d,Math.min(O,j));w&&(e.style.transition="all 0.3s ease-out",setTimeout(()=>{e.style.transition=""},300)),e.style.left=p+"px",e.style.top=l+"px"}function Z(){const w=i.getBoundingClientRect(),S=e.getBoundingClientRect(),L=w.width-S.width-a;e.style.top=o+"px",e.style.left=L+"px"}if(Z(),s.addEventListener("mousedown",function(w){if(w.button!==0)return;w.preventDefault(),y=!0,g=w.clientX,E=w.clientY;const S=e.getBoundingClientRect();h=S.left,$=S.top,e.classList.add("is-dragging");const L=document.createElement("div");L.id="drag-overlay",L.style.position="fixed",L.style.top="0",L.style.left="0",L.style.width="100%",L.style.height="100%",L.style.zIndex="999",L.style.cursor="move",document.body.appendChild(L)}),document.addEventListener("mousemove",function(w){if(!y)return;const S=w.clientX-g,L=w.clientY-E,I=i.getBoundingClientRect(),j=e.getBoundingClientRect();let W=h+S,O=$+L;const F=I.right-j.width-a,d=I.bottom-j.height-o,p=I.left+a,l=I.top+o;W=Math.max(p,Math.min(F,W)),O=Math.max(l,Math.min(d,O)),e.style.left=W-I.left+"px",e.style.top=O-I.top+"px"}),document.addEventListener("mouseup",function(){if(!y)return;y=!1,e.classList.remove("is-dragging");const w=document.getElementById("drag-overlay");w&&w.remove();const S=e.getBoundingClientRect(),L=i.getBoundingClientRect();let I=S.left-L.left,j=S.top-L.top;I=Math.round(I/m)*m,j=Math.round(j/m)*m,e.style.transition="all 0.2s ease-out",e.style.left=I+"px",e.style.top=j+"px",setTimeout(()=>{e.style.transition=""},200)}),window.addEventListener("resize",function(){requestAnimationFrame(function(){M(!0)})}),typeof ResizeObserver<"u")new ResizeObserver(function(){M(!0)}).observe(i);else{let w=i.clientWidth,S=i.clientHeight;setInterval(function(){(w!==i.clientWidth||S!==i.clientHeight)&&(w=i.clientWidth,S=i.clientHeight,M(!0))},250)}const T=document.createElement("style");T.textContent=`
        #stack-control.is-dragging {
            opacity: 0.8;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }
        #stack-control .panel-hdr:hover {
            background-color: rgba(0,0,0,0.05);
        }
    `,document.head.appendChild(T)});document.addEventListener("DOMContentLoaded",function(){function i(){const m=document.getElementById("example-modal-backdrop-transparent"),y=m.querySelector(".modal-footer .btn-primary"),g=document.getElementById("my-icon"),h=document.getElementById("stack-control").querySelector(".panel-content");m.addEventListener("hidden.bs.modal",function(){z=null,document.querySelectorAll("#iconList li a.js-showcase-icon").forEach(d=>{d.classList.remove("selected-icon")}),y&&(y.disabled=!0,y.classList.add("disabled")),console.log("Modal closed, reset selection state")}),m.addEventListener("shown.bs.modal",function(){console.log("Modal opened, reattaching icon click handlers"),setTimeout(at,100)});let $=null;function M(){$&&$.destroy(),$=new rt(h,{animation:150,handle:".drag-handle",ghostClass:"sortable-ghost",chosenClass:"sortable-chosen",dragClass:"sortable-drag",onEnd:function(d){Z()}})}function Z(){const d=h.querySelectorAll(".stack-layers"),p=[],l={},n=g.querySelectorAll(".icon-layers");n.forEach((r,u)=>{const f=r.querySelector("svg, i");let b="0";if(f){for(const q of f.classList)if(q.startsWith("rotate-")){b=q.replace("rotate-","");break}}let A="10";if(f){for(const q of f.classList)if(q.startsWith("alpha-")){A=q.replace("alpha-","");break}}l[u]={rotation:b,opacity:A}});const t={};d.forEach((r,u)=>{const f=parseInt(r.dataset.layerIndex);f>=0&&f<n.length&&(p.push(n[f]),t[f]=u)}),g.innerHTML="",p.forEach(r=>{g.appendChild(r)}),T(),h.querySelectorAll(".stack-layers").forEach(r=>{const u=parseInt(r.dataset.layerIndex);let f=null;for(const[b,A]of Object.entries(t))if(parseInt(A)===u){f=parseInt(b);break}if(f!==null&&l[f]){const b=l[f],A=r.querySelector(".rotation-slider");if(A){A.value=b.rotation;const k=A.parentElement.querySelector(".rotation-value");k&&(k.textContent=`${b.rotation}°`)}const q=r.querySelector(".opacity-slider");if(q){q.value=b.opacity;const k=q.parentElement.querySelector(".opacity-value");k&&(k.textContent=`${b.opacity}0%`)}}})}function T(){h.innerHTML="",g.querySelectorAll(".icon-layers").forEach((l,n)=>{const t=l.innerHTML.trim();l.className;const c=document.createElement("div");c.className="stack-layers d-flex flex-column",c.dataset.layerIndex=n;let r="Unknown Icon";if(t.includes('class="')){const v=t.match(/class="([^"]+)"/);if(v&&v[1])if(t.includes("<svg")&&t.includes('href="')){const U=t.match(/href="[^#]+#([^"]+)"/);if(U&&U[1])r=U[1];else{const x=v[1].split(" ").filter(B=>!["sa-icon","sa","stack-1x","stack-2x","stack-3x","fal","far","fas","fad","fab","text-primary","text-secondary","text-success","text-danger","text-warning","text-info","text-dark","text-light","sa-thin","sa-regular","sa-medium","sa-bold","sa-nofill","alpha-1","alpha-2","alpha-3","alpha-4","alpha-5","alpha-6","alpha-7","alpha-8","alpha-9","alpha-10"].includes(B)&&!B.startsWith("rotate-"));x.some(B=>B.startsWith("sa-"))?r=x.find(B=>B.startsWith("sa-")):x.some(B=>B.startsWith("base-"))?r=x.find(B=>B.startsWith("base-")):x.length>0&&(r=x.join(" "))}}else{const H=v[1].split(" ").filter(x=>!["sa-icon","sa","stack-1x","stack-2x","stack-3x","fal","far","fas","fad","fab","text-primary","text-secondary","text-success","text-danger","text-warning","text-info","text-dark","text-light","sa-thin","sa-regular","sa-medium","sa-bold","sa-nofill","alpha-1","alpha-2","alpha-3","alpha-4","alpha-5","alpha-6","alpha-7","alpha-8","alpha-9","alpha-10"].includes(x)&&!x.startsWith("rotate-"));H.some(x=>x.startsWith("sa-"))?r=H.find(x=>x.startsWith("sa-")):H.some(x=>x.startsWith("base-"))?r=H.find(x=>x.startsWith("base-")):H.some(x=>x.startsWith("fa-"))?r=H.find(x=>x.startsWith("fa-")):H.length>0&&(r=H.join(" "))}}const u=t.includes("<svg"),f=l.querySelector("svg, i");let b="2x";f&&(f.classList.contains("stack-1x")?b="1x":f.classList.contains("stack-2x")?b="2x":f.classList.contains("stack-3x")&&(b="3x"));let A="0";if(f){for(const v of f.classList)if(v.startsWith("rotate-")){A=v.replace("rotate-","");break}}let q="10";if(f){for(const v of f.classList)if(v.startsWith("alpha-")){q=v.replace("alpha-","");break}}let k="";const it=["primary","secondary","success","danger","warning","info","dark","light"];for(const v of it)if(u&&l.querySelector(`svg.sa-icon-${v}`)){k=v;break}else if(!u&&l.querySelector(`i.text-${v}`)){k=v;break}let N="",J=!1;if(u){const v=l.querySelector("svg");v&&(v.classList.contains("sa-thin")?N="sa-thin":v.classList.contains("sa-regular")?N="sa-regular":v.classList.contains("sa-medium")?N="sa-medium":v.classList.contains("sa-bold")&&(N="sa-bold"),J=v.classList.contains("sa-nofill"))}else N="",J=!1;const ct=`
                    <div class="d-flex flex-row border-bottom position-relative">
                        <div class="stack-icon-preview flex-shrink-0 d-flex align-items-center justify-content-center">
                            ${t}
                        </div>
                        <div class="icon-settings d-flex flex-column flex-grow-1 border-start px-2 py-2 pe-4">
                            <div class="d-flex justify-content-between align-items-center flex-grow-1">
                                <div class="fs-sm">
                                    <span class="text-primary fw-bold">${r}</span>
                                    <span class="badge badge-icon">
                                        Layer ${n+1}
                                    </span>
                                </div>
                                <div class="layer-actions d-flex gap-1">

                                    <button type="button" class="btn btn-icon btn-outline-secondary edit-layer p-1 w-auto h-auto border-0" data-layer="${n}">
                                        <i class="sa sa-reload"></i>
                                    </button>
                                    <button type="button" class="btn btn-icon btn-outline-secondary tune-layer p-1 w-auto h-auto border-0" data-layer="${n}" data-bs-toggle="collapse" data-bs-target="#layer-controls-${n}" aria-expanded="false">
                                        <i class="sa sa-settings"></i>
                                    </button>
                                    <button type="button" class="btn btn-icon btn-outline-danger delete-layer p-1 w-auto h-auto border-0" data-layer="${n}">
                                        <i class="sa sa-close"></i>
                                    </button>
                                    <div class="drag-handle btn btn-icon btn-default-outline">
                                        <svg class="sa-icon sa-thin">
                                            <use href="/icons/sprite.svg#more-vertical"></use>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `,lt=`
                    <div class="layer-control-panel collapse" id="layer-controls-${n}" data-layer="${n}">
                        <div class="control-panel-content shadow-inset-2 px-3 pt-1 pb-3" onclick="event.stopPropagation();">
                            <!-- Size controls -->
                            <div class="mb-2">
                                <label class="form-label mb-1 fs-sm">Size</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="size-${n}" id="size-1x-${n}" value="1x" ${b==="1x"?"checked":""}>
                                    <label class="btn btn-outline-secondary btn-xs" for="size-1x-${n}">Small</label>

                                    <input type="radio" class="btn-check" name="size-${n}" id="size-2x-${n}" value="2x" ${b==="2x"?"checked":""}>
                                    <label class="btn btn-outline-secondary btn-xs" for="size-2x-${n}">Medium</label>

                                    <input type="radio" class="btn-check" name="size-${n}" id="size-3x-${n}" value="3x" ${b==="3x"?"checked":""}>
                                    <label class="btn btn-outline-secondary btn-xs" for="size-3x-${n}">Large</label>
                                </div>
                            </div>

                            <!-- Rotation controls (slider) -->
                            <div class="mb-2">
                                <label class="form-label mb-1 fs-sm">Rotation: <span class="rotation-value">${A}°</span></label>
                                <input type="range" class="form-range rotation-slider" min="0" max="360" step="45" value="${A}" data-layer="${n}">
                            </div>

                            <!-- Opacity slider -->
                            <div class="mb-2">
                                <label class="form-label mb-1 fs-sm">Opacity: <span class="opacity-value">${q}0%</span></label>
                                <input type="range" class="form-range opacity-slider" min="1" max="10" step="1" value="${q}" data-layer="${n}">
                            </div>

                            <!-- SVG specific controls -->
                            ${u?`
                            <div class="mb-2">
                                <label class="form-label mb-1 fs-sm">SVG Weight</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="weight-${n}" id="weight-thin-${n}" value="sa-thin" ${N==="sa-thin"?"checked":""}>
                                    <label class="btn btn-outline-secondary btn-xs" for="weight-thin-${n}">Thin</label>

                                    <input type="radio" class="btn-check" name="weight-${n}" id="weight-regular-${n}" value="sa-regular" ${N==="sa-regular"?"checked":""}>
                                    <label class="btn btn-outline-secondary btn-xs" for="weight-regular-${n}">Normal</label>

                                    <input type="radio" class="btn-check" name="weight-${n}" id="weight-medium-${n}" value="sa-medium" ${N==="sa-medium"?"checked":""}>
                                    <label class="btn btn-outline-secondary btn-xs" for="weight-medium-${n}">Medium</label>

                                    <input type="radio" class="btn-check" name="weight-${n}" id="weight-bold-${n}" value="sa-bold" ${N==="sa-bold"?"checked":""}>
                                    <label class="btn btn-outline-secondary btn-xs" for="weight-bold-${n}">Bold</label>
                                </div>
                            </div>

                            <div class="mb-2 form-check">
                                <input type="checkbox" class="form-check-input svg-nofill-check" id="nofill-${n}" ${J?"checked":""} data-layer="${n}">
                                <label class="form-check-label" for="nofill-${n}">No Fill</label>
                            </div>
                            `:""}

                            <!-- Color controls -->
                            <div>
                                <label class="form-label mb-1 fs-sm">Color</label>
                                <div class="d-flex flex-wrap gap-1">
                                    <button type="button" class="btn btn-icon btn-outline-default color-btn ${k?"":"active"}" data-color="" data-layer="${n}">
                                        <i class="sa sa-ban"></i>
                                    </button>
                                    <button type="button" class="btn btn-icon btn-primary color-btn ${k==="primary"?"active":""}" data-color="primary" data-layer="${n}"></button>
                                    <button type="button" class="btn btn-icon btn-secondary color-btn ${k==="secondary"?"active":""}" data-color="secondary" data-layer="${n}"></button>
                                    <button type="button" class="btn btn-icon btn-success color-btn ${k==="success"?"active":""}" data-color="success" data-layer="${n}"></button>
                                    <button type="button" class="btn btn-icon btn-danger color-btn ${k==="danger"?"active":""}" data-color="danger" data-layer="${n}"></button>
                                    <button type="button" class="btn btn-icon btn-warning color-btn ${k==="warning"?"active":""}" data-color="warning" data-layer="${n}"></button>
                                    <button type="button" class="btn btn-icon btn-info color-btn ${k==="info"?"active":""}" data-color="info" data-layer="${n}"></button>
                                    <button type="button" class="btn btn-icon btn-dark color-btn ${k==="dark"?"active":""}" data-color="dark" data-layer="${n}"></button>
                                    <button type="button" class="btn btn-icon btn-default color-btn ${k==="light"?"active":""}" data-color="light" data-layer="${n}"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;c.innerHTML=ct+lt,h.appendChild(c)}),h.querySelectorAll(".edit-layer").forEach(l=>{l.addEventListener("click",function(n){n.stopPropagation();const t=parseInt(this.dataset.layer);O(t)})}),h.querySelectorAll(".delete-layer").forEach(l=>{l.addEventListener("click",function(n){n.stopPropagation();const t=parseInt(this.dataset.layer);F(t)})}),h.querySelectorAll(".tune-layer").forEach(l=>{l.addEventListener("click",function(n){n.stopPropagation();const t=this.getAttribute("data-bs-target");document.querySelector(t).classList.contains("show")||document.querySelectorAll(".layer-control-panel.show").forEach(u=>{if(u.id!==t.substring(1)){const f=bootstrap.Collapse.getInstance(u);f&&f.hide()}})})}),h.querySelectorAll(".control-panel-content").forEach(l=>{l.addEventListener("click",function(n){n.stopPropagation()})}),h.querySelectorAll(".layer-control-panel input, .layer-control-panel label, .layer-control-panel button").forEach(l=>{l.addEventListener("click",function(n){n.stopPropagation()})}),M(),h.querySelectorAll('input[type="radio"][name^="size-"]').forEach(l=>{l.addEventListener("change",function(n){n.stopPropagation();const t=parseInt(this.id.split("-")[2]),c=this.value;w(t,c)})}),document.querySelectorAll(".rotation-slider").forEach(l=>{l.addEventListener("input",function(n){n.stopPropagation();const t=parseInt(this.dataset.layer),c=this.value,r=this.parentElement.querySelector(".rotation-value");r&&(r.textContent=`${c}°`),S(t,c==="360"?"0":c)})}),h.querySelectorAll(".opacity-slider").forEach(l=>{l.addEventListener("input",function(n){n.stopPropagation();const t=parseInt(this.dataset.layer),c=this.value,r=this.parentElement.querySelector(".opacity-value");r&&(r.textContent=`${c}0%`),L(t,c)})}),h.querySelectorAll('input[type="radio"][name^="weight-"]').forEach(l=>{l.addEventListener("change",function(n){n.stopPropagation();const t=parseInt(this.id.split("-")[2]),c=this.value;j(t,c)})}),h.querySelectorAll(".svg-nofill-check").forEach(l=>{l.addEventListener("change",function(n){n.stopPropagation();const t=parseInt(this.dataset.layer),c=this.checked;W(t,c)})}),h.querySelectorAll(".color-btn").forEach(l=>{l.addEventListener("click",function(n){n.stopPropagation();const t=parseInt(this.dataset.layer),c=this.dataset.color;this.parentElement.querySelectorAll(".color-btn").forEach(u=>u.classList.remove("active")),this.classList.add("active"),I(t,c)})})}function w(d,p){const l=g.querySelectorAll(".icon-layers");if(d>=0&&d<l.length){const t=l[d].querySelector("svg, i");t&&(t.classList.remove("stack-1x","stack-2x","stack-3x"),t.classList.add(`stack-${p}`))}}function S(d,p){const l=g.querySelectorAll(".icon-layers");if(d>=0&&d<l.length){const t=l[d].querySelector("svg, i");if(t){for(let r=0;r<=360;r+=45)t.classList.remove(`rotate-${r}`);p!=="0"&&p!=="360"&&t.classList.add(`rotate-${p}`);const c=h.querySelector(`.stack-layers[data-layer-index="${d}"] .stack-icon-preview`);if(c){const r=c.querySelector("svg, i");if(r){for(let u=0;u<=360;u+=45)r.classList.remove(`rotate-${u}`);p!=="0"&&p!=="360"&&r.classList.add(`rotate-${p}`)}}}}}function L(d,p){const l=g.querySelectorAll(".icon-layers");if(d>=0&&d<l.length){const t=l[d].querySelector("svg, i");if(t){for(let r=1;r<=10;r++)t.classList.remove(`alpha-${r}`);p!=="10"&&t.classList.add(`alpha-${p}`);const c=h.querySelector(`.stack-layers[data-layer-index="${d}"] .stack-icon-preview`);if(c){const r=c.querySelector("svg, i");if(r){for(let u=1;u<=10;u++)r.classList.remove(`alpha-${u}`);p!=="10"&&r.classList.add(`alpha-${p}`)}}}}}function I(d,p){const l=g.querySelectorAll(".icon-layers");if(d>=0&&d<l.length){const n=l[d];if(n.innerHTML.includes("<svg")){const c=n.querySelector("svg");if(c){const r=["primary","secondary","success","danger","warning","info","dark","light"].map(f=>`sa-icon-${f}`);r.forEach(f=>{c.classList.remove(f)}),p&&c.classList.add(`sa-icon-${p}`);const u=h.querySelector(`.stack-layers[data-layer-index="${d}"] .stack-icon-preview`);if(u){const f=u.querySelector("svg");f&&(r.forEach(b=>{f.classList.remove(b)}),p&&f.classList.add(`sa-icon-${p}`))}}}else{const c=n.querySelector("i");if(c){const r=["text-primary","text-secondary","text-success","text-danger","text-warning","text-info","text-dark","text-light"];r.forEach(f=>{c.classList.remove(f)}),p&&c.classList.add(`text-${p}`);const u=h.querySelector(`.stack-layers[data-layer-index="${d}"] .stack-icon-preview`);if(u){const f=u.querySelector("i");f&&(r.forEach(b=>{f.classList.remove(b)}),p&&f.classList.add(`text-${p}`))}}}}}function j(d,p){const l=g.querySelectorAll(".icon-layers");if(d>=0&&d<l.length){const t=l[d].querySelector("svg");if(t){t.classList.remove("sa-thin","sa-regular","sa-medium","sa-bold"),p&&t.classList.add(p);const c=h.querySelector(`.stack-layers[data-layer-index="${d}"] .stack-icon-preview`);if(c){const r=c.querySelector("svg");r&&(r.classList.remove("sa-thin","sa-regular","sa-medium","sa-bold"),p&&r.classList.add(p))}}}}function W(d,p){const l=g.querySelectorAll(".icon-layers");if(d>=0&&d<l.length){const t=l[d].querySelector("svg");if(t){p?t.classList.add("sa-nofill"):t.classList.remove("sa-nofill");const c=h.querySelector(`.stack-layers[data-layer-index="${d}"] .stack-icon-preview`);if(c){const r=c.querySelector("svg");r&&(p?r.classList.add("sa-nofill"):r.classList.remove("sa-nofill"))}}}}T(),y.addEventListener("click",function(){if(console.log("Select button clicked, selectedIcon:",z),!z){C("Please select an icon first","warning");return}const d=g.querySelectorAll(".icon-layers");if(P!==null){if(P>=0&&P<d.length){const t=d[P].querySelector("svg, i"),c={size:t?t.classList.contains("stack-1x")?"stack-1x":t.classList.contains("stack-2x")?"stack-2x":t.classList.contains("stack-3x")?"stack-3x":"stack-2x":"stack-2x",rotation:t?t.classList.contains("rotate-45")?"rotate-45":t.classList.contains("rotate-90")?"rotate-90":t.classList.contains("rotate-135")?"rotate-135":t.classList.contains("rotate-180")?"rotate-180":t.classList.contains("rotate-225")?"rotate-225":t.classList.contains("rotate-270")?"rotate-270":t.classList.contains("rotate-315")?"rotate-315":"":"",opacity:t?t.classList.contains("alpha-1")?"alpha-1":t.classList.contains("alpha-2")?"alpha-2":t.classList.contains("alpha-3")?"alpha-3":t.classList.contains("alpha-4")?"alpha-4":t.classList.contains("alpha-5")?"alpha-5":t.classList.contains("alpha-6")?"alpha-6":t.classList.contains("alpha-7")?"alpha-7":t.classList.contains("alpha-8")?"alpha-8":t.classList.contains("alpha-9")?"alpha-9":"":"",color:t?t.classList.contains("text-primary")?"text-primary":t.classList.contains("text-secondary")?"text-secondary":t.classList.contains("text-success")?"text-success":t.classList.contains("text-danger")?"text-danger":t.classList.contains("text-warning")?"text-warning":t.classList.contains("text-info")?"text-info":t.classList.contains("text-dark")?"text-dark":t.classList.contains("text-light")?"text-light":"":"",svgWeight:t&&t.tagName==="svg"?t.classList.contains("sa-thin")?"sa-thin":t.classList.contains("sa-regular")?"sa-regular":t.classList.contains("sa-medium")?"sa-medium":t.classList.contains("sa-bold")?"sa-bold":"":"",svgColor:t&&t.tagName==="svg"?t.classList.contains("sa-icon-primary")?"sa-icon-primary":t.classList.contains("sa-icon-secondary")?"sa-icon-secondary":t.classList.contains("sa-icon-success")?"sa-icon-success":t.classList.contains("sa-icon-danger")?"sa-icon-danger":t.classList.contains("sa-icon-warning")?"sa-icon-warning":t.classList.contains("sa-icon-info")?"sa-icon-info":t.classList.contains("sa-icon-dark")?"sa-icon-dark":t.classList.contains("sa-icon-light")?"sa-icon-light":"":"",noFill:t&&t.tagName==="svg"?t.classList.contains("sa-nofill"):!1},r=document.createElement("div");r.innerHTML=z;const u=r.querySelector("svg, i");if(u){if(u.classList.remove("stack-1x","stack-2x","stack-3x"),u.classList.remove("rotate-45","rotate-90","rotate-135","rotate-180","rotate-225","rotate-270","rotate-315"),u.classList.remove("alpha-1","alpha-2","alpha-3","alpha-4","alpha-5","alpha-6","alpha-7","alpha-8","alpha-9"),u.classList.remove("text-primary","text-secondary","text-success","text-danger","text-warning","text-info","text-dark","text-light"),u.classList.remove("sa-icon-primary","sa-icon-secondary","sa-icon-success","sa-icon-danger","sa-icon-warning","sa-icon-info","sa-icon-dark","sa-icon-light"),u.classList.remove("sa-thin","sa-regular","sa-medium","sa-bold","sa-nofill"),u.tagName.toLowerCase()!=="svg"){const f=/^(sa-(thin|regular|medium|bold|nofill))$/;Array.from(u.classList).forEach(b=>{f.test(b)&&u.classList.remove(b)})}if(u.classList.add(c.size),c.rotation&&u.classList.add(c.rotation),c.opacity&&u.classList.add(c.opacity),u.tagName.toLowerCase()==="svg"){if(c.svgWeight&&u.classList.add(c.svgWeight),c.svgColor&&u.classList.add(c.svgColor),c.noFill&&u.classList.add("sa-nofill"),!c.svgColor&&c.color){const f={"text-primary":"sa-icon-primary","text-secondary":"sa-icon-secondary","text-success":"sa-icon-success","text-danger":"sa-icon-danger","text-warning":"sa-icon-warning","text-info":"sa-icon-info","text-dark":"sa-icon-dark","text-light":"sa-icon-light"};f[c.color]&&u.classList.add(f[c.color])}}else if(c.color&&u.classList.add(c.color),!c.color&&c.svgColor){const f={"sa-icon-primary":"text-primary","sa-icon-secondary":"text-secondary","sa-icon-success":"text-success","sa-icon-danger":"text-danger","sa-icon-warning":"text-warning","sa-icon-info":"text-info","sa-icon-dark":"text-dark","sa-icon-light":"text-light"};f[c.svgColor]&&u.classList.add(f[c.svgColor])}}d[P].innerHTML=r.innerHTML}P=null}else{if(d.length>=4){C("Maximum of 4 icon layers allowed","danger");return}const n=document.createElement("div");n.className="icon-layers";const t=document.createElement("div");t.innerHTML=z;const c=t.querySelector("svg, i");c&&(c.classList.remove("stack-1x","stack-2x","stack-3x"),c.classList.add("stack-2x")),n.innerHTML=t.innerHTML,g.appendChild(n)}T(),G(),z=null,document.querySelectorAll("#iconList li a.js-showcase-icon").forEach(n=>{n.classList.remove("selected-icon")}),y.disabled=!0,y.classList.add("disabled");const p=m.querySelector(".modal-title");p&&(p.textContent="Select an Icon"),bootstrap.Modal.getInstance(m).hide()});function O(d){const p=g.querySelectorAll(".icon-layers");if(d>=0&&d<p.length){P=d,z=null,document.querySelectorAll("#iconList li a.js-showcase-icon").forEach(t=>{t.classList.remove("selected-icon")}),y&&(y.disabled=!0,y.classList.add("disabled"));const l=m.querySelector(".modal-title");l&&(l.textContent=`Editing Icon for Layer ${d+1}`),new bootstrap.Modal(m).show()}}function F(d){const p=g.querySelectorAll(".icon-layers");d>=0&&d<p.length&&confirm("Are you sure you want to delete this icon layer?")&&(p[d].remove(),T(),G())}document.getElementById("add-layer").addEventListener("click",function(d){if(g.querySelectorAll(".icon-layers").length>=4)return C("Maximum of 4 icon layers allowed","danger"),d.stopPropagation(),!1;P=null,z=null;const l=m.querySelector(".modal-title");l&&(l.textContent="Select an Icon"),y&&(y.disabled=!0,y.classList.add("disabled"))}),document.getElementById("reset-layers").addEventListener("click",function(){confirm("Are you sure you want to reset all icon layers?")&&(g.innerHTML="",T(),G(),C("All icon layers have been reset","primary"))})}const s=document.createElement("style");s.textContent=`
        .js-showcase-icon.selected-icon {
            border: 2px solid #2196F3;
            box-shadow: 0 0 0 4px rgba(33, 150, 243, 0.3);
            transform: scale(1.05);
        }
        .layer-actions {
            display: flex;
            gap: 4px;
        }
        .stack-icon-preview {
            min-width: 50px;
            min-height: 50px;
        }

        /* Drag and drop styles */
        .sortable-ghost {
            opacity: 0.4;
            background-color: #f8f9fa;
        }
        .sortable-chosen {
            background-color: #f0f0f0;
        }
        .sortable-drag {
            opacity: 0.8;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        .drag-handle {
            cursor: move;
        }
        .drag-handle:active {
            cursor: grabbing;
        }

        /* Fine-tuning controls styles */
        .color-btn {
            width: 24px;
            height: 24px;
            border-radius: 4px;
            position: relative;
        }
        .color-btn.active::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 12px;
            text-shadow: 0 0 2px rgba(0,0,0,0.5);
        }
        .color-btn[data-color="light"].active::after {
            color: #333;
        }


        /* Prevent collapse when clicking inside controls */
        .collapse.show {
            pointer-events: auto;
        }

        .collapse.show .mt-2.mb-2 {
            pointer-events: auto;
        }

        /* Make sure the tune button still works */
        .tune-layer {
            z-index: 2;
        }

        /* Prevent text selection when clicking controls */
        .control-panel-content {
            user-select: text;
            pointer-events: auto !important;
        }

        /* Ensure labels don't collapse panels */
        .form-label, .form-check-label {
            pointer-events: auto;
        }

        /* Ensure buttons in control panel don't collapse */
        .control-panel-content button,
        .control-panel-content input,
        .control-panel-content label {
            pointer-events: auto;
        }

        /* Additional fixes for collapse issue */
        .layer-control-panel {
            position: relative;
            z-index: 10;
        }

        .layer-control-panel .form-range,
        .layer-control-panel .btn-group,
        .layer-control-panel .form-check,
        .layer-control-panel .d-flex {
            pointer-events: auto !important;
            position: relative;
            z-index: 11;
        }
    `,document.head.appendChild(s),i();const a=document.getElementById("copy-icon");a&&a.addEventListener("click",function(){o()});function o(){const m=document.getElementById("my-icon");if(!m){C("No icon found to copy","danger");return}const y=m.querySelectorAll(".icon-layers");if(y.length<2){C("Incomplete icon. A minimum of 2 layers is required.","warning");return}let g='<div class="stack-icon">';y.forEach(E=>{g+=E.outerHTML}),g+="</div>",navigator.clipboard.writeText(g).then(()=>C("Icon copied to clipboard!","success")).catch(()=>C("Failed to copy icon","danger"))}});const Lt="IconStackDB",D="savedIcons",xt=1;function wt(){return new Promise((e,i)=>{const s=indexedDB.open(Lt,xt);s.onerror=()=>i(s.error),s.onsuccess=()=>{K=s.result,e(K)},s.onupgradeneeded=a=>{const o=a.target.result;if(!o.objectStoreNames.contains(D)){const m=o.createObjectStore(D,{keyPath:"id",autoIncrement:!0});m.createIndex("name","name",{unique:!0}),m.createIndex("createdAt","createdAt",{unique:!1})}}})}async function St(e){const a=K.transaction([D],"readonly").objectStore(D).getAll();return new Promise((o,m)=>{a.onsuccess=()=>{const g=a.result.some(E=>E.html===e);o(g)},a.onerror=()=>m(a.error)})}async function kt(e){const o=K.transaction([D],"readonly").objectStore(D).index("name").get(e);return new Promise((m,y)=>{o.onsuccess=()=>{m(o.result!==void 0)},o.onerror=()=>y(o.error)})}async function Et(e,i){const a=K.transaction([D],"readwrite").objectStore(D),o={name:e,html:i,createdAt:new Date().toISOString(),updatedAt:new Date().toISOString()};return new Promise((m,y)=>{const g=a.add(o);g.onsuccess=()=>m(g.result),g.onerror=()=>y(g.error)})}function $t(){return document.getElementById("iconNamingModal")||document.body.insertAdjacentHTML("beforeend",`
        <div class="modal fade" id="iconNamingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Save Icon</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="iconName" class="form-label">Icon Name (no spaces, max 15 characters)</label>
                            <input type="text" class="form-control" id="iconName" maxlength="15"
                                pattern="[A-Za-z0-9_\\-]+" placeholder="Enter a unique name for your icon">
                            <div class="invalid-feedback">Name must not contain spaces or special characters (except - and _), and must be unique.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" id="confirmSave" disabled>Save</button>
                    </div>
                </div>
            </div>
        </div>
    `),new bootstrap.Modal(document.getElementById("iconNamingModal"))}async function It(){const e=document.getElementById("my-icon");if(!e){C("No icon found to save","danger");return}if(e.querySelectorAll(".icon-layers").length<2){C("Incomplete icon. A minimum of 2 layers is required.","warning");return}const s=e.innerHTML;if(await St(s)){C("This icon already exists in your saved icons","warning");return}const o=$t(),m=document.getElementById("iconNamingModal"),y=m.querySelector("#iconName"),g=m.querySelector("#confirmSave"),E=m.querySelector(".invalid-feedback");y.value="",y.classList.remove("is-invalid"),E.style.display="none",g.disabled=!0;function h($){return $.replace(/\s+/g,"").replace(/[^\w\-]/g,"").substring(0,15)}y.addEventListener("input",async function(){const $=h(this.value);this.value!==$&&(this.value=$);const M=$.trim();if(!M){this.classList.add("is-invalid"),E.style.display="block",g.disabled=!0;return}await kt(M)?(this.classList.add("is-invalid"),E.textContent="This name is already taken.",E.style.display="block",g.disabled=!0):this.checkValidity()?(this.classList.remove("is-invalid"),E.style.display="none",g.disabled=!1):(this.classList.add("is-invalid"),E.textContent="Name must not contain spaces or special characters (except - and _).",E.style.display="block",g.disabled=!0)}),g.addEventListener("click",async function(){const $=y.value.trim();if(!(!$||!y.checkValidity()))try{const M=h($);await Et(M,s),C("Icon saved successfully!","success"),o.hide()}catch(M){console.error("Error saving icon:",M),C("Failed to save icon","danger")}}),o.show()}function G(){const e=document.getElementById("save-icon"),i=document.getElementById("copy-icon"),s=document.getElementById("my-icon");if(!s){e&&(e.disabled=!0),i&&(i.disabled=!0);return}const o=s.querySelectorAll(".icon-layers").length<2;e&&(e.disabled=o),i&&(i.disabled=o)}document.addEventListener("DOMContentLoaded",async function(){try{await wt(),console.log("IndexedDB initialized successfully"),G()}catch(e){console.error("Error initializing IndexedDB:",e)}});document.getElementById("save-icon").addEventListener("click",It);document.addEventListener("DOMContentLoaded",function(){st("svg"),document.getElementById("iconSetSelect").onchange=function(){var e=this.value;st(e)}});
