document.addEventListener("DOMContentLoaded",function(){const h="order_cart";function g(){return JSON.parse(localStorage.getItem(h)||"[]")}function x(c){localStorage.setItem(h,JSON.stringify(c)),E()}function E(){const r=g().reduce((a,i)=>a+i.quantity,0),o=document.getElementById("cart-count");o&&(o.textContent=r,o.style.display=r>0?"block":"none")}document.getElementById("cart-items-container")&&$(),document.getElementById("checkout-items-container")&&_();function $(){const c=g(),r=document.getElementById("cart-items-container"),o=document.getElementById("checkout-btn");if(c.length===0){r.innerHTML=`
                <div class="text-center py-5">
                    <iconify-icon icon="solar:cart-large-3-bold-duotone" class="fs-64 text-muted"></iconify-icon>
                    <p class="text-muted mt-3">السلة فارغة</p>
                    <a href="{{ route('representative.orders.create') }}" class="btn btn-primary">إضافة منتجات</a>
                </div>
            `,o&&(o.style.display="none");return}let a="",i=0,m=0;if(c.forEach((n,e)=>{const t=n.customer_price||0,l=n.retail_price||0,d=Math.max(0,t-(n.wholesale_price||0)),v=t*n.quantity,p=d*n.quantity;i+=v,m+=p;const f=l>0,y=f&&t===0?`اقتراح: ${s(l)}`:"أدخل السعر";a+=`
                <div class="card mb-3 cart-item" data-index="${e}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${n.product_name}</h6>
                                <div class="d-flex gap-3 mb-2">
                                    <small class="text-muted">سعر الجملة: ${s(n.wholesale_price||0)}</small>
                                    <small class="text-muted">الكمية: ${n.quantity}</small>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger remove-item-btn" data-index="${e}">
                                <iconify-icon icon="solar:trash-bin-minimalistic-bold-duotone"></iconify-icon>
                            </button>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small">سعر البيع للزبون <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <input type="number" 
                                           step="0.01" 
                                           min="${n.wholesale_price||0}" 
                                           class="form-control form-control-sm customer-price-input" 
                                           data-index="${e}"
                                           data-wholesale-price="${n.wholesale_price||0}"
                                           data-retail-price="${l}"
                                           value="${t>0?t:""}"
                                           placeholder="${y}"
                                           required>
                                    ${f&&t===0?`
                                    <button type="button" 
                                            class="btn btn-outline-primary btn-sm use-suggested-price-btn" 
                                            data-index="${e}"
                                            data-suggested-price="${l}"
                                            title="استخدام السعر المقترح">
                                        <iconify-icon icon="solar:lightbulb-bold-duotone"></iconify-icon>
                                    </button>
                                    `:""}
                                </div>
                                <small class="text-muted d-block mt-1">
                                    ${f&&t===0?`<span class="text-info"><iconify-icon icon="solar:lightbulb-bold-duotone" class="align-middle"></iconify-icon> اقتراح السعر: ${s(l)} (سعر المفرد)</span>`:"يجب أن يكون أكبر من أو يساوي سعر الجملة"}
                                </small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">الربح للوحدة</label>
                                <input type="text" 
                                       class="form-control form-control-sm profit-per-item-display" 
                                       data-index="${e}"
                                       value="${s(d)}"
                                       readonly
                                       style="background-color: #f8f9fa;">
                            </div>
                        </div>
                        <div class="mt-2 d-flex justify-content-between align-items-center">
                            <small class="text-muted">الإجمالي: <strong>${s(v)}</strong></small>
                            <small class="text-success">الربح الإجمالي: <strong>${s(p)}</strong></small>
                        </div>
                    </div>
                </div>
            `}),r.innerHTML=a,S(i,m),o){const n=c.every(e=>e.customer_price>0);o.style.display=n?"block":"none",n||(o.disabled=!0)}document.querySelectorAll(".remove-item-btn").forEach(n=>{n.addEventListener("click",function(){const e=parseInt(this.dataset.index),t=g();t.splice(e,1),x(t),$()})}),document.querySelectorAll(".customer-price-input").forEach(n=>{n.addEventListener("input",function(){const e=parseInt(this.dataset.index),t=g(),l=parseFloat(this.dataset.wholesalePrice)||0,d=parseFloat(this.value)||0,v=t[e].quantity;if(d<l){this.classList.add("is-invalid");return}else this.classList.remove("is-invalid");const p=Math.max(0,d-l);t[e].customer_price=d,t[e].profit_per_item=p,t[e].subtotal=d*v,t[e].profit_subtotal=p*v,x(t);const f=document.querySelector(`.profit-per-item-display[data-index="${e}"]`);f&&(f.value=s(p));const y=this.closest(".cart-item"),I=y.querySelector(".text-muted strong"),w=y.querySelector(".text-success strong");I&&(I.textContent=s(t[e].subtotal)),w&&(w.textContent=s(t[e].profit_subtotal));const q=t.reduce((u,b)=>u+(b.subtotal||0),0),P=t.reduce((u,b)=>u+(b.profit_subtotal||0),0);S(q,P);const C=t.every(u=>u.customer_price>0);if(o&&(o.style.display=C?"block":"none",o.disabled=!C),d>0){const u=y.querySelector(".use-suggested-price-btn"),b=y.querySelector(".text-info");u&&(u.style.display="none"),b&&(b.style.display="none")}})}),document.querySelectorAll(".use-suggested-price-btn").forEach(n=>{n.addEventListener("click",function(){const e=parseInt(this.dataset.index),t=parseFloat(this.dataset.suggestedPrice)||0,l=document.querySelector(`.customer-price-input[data-index="${e}"]`);l&&t>0&&(l.value=t,l.dispatchEvent(new Event("input",{bubbles:!0})))})})}function S(c,r){const o=document.getElementById("cart-total"),a=document.getElementById("cart-profit"),i=document.getElementById("cart-net-profit");o&&(o.textContent=s(c)),a&&(a.textContent=s(r)),i&&(i.textContent=s(r))}function _(){const c=g();if(c.length===0){window.location.href='{{ route("representative.orders.cart") }}';return}if(!c.every(i=>i.customer_price>0)){alert("يرجى إدخال سعر البيع لجميع المنتجات في صفحة السلة أولاً"),window.location.href='{{ route("representative.orders.cart") }}';return}const o=c.reduce((i,m)=>i+(m.subtotal||0),0),a=c.reduce((i,m)=>i+(m.profit_subtotal||0),0);document.getElementById("summary-total").textContent=s(o),document.getElementById("summary-profit").textContent=s(a),document.getElementById("summary-net-profit").textContent=s(a)}function s(c){const r=parseFloat(c),a=(r%1===0?r.toString():r.toFixed(2).replace(/\.?0+$/,"")).split(".");return a[0]=a[0].replace(/\B(?=(\d{3})+(?!\d))/g,","),a.join(".")+" د.ع"}E()});
