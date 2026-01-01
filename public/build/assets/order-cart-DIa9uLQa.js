document.addEventListener("DOMContentLoaded",function(){const $="order_cart";function h(){return JSON.parse(localStorage.getItem($)||"[]")}function E(a){localStorage.setItem($,JSON.stringify(a)),S()}function S(){const r=h().reduce((i,c)=>i+c.quantity,0),o=document.getElementById("cart-count");o&&(o.textContent=r,o.style.display=r>0?"block":"none")}document.getElementById("cart-items-container")&&I(),document.getElementById("checkout-items-container")&&P();function I(){const a=h(),r=document.getElementById("cart-items-container"),o=document.getElementById("checkout-btn");if(a.length===0){r.innerHTML=`
                <div class="text-center py-5">
                    <iconify-icon icon="solar:cart-large-3-bold-duotone" class="fs-64 text-muted"></iconify-icon>
                    <p class="text-muted mt-3">السلة فارغة</p>
                    <a href="{{ route('representative.orders.create') }}" class="btn btn-primary">إضافة منتجات</a>
                </div>
            `,o&&(o.style.display="none");return}let i="",c=0,m=0;if(a.forEach((e,n)=>{const t=e.customer_price||0,l=e.retail_price||0,d=Math.max(0,t-(e.wholesale_price||0)),x=t*e.quantity,p=d*e.quantity;c+=x,m+=p;const f=l>0,y=f&&t===0?`اقتراح: ${s(l)}`:"أدخل السعر";let _="";if(e.product_variant_id&&e.variant_combination){const b=[];Array.isArray(e.variant_combination)&&e.variant_combination.forEach(g=>{g.option_name&&g.value_name&&b.push(`${g.option_name}: ${g.value_name}`)}),b.length>0&&(_=`<div class="mb-1"><small class="text-info">${b.join(", ")}</small></div>`)}i+=`
                <div class="card mb-3 cart-item" data-index="${n}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${e.product_name}</h6>
                                ${_}
                                <div class="d-flex gap-3 mb-2">
                                    <small class="text-muted">سعر الجملة: ${s(e.wholesale_price||0)}</small>
                                    <small class="text-muted">الكمية: ${e.quantity}</small>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger remove-item-btn" data-index="${n}">
                                <iconify-icon icon="solar:trash-bin-minimalistic-bold-duotone"></iconify-icon>
                            </button>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small">سعر البيع للزبون <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <input type="number" 
                                           step="0.01" 
                                           min="${e.wholesale_price||0}" 
                                           class="form-control form-control-sm customer-price-input" 
                                           data-index="${n}"
                                           data-wholesale-price="${e.wholesale_price||0}"
                                           data-retail-price="${l}"
                                           value="${t>0?t:""}"
                                           placeholder="${y}"
                                           required>
                                    ${f&&t===0?`
                                    <button type="button" 
                                            class="btn btn-outline-primary btn-sm use-suggested-price-btn" 
                                            data-index="${n}"
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
                                       data-index="${n}"
                                       value="${s(d)}"
                                       readonly
                                       style="background-color: #f8f9fa;">
                            </div>
                        </div>
                        <div class="mt-2 d-flex justify-content-between align-items-center">
                            <small class="text-muted">الإجمالي: <strong>${s(x)}</strong></small>
                            <small class="text-success">الربح الإجمالي: <strong>${s(p)}</strong></small>
                        </div>
                    </div>
                </div>
            `}),r.innerHTML=i,w(c,m),o){const e=a.every(n=>n.customer_price>0);o.style.display=e?"block":"none",e||(o.disabled=!0)}document.querySelectorAll(".remove-item-btn").forEach(e=>{e.addEventListener("click",function(){const n=parseInt(this.dataset.index),t=h();t.splice(n,1),E(t),I()})}),document.querySelectorAll(".customer-price-input").forEach(e=>{e.addEventListener("input",function(){const n=parseInt(this.dataset.index),t=h(),l=parseFloat(this.dataset.wholesalePrice)||0,d=parseFloat(this.value)||0,x=t[n].quantity;if(d<l){this.classList.add("is-invalid");return}else this.classList.remove("is-invalid");const p=Math.max(0,d-l);t[n].customer_price=d,t[n].profit_per_item=p,t[n].subtotal=d*x,t[n].profit_subtotal=p*x,E(t);const f=document.querySelector(`.profit-per-item-display[data-index="${n}"]`);f&&(f.value=s(p));const y=this.closest(".cart-item"),_=y.querySelector(".text-muted strong"),b=y.querySelector(".text-success strong");_&&(_.textContent=s(t[n].subtotal)),b&&(b.textContent=s(t[n].profit_subtotal));const g=t.reduce((u,v)=>u+(v.subtotal||0),0),q=t.reduce((u,v)=>u+(v.profit_subtotal||0),0);w(g,q);const C=t.every(u=>u.customer_price>0);if(o&&(o.style.display=C?"block":"none",o.disabled=!C),d>0){const u=y.querySelector(".use-suggested-price-btn"),v=y.querySelector(".text-info");u&&(u.style.display="none"),v&&(v.style.display="none")}})}),document.querySelectorAll(".use-suggested-price-btn").forEach(e=>{e.addEventListener("click",function(){const n=parseInt(this.dataset.index),t=parseFloat(this.dataset.suggestedPrice)||0,l=document.querySelector(`.customer-price-input[data-index="${n}"]`);l&&t>0&&(l.value=t,l.dispatchEvent(new Event("input",{bubbles:!0})))})})}function w(a,r){const o=document.getElementById("cart-total"),i=document.getElementById("cart-profit"),c=document.getElementById("cart-net-profit");o&&(o.textContent=s(a)),i&&(i.textContent=s(r)),c&&(c.textContent=s(r))}function P(){const a=h();if(a.length===0){window.location.href='{{ route("representative.orders.cart") }}';return}if(!a.every(c=>c.customer_price>0)){alert("يرجى إدخال سعر البيع لجميع المنتجات في صفحة السلة أولاً"),window.location.href='{{ route("representative.orders.cart") }}';return}const o=a.reduce((c,m)=>c+(m.subtotal||0),0),i=a.reduce((c,m)=>c+(m.profit_subtotal||0),0);document.getElementById("summary-total").textContent=s(o),document.getElementById("summary-profit").textContent=s(i),document.getElementById("summary-net-profit").textContent=s(i)}function s(a){const r=parseFloat(a),i=(r%1===0?r.toString():r.toFixed(2).replace(/\.?0+$/,"")).split(".");return i[0]=i[0].replace(/\B(?=(\d{3})+(?!\d))/g,","),i.join(".")+" د.ع"}S()});
