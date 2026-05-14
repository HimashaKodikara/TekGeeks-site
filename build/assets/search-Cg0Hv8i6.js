document.addEventListener("DOMContentLoaded",function(){const m=document.querySelector("#tab-images");if(m){let b=function(t,i){a(t,i),g.show()},a=function(t,i){const o=e.querySelector(".img-preview");o.src=t,e.querySelector(".image-title").textContent=i.dataset.imgTitle||"",e.querySelector(".image-description").textContent=i.dataset.imgDescription||"",e.querySelector(".image-category").textContent=i.dataset.imgCategory||"",e.querySelector(".image-date").textContent=i.dataset.imgDate||"",e.querySelector(".image-source").textContent=i.dataset.imgSource||""},u=function(){g.hide()},c=function(){r.classList.toggle("hidden",n===0),l.classList.toggle("hidden",n===s.length-1)};var f=b,h=a,y=u,x=c;const e=document.createElement("div");e.className="modal fade image-preview-modal",e.id="imagePreviewModal",e.tabIndex="-1",e.setAttribute("aria-hidden","true"),e.innerHTML=`
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body p-0 position-relative d-flex align-items-center justify-content-center">
                        <div class="d-flex flex-column flex-lg-row shadow rounded overflow-hidden border border-3 border-light">
                            <!-- Left Info Panel -->
                            <div class="order-2 order-lg-1 flex-shrink-0" style="width: 300px;">
                                <div class="d-flex flex-column h-100 p-0">
                                    <div class="flex-grow-1 p-3">
                                        <h5 class="image-title fw-bold mb-3"></h5>
                                        <p class="image-description text-muted mb-4"></p>

                                        <div class="mb-3">
                                            <div class="text-muted mb-1 fs-sm">Date</div>
                                            <div class="image-date"></div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="text-muted mb-1 fs-sm">Source</div>
                                            <div class="image-source text-primary text-decoration-underline link-offset-1 link-underline link-underline-opacity-75"></div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="text-muted mb-1 fs-sm">Tags</div>
                                            <div class="image-category badge bg-secondary"></div>
                                        </div>
                                    </div>
                                    <div class="px-3 pt-2 pb-0">
                                        <p class="text-muted fs-nano mb-2">
                                            Images may be subject to copyright. Please check the source for more information.
                                        </p>
                                        <div class="d-flex gap-2 pb-3">
                                            <button type="button" class="btn btn-default btn-sm flex-grow-1">
                                                Save
                                            </button>
                                            <button type="button" class="btn btn-default btn-sm flex-grow-1">
                                                Share
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Image Container -->
                            <div class="position-relative bg-light order-1 order-lg-2" style="width: auto; height: auto; max-height: 90vh;">
                                <button type="button" class="btn btn-icon btn-danger border border-dark position-absolute top-0 end-0 m-2 z-1" data-bs-dismiss="modal" aria-label="Close">
                                    <svg class="sa-icon sa-bold sa-icon-2x sa-icon-light">
                                        <use href="/icons/sprite.svg#x"></use>
                                    </svg>
                                </button>
                                <div class="d-flex align-items-center justify-content-center h-100 p-0">
                                    <button type="button" class="btn btn-icon align-items-center justify-content-center text-light btn-dark bg-dark bg-opacity-50 rounded-circle position-absolute top-50 start-0 translate-middle-y ms-4 d-none d-sm-flex fs-3 z-1" id="prevImage">
                                        <i class="sa sa-chevron-left"></i>
                                    </button>

                                    <img src="" class="img-preview" style="max-height: 90vh; max-width: 100%; object-fit: contain;" alt="Preview">

                                    <button type="button" class="btn btn-icon align-items-center justify-content-center text-light btn-dark bg-dark bg-opacity-50 rounded-circle position-absolute top-50 end-0 translate-middle-y me-4 d-none d-sm-flex fs-3 z-1" id="nextImage">
                                        <i class="sa sa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `,document.body.appendChild(e);const g=new bootstrap.Modal(e),s=m.querySelectorAll('a[href="#"]');let n=0;s.forEach((t,i)=>{t.addEventListener("click",o=>{o.preventDefault();const p=t.querySelector("img").src.replace(/(\.[^.]+)$/,"-big$1");b(p,t),n=i,c()})});const r=e.querySelector("#prevImage"),l=e.querySelector("#nextImage");r.addEventListener("click",t=>{if(t.stopPropagation(),n>0){n--;const i=s[n],d=i.querySelector("img").src.replace(/(\.[^.]+)$/,"-big$1");a(d,i),c()}}),l.addEventListener("click",t=>{if(t.stopPropagation(),n<s.length-1){n++;const i=s[n],d=i.querySelector("img").src.replace(/(\.[^.]+)$/,"-big$1");a(d,i),c()}}),document.addEventListener("keydown",t=>{if(e.classList.contains("show"))switch(t.key){case"Escape":u();break;case"ArrowLeft":n>0&&r.click();break;case"ArrowRight":n<s.length-1&&l.click();break}}),e.addEventListener("hidden.bs.modal",function(){const t=document.querySelector(".modal-backdrop");t&&t.remove()})}});
