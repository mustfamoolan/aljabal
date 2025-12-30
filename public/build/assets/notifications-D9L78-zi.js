const Pt=()=>{};var ye={};/**
 * @license
 * Copyright 2017 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const We=function(e){const t=[];let n=0;for(let i=0;i<e.length;i++){let r=e.charCodeAt(i);r<128?t[n++]=r:r<2048?(t[n++]=r>>6|192,t[n++]=r&63|128):(r&64512)===55296&&i+1<e.length&&(e.charCodeAt(i+1)&64512)===56320?(r=65536+((r&1023)<<10)+(e.charCodeAt(++i)&1023),t[n++]=r>>18|240,t[n++]=r>>12&63|128,t[n++]=r>>6&63|128,t[n++]=r&63|128):(t[n++]=r>>12|224,t[n++]=r>>6&63|128,t[n++]=r&63|128)}return t},$t=function(e){const t=[];let n=0,i=0;for(;n<e.length;){const r=e[n++];if(r<128)t[i++]=String.fromCharCode(r);else if(r>191&&r<224){const o=e[n++];t[i++]=String.fromCharCode((r&31)<<6|o&63)}else if(r>239&&r<365){const o=e[n++],s=e[n++],a=e[n++],l=((r&7)<<18|(o&63)<<12|(s&63)<<6|a&63)-65536;t[i++]=String.fromCharCode(55296+(l>>10)),t[i++]=String.fromCharCode(56320+(l&1023))}else{const o=e[n++],s=e[n++];t[i++]=String.fromCharCode((r&15)<<12|(o&63)<<6|s&63)}}return t.join("")},Ge={byteToCharMap_:null,charToByteMap_:null,byteToCharMapWebSafe_:null,charToByteMapWebSafe_:null,ENCODED_VALS_BASE:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789",get ENCODED_VALS(){return this.ENCODED_VALS_BASE+"+/="},get ENCODED_VALS_WEBSAFE(){return this.ENCODED_VALS_BASE+"-_."},HAS_NATIVE_SUPPORT:typeof atob=="function",encodeByteArray(e,t){if(!Array.isArray(e))throw Error("encodeByteArray takes an array as a parameter");this.init_();const n=t?this.byteToCharMapWebSafe_:this.byteToCharMap_,i=[];for(let r=0;r<e.length;r+=3){const o=e[r],s=r+1<e.length,a=s?e[r+1]:0,l=r+2<e.length,c=l?e[r+2]:0,h=o>>2,m=(o&3)<<4|a>>4;let p=(a&15)<<2|c>>6,$=c&63;l||($=64,s||(p=64)),i.push(n[h],n[m],n[p],n[$])}return i.join("")},encodeString(e,t){return this.HAS_NATIVE_SUPPORT&&!t?btoa(e):this.encodeByteArray(We(e),t)},decodeString(e,t){return this.HAS_NATIVE_SUPPORT&&!t?atob(e):$t(this.decodeStringToByteArray(e,t))},decodeStringToByteArray(e,t){this.init_();const n=t?this.charToByteMapWebSafe_:this.charToByteMap_,i=[];for(let r=0;r<e.length;){const o=n[e.charAt(r++)],a=r<e.length?n[e.charAt(r)]:0;++r;const c=r<e.length?n[e.charAt(r)]:64;++r;const m=r<e.length?n[e.charAt(r)]:64;if(++r,o==null||a==null||c==null||m==null)throw new Bt;const p=o<<2|a>>4;if(i.push(p),c!==64){const $=a<<4&240|c>>2;if(i.push($),m!==64){const Nt=c<<6&192|m;i.push(Nt)}}}return i},init_(){if(!this.byteToCharMap_){this.byteToCharMap_={},this.charToByteMap_={},this.byteToCharMapWebSafe_={},this.charToByteMapWebSafe_={};for(let e=0;e<this.ENCODED_VALS.length;e++)this.byteToCharMap_[e]=this.ENCODED_VALS.charAt(e),this.charToByteMap_[this.byteToCharMap_[e]]=e,this.byteToCharMapWebSafe_[e]=this.ENCODED_VALS_WEBSAFE.charAt(e),this.charToByteMapWebSafe_[this.byteToCharMapWebSafe_[e]]=e,e>=this.ENCODED_VALS_BASE.length&&(this.charToByteMap_[this.ENCODED_VALS_WEBSAFE.charAt(e)]=e,this.charToByteMapWebSafe_[this.ENCODED_VALS.charAt(e)]=e)}}};class Bt extends Error{constructor(){super(...arguments),this.name="DecodeBase64StringError"}}const Lt=function(e){const t=We(e);return Ge.encodeByteArray(t,!0)},Ye=function(e){return Lt(e).replace(/\./g,"")},xt=function(e){try{return Ge.decodeString(e,!0)}catch(t){console.error("base64Decode failed: ",t)}return null};/**
 * @license
 * Copyright 2022 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function jt(){if(typeof self<"u")return self;if(typeof window<"u")return window;if(typeof global<"u")return global;throw new Error("Unable to locate global object.")}/**
 * @license
 * Copyright 2022 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const Ht=()=>jt().__FIREBASE_DEFAULTS__,Vt=()=>{if(typeof process>"u"||typeof ye>"u")return;const e=ye.__FIREBASE_DEFAULTS__;if(e)return JSON.parse(e)},Ut=()=>{if(typeof document>"u")return;let e;try{e=document.cookie.match(/__FIREBASE_DEFAULTS__=([^;]+)/)}catch{return}const t=e&&xt(e[1]);return t&&JSON.parse(t)},qt=()=>{try{return Pt()||Ht()||Vt()||Ut()}catch(e){console.info(`Unable to get __FIREBASE_DEFAULTS__ due to: ${e}`);return}},Je=()=>qt()?.config;/**
 * @license
 * Copyright 2017 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */class Kt{constructor(){this.reject=()=>{},this.resolve=()=>{},this.promise=new Promise((t,n)=>{this.resolve=t,this.reject=n})}wrapCallback(t){return(n,i)=>{n?this.reject(n):this.resolve(i),typeof t=="function"&&(this.promise.catch(()=>{}),t.length===1?t(n):t(n,i))}}}function zt(){const e=typeof chrome=="object"?chrome.runtime:typeof browser=="object"?browser.runtime:void 0;return typeof e=="object"&&e.id!==void 0}function ae(){try{return typeof indexedDB=="object"}catch{return!1}}function ce(){return new Promise((e,t)=>{try{let n=!0;const i="validate-browser-context-for-indexeddb-analytics-module",r=self.indexedDB.open(i);r.onsuccess=()=>{r.result.close(),n||self.indexedDB.deleteDatabase(i),e(!0)},r.onupgradeneeded=()=>{n=!1},r.onerror=()=>{t(r.error?.message||"")}}catch(n){t(n)}})}function Xe(){return!(typeof navigator>"u"||!navigator.cookieEnabled)}/**
 * @license
 * Copyright 2017 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const Wt="FirebaseError";class k extends Error{constructor(t,n,i){super(n),this.code=t,this.customData=i,this.name=Wt,Object.setPrototypeOf(this,k.prototype),Error.captureStackTrace&&Error.captureStackTrace(this,N.prototype.create)}}class N{constructor(t,n,i){this.service=t,this.serviceName=n,this.errors=i}create(t,...n){const i=n[0]||{},r=`${this.service}/${t}`,o=this.errors[t],s=o?Gt(o,i):"Error",a=`${this.serviceName}: ${s} (${r}).`;return new k(r,a,i)}}function Gt(e,t){return e.replace(Yt,(n,i)=>{const r=t[i];return r!=null?String(r):`<${i}?>`})}const Yt=/\{\$([^}]+)}/g;function B(e,t){if(e===t)return!0;const n=Object.keys(e),i=Object.keys(t);for(const r of n){if(!i.includes(r))return!1;const o=e[r],s=t[r];if(Ie(o)&&Ie(s)){if(!B(o,s))return!1}else if(o!==s)return!1}for(const r of i)if(!n.includes(r))return!1;return!0}function Ie(e){return e!==null&&typeof e=="object"}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const Jt=1e3,Xt=2,Zt=14400*1e3,Qt=.5;function Te(e,t=Jt,n=Xt){const i=t*Math.pow(n,e),r=Math.round(Qt*i*(Math.random()-.5)*2);return Math.min(Zt,i+r)}/**
 * @license
 * Copyright 2021 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function M(e){return e&&e._delegate?e._delegate:e}class w{constructor(t,n,i){this.name=t,this.instanceFactory=n,this.type=i,this.multipleInstances=!1,this.serviceProps={},this.instantiationMode="LAZY",this.onInstanceCreated=null}setInstantiationMode(t){return this.instantiationMode=t,this}setMultipleInstances(t){return this.multipleInstances=t,this}setServiceProps(t){return this.serviceProps=t,this}setInstanceCreatedCallback(t){return this.onInstanceCreated=t,this}}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const A="[DEFAULT]";/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */class en{constructor(t,n){this.name=t,this.container=n,this.component=null,this.instances=new Map,this.instancesDeferred=new Map,this.instancesOptions=new Map,this.onInitCallbacks=new Map}get(t){const n=this.normalizeInstanceIdentifier(t);if(!this.instancesDeferred.has(n)){const i=new Kt;if(this.instancesDeferred.set(n,i),this.isInitialized(n)||this.shouldAutoInitialize())try{const r=this.getOrInitializeService({instanceIdentifier:n});r&&i.resolve(r)}catch{}}return this.instancesDeferred.get(n).promise}getImmediate(t){const n=this.normalizeInstanceIdentifier(t?.identifier),i=t?.optional??!1;if(this.isInitialized(n)||this.shouldAutoInitialize())try{return this.getOrInitializeService({instanceIdentifier:n})}catch(r){if(i)return null;throw r}else{if(i)return null;throw Error(`Service ${this.name} is not available`)}}getComponent(){return this.component}setComponent(t){if(t.name!==this.name)throw Error(`Mismatching Component ${t.name} for Provider ${this.name}.`);if(this.component)throw Error(`Component for ${this.name} has already been provided`);if(this.component=t,!!this.shouldAutoInitialize()){if(nn(t))try{this.getOrInitializeService({instanceIdentifier:A})}catch{}for(const[n,i]of this.instancesDeferred.entries()){const r=this.normalizeInstanceIdentifier(n);try{const o=this.getOrInitializeService({instanceIdentifier:r});i.resolve(o)}catch{}}}}clearInstance(t=A){this.instancesDeferred.delete(t),this.instancesOptions.delete(t),this.instances.delete(t)}async delete(){const t=Array.from(this.instances.values());await Promise.all([...t.filter(n=>"INTERNAL"in n).map(n=>n.INTERNAL.delete()),...t.filter(n=>"_delete"in n).map(n=>n._delete())])}isComponentSet(){return this.component!=null}isInitialized(t=A){return this.instances.has(t)}getOptions(t=A){return this.instancesOptions.get(t)||{}}initialize(t={}){const{options:n={}}=t,i=this.normalizeInstanceIdentifier(t.instanceIdentifier);if(this.isInitialized(i))throw Error(`${this.name}(${i}) has already been initialized`);if(!this.isComponentSet())throw Error(`Component ${this.name} has not been registered yet`);const r=this.getOrInitializeService({instanceIdentifier:i,options:n});for(const[o,s]of this.instancesDeferred.entries()){const a=this.normalizeInstanceIdentifier(o);i===a&&s.resolve(r)}return r}onInit(t,n){const i=this.normalizeInstanceIdentifier(n),r=this.onInitCallbacks.get(i)??new Set;r.add(t),this.onInitCallbacks.set(i,r);const o=this.instances.get(i);return o&&t(o,i),()=>{r.delete(t)}}invokeOnInitCallbacks(t,n){const i=this.onInitCallbacks.get(n);if(i)for(const r of i)try{r(t,n)}catch{}}getOrInitializeService({instanceIdentifier:t,options:n={}}){let i=this.instances.get(t);if(!i&&this.component&&(i=this.component.instanceFactory(this.container,{instanceIdentifier:tn(t),options:n}),this.instances.set(t,i),this.instancesOptions.set(t,n),this.invokeOnInitCallbacks(i,t),this.component.onInstanceCreated))try{this.component.onInstanceCreated(this.container,t,i)}catch{}return i||null}normalizeInstanceIdentifier(t=A){return this.component?this.component.multipleInstances?t:A:t}shouldAutoInitialize(){return!!this.component&&this.component.instantiationMode!=="EXPLICIT"}}function tn(e){return e===A?void 0:e}function nn(e){return e.instantiationMode==="EAGER"}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */class rn{constructor(t){this.name=t,this.providers=new Map}addComponent(t){const n=this.getProvider(t.name);if(n.isComponentSet())throw new Error(`Component ${t.name} has already been registered with ${this.name}`);n.setComponent(t)}addOrOverwriteComponent(t){this.getProvider(t.name).isComponentSet()&&this.providers.delete(t.name),this.addComponent(t)}getProvider(t){if(this.providers.has(t))return this.providers.get(t);const n=new en(t,this);return this.providers.set(t,n),n}getProviders(){return Array.from(this.providers.values())}}/**
 * @license
 * Copyright 2017 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */var u;(function(e){e[e.DEBUG=0]="DEBUG",e[e.VERBOSE=1]="VERBOSE",e[e.INFO=2]="INFO",e[e.WARN=3]="WARN",e[e.ERROR=4]="ERROR",e[e.SILENT=5]="SILENT"})(u||(u={}));const on={debug:u.DEBUG,verbose:u.VERBOSE,info:u.INFO,warn:u.WARN,error:u.ERROR,silent:u.SILENT},sn=u.INFO,an={[u.DEBUG]:"log",[u.VERBOSE]:"log",[u.INFO]:"info",[u.WARN]:"warn",[u.ERROR]:"error"},cn=(e,t,...n)=>{if(t<e.logLevel)return;const i=new Date().toISOString(),r=an[t];if(r)console[r](`[${i}]  ${e.name}:`,...n);else throw new Error(`Attempted to log a message with an invalid logType (value: ${t})`)};class Ze{constructor(t){this.name=t,this._logLevel=sn,this._logHandler=cn,this._userLogHandler=null}get logLevel(){return this._logLevel}set logLevel(t){if(!(t in u))throw new TypeError(`Invalid value "${t}" assigned to \`logLevel\``);this._logLevel=t}setLogLevel(t){this._logLevel=typeof t=="string"?on[t]:t}get logHandler(){return this._logHandler}set logHandler(t){if(typeof t!="function")throw new TypeError("Value assigned to `logHandler` must be a function");this._logHandler=t}get userLogHandler(){return this._userLogHandler}set userLogHandler(t){this._userLogHandler=t}debug(...t){this._userLogHandler&&this._userLogHandler(this,u.DEBUG,...t),this._logHandler(this,u.DEBUG,...t)}log(...t){this._userLogHandler&&this._userLogHandler(this,u.VERBOSE,...t),this._logHandler(this,u.VERBOSE,...t)}info(...t){this._userLogHandler&&this._userLogHandler(this,u.INFO,...t),this._logHandler(this,u.INFO,...t)}warn(...t){this._userLogHandler&&this._userLogHandler(this,u.WARN,...t),this._logHandler(this,u.WARN,...t)}error(...t){this._userLogHandler&&this._userLogHandler(this,u.ERROR,...t),this._logHandler(this,u.ERROR,...t)}}const ln=(e,t)=>t.some(n=>e instanceof n);let Ee,ve;function un(){return Ee||(Ee=[IDBDatabase,IDBObjectStore,IDBIndex,IDBCursor,IDBTransaction])}function dn(){return ve||(ve=[IDBCursor.prototype.advance,IDBCursor.prototype.continue,IDBCursor.prototype.continuePrimaryKey])}const Qe=new WeakMap,te=new WeakMap,et=new WeakMap,q=new WeakMap,le=new WeakMap;function fn(e){const t=new Promise((n,i)=>{const r=()=>{e.removeEventListener("success",o),e.removeEventListener("error",s)},o=()=>{n(I(e.result)),r()},s=()=>{i(e.error),r()};e.addEventListener("success",o),e.addEventListener("error",s)});return t.then(n=>{n instanceof IDBCursor&&Qe.set(n,e)}).catch(()=>{}),le.set(t,e),t}function hn(e){if(te.has(e))return;const t=new Promise((n,i)=>{const r=()=>{e.removeEventListener("complete",o),e.removeEventListener("error",s),e.removeEventListener("abort",s)},o=()=>{n(),r()},s=()=>{i(e.error||new DOMException("AbortError","AbortError")),r()};e.addEventListener("complete",o),e.addEventListener("error",s),e.addEventListener("abort",s)});te.set(e,t)}let ne={get(e,t,n){if(e instanceof IDBTransaction){if(t==="done")return te.get(e);if(t==="objectStoreNames")return e.objectStoreNames||et.get(e);if(t==="store")return n.objectStoreNames[1]?void 0:n.objectStore(n.objectStoreNames[0])}return I(e[t])},set(e,t,n){return e[t]=n,!0},has(e,t){return e instanceof IDBTransaction&&(t==="done"||t==="store")?!0:t in e}};function pn(e){ne=e(ne)}function gn(e){return e===IDBDatabase.prototype.transaction&&!("objectStoreNames"in IDBTransaction.prototype)?function(t,...n){const i=e.call(K(this),t,...n);return et.set(i,t.sort?t.sort():[t]),I(i)}:dn().includes(e)?function(...t){return e.apply(K(this),t),I(Qe.get(this))}:function(...t){return I(e.apply(K(this),t))}}function mn(e){return typeof e=="function"?gn(e):(e instanceof IDBTransaction&&hn(e),ln(e,un())?new Proxy(e,ne):e)}function I(e){if(e instanceof IDBRequest)return fn(e);if(q.has(e))return q.get(e);const t=mn(e);return t!==e&&(q.set(e,t),le.set(t,e)),t}const K=e=>le.get(e);function H(e,t,{blocked:n,upgrade:i,blocking:r,terminated:o}={}){const s=indexedDB.open(e,t),a=I(s);return i&&s.addEventListener("upgradeneeded",l=>{i(I(s.result),l.oldVersion,l.newVersion,I(s.transaction),l)}),n&&s.addEventListener("blocked",l=>n(l.oldVersion,l.newVersion,l)),a.then(l=>{o&&l.addEventListener("close",()=>o()),r&&l.addEventListener("versionchange",c=>r(c.oldVersion,c.newVersion,c))}).catch(()=>{}),a}function z(e,{blocked:t}={}){const n=indexedDB.deleteDatabase(e);return t&&n.addEventListener("blocked",i=>t(i.oldVersion,i)),I(n).then(()=>{})}const bn=["get","getKey","getAll","getAllKeys","count"],wn=["put","add","delete","clear"],W=new Map;function Ae(e,t){if(!(e instanceof IDBDatabase&&!(t in e)&&typeof t=="string"))return;if(W.get(t))return W.get(t);const n=t.replace(/FromIndex$/,""),i=t!==n,r=wn.includes(n);if(!(n in(i?IDBIndex:IDBObjectStore).prototype)||!(r||bn.includes(n)))return;const o=async function(s,...a){const l=this.transaction(s,r?"readwrite":"readonly");let c=l.store;return i&&(c=c.index(a.shift())),(await Promise.all([c[n](...a),r&&l.done]))[0]};return W.set(t,o),o}pn(e=>({...e,get:(t,n,i)=>Ae(t,n)||e.get(t,n,i),has:(t,n)=>!!Ae(t,n)||e.has(t,n)}));/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */class yn{constructor(t){this.container=t}getPlatformInfoString(){return this.container.getProviders().map(n=>{if(In(n)){const i=n.getImmediate();return`${i.library}/${i.version}`}else return null}).filter(n=>n).join(" ")}}function In(e){return e.getComponent()?.type==="VERSION"}const ie="@firebase/app",Se="0.14.6";/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const T=new Ze("@firebase/app"),Tn="@firebase/app-compat",En="@firebase/analytics-compat",vn="@firebase/analytics",An="@firebase/app-check-compat",Sn="@firebase/app-check",_n="@firebase/auth",Cn="@firebase/auth-compat",kn="@firebase/database",Dn="@firebase/data-connect",Mn="@firebase/database-compat",Fn="@firebase/functions",On="@firebase/functions-compat",Rn="@firebase/installations",Nn="@firebase/installations-compat",Pn="@firebase/messaging",$n="@firebase/messaging-compat",Bn="@firebase/performance",Ln="@firebase/performance-compat",xn="@firebase/remote-config",jn="@firebase/remote-config-compat",Hn="@firebase/storage",Vn="@firebase/storage-compat",Un="@firebase/firestore",qn="@firebase/ai",Kn="@firebase/firestore-compat",zn="firebase";/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const re="[DEFAULT]",Wn={[ie]:"fire-core",[Tn]:"fire-core-compat",[vn]:"fire-analytics",[En]:"fire-analytics-compat",[Sn]:"fire-app-check",[An]:"fire-app-check-compat",[_n]:"fire-auth",[Cn]:"fire-auth-compat",[kn]:"fire-rtdb",[Dn]:"fire-data-connect",[Mn]:"fire-rtdb-compat",[Fn]:"fire-fn",[On]:"fire-fn-compat",[Rn]:"fire-iid",[Nn]:"fire-iid-compat",[Pn]:"fire-fcm",[$n]:"fire-fcm-compat",[Bn]:"fire-perf",[Ln]:"fire-perf-compat",[xn]:"fire-rc",[jn]:"fire-rc-compat",[Hn]:"fire-gcs",[Vn]:"fire-gcs-compat",[Un]:"fire-fst",[Kn]:"fire-fst-compat",[qn]:"fire-vertex","fire-js":"fire-js",[zn]:"fire-js-all"};/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const L=new Map,Gn=new Map,oe=new Map;function _e(e,t){try{e.container.addComponent(t)}catch(n){T.debug(`Component ${t.name} failed to register with FirebaseApp ${e.name}`,n)}}function E(e){const t=e.name;if(oe.has(t))return T.debug(`There were multiple attempts to register component ${t}.`),!1;oe.set(t,e);for(const n of L.values())_e(n,e);for(const n of Gn.values())_e(n,e);return!0}function P(e,t){const n=e.container.getProvider("heartbeat").getImmediate({optional:!0});return n&&n.triggerHeartbeat(),e.container.getProvider(t)}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const Yn={"no-app":"No Firebase App '{$appName}' has been created - call initializeApp() first","bad-app-name":"Illegal App name: '{$appName}'","duplicate-app":"Firebase App named '{$appName}' already exists with different options or config","app-deleted":"Firebase App named '{$appName}' already deleted","server-app-deleted":"Firebase Server App has been deleted","no-options":"Need to provide options, when not being deployed to hosting via source.","invalid-app-argument":"firebase.{$appName}() takes either no argument or a Firebase App instance.","invalid-log-argument":"First argument to `onLog` must be null or a function.","idb-open":"Error thrown when opening IndexedDB. Original error: {$originalErrorMessage}.","idb-get":"Error thrown when reading from IndexedDB. Original error: {$originalErrorMessage}.","idb-set":"Error thrown when writing to IndexedDB. Original error: {$originalErrorMessage}.","idb-delete":"Error thrown when deleting from IndexedDB. Original error: {$originalErrorMessage}.","finalization-registry-not-supported":"FirebaseServerApp deleteOnDeref field defined but the JS runtime does not support FinalizationRegistry.","invalid-server-app-environment":"FirebaseServerApp is not for use in browser environments."},v=new N("app","Firebase",Yn);/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */class Jn{constructor(t,n,i){this._isDeleted=!1,this._options={...t},this._config={...n},this._name=n.name,this._automaticDataCollectionEnabled=n.automaticDataCollectionEnabled,this._container=i,this.container.addComponent(new w("app",()=>this,"PUBLIC"))}get automaticDataCollectionEnabled(){return this.checkDestroyed(),this._automaticDataCollectionEnabled}set automaticDataCollectionEnabled(t){this.checkDestroyed(),this._automaticDataCollectionEnabled=t}get name(){return this.checkDestroyed(),this._name}get options(){return this.checkDestroyed(),this._options}get config(){return this.checkDestroyed(),this._config}get container(){return this._container}get isDeleted(){return this._isDeleted}set isDeleted(t){this._isDeleted=t}checkDestroyed(){if(this.isDeleted)throw v.create("app-deleted",{appName:this._name})}}function tt(e,t={}){let n=e;typeof t!="object"&&(t={name:t});const i={name:re,automaticDataCollectionEnabled:!0,...t},r=i.name;if(typeof r!="string"||!r)throw v.create("bad-app-name",{appName:String(r)});if(n||(n=Je()),!n)throw v.create("no-options");const o=L.get(r);if(o){if(B(n,o.options)&&B(i,o.config))return o;throw v.create("duplicate-app",{appName:r})}const s=new rn(r);for(const l of oe.values())s.addComponent(l);const a=new Jn(n,i,s);return L.set(r,a),a}function nt(e=re){const t=L.get(e);if(!t&&e===re&&Je())return tt();if(!t)throw v.create("no-app",{appName:e});return t}function b(e,t,n){let i=Wn[e]??e;n&&(i+=`-${n}`);const r=i.match(/\s|\//),o=t.match(/\s|\//);if(r||o){const s=[`Unable to register library "${i}" with version "${t}":`];r&&s.push(`library name "${i}" contains illegal characters (whitespace or "/")`),r&&o&&s.push("and"),o&&s.push(`version name "${t}" contains illegal characters (whitespace or "/")`),T.warn(s.join(" "));return}E(new w(`${i}-version`,()=>({library:i,version:t}),"VERSION"))}/**
 * @license
 * Copyright 2021 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const Xn="firebase-heartbeat-database",Zn=1,F="firebase-heartbeat-store";let G=null;function it(){return G||(G=H(Xn,Zn,{upgrade:(e,t)=>{switch(t){case 0:try{e.createObjectStore(F)}catch(n){console.warn(n)}}}}).catch(e=>{throw v.create("idb-open",{originalErrorMessage:e.message})})),G}async function Qn(e){try{const n=(await it()).transaction(F),i=await n.objectStore(F).get(rt(e));return await n.done,i}catch(t){if(t instanceof k)T.warn(t.message);else{const n=v.create("idb-get",{originalErrorMessage:t?.message});T.warn(n.message)}}}async function Ce(e,t){try{const i=(await it()).transaction(F,"readwrite");await i.objectStore(F).put(t,rt(e)),await i.done}catch(n){if(n instanceof k)T.warn(n.message);else{const i=v.create("idb-set",{originalErrorMessage:n?.message});T.warn(i.message)}}}function rt(e){return`${e.name}!${e.options.appId}`}/**
 * @license
 * Copyright 2021 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const ei=1024,ti=30;class ni{constructor(t){this.container=t,this._heartbeatsCache=null;const n=this.container.getProvider("app").getImmediate();this._storage=new ri(n),this._heartbeatsCachePromise=this._storage.read().then(i=>(this._heartbeatsCache=i,i))}async triggerHeartbeat(){try{const n=this.container.getProvider("platform-logger").getImmediate().getPlatformInfoString(),i=ke();if(this._heartbeatsCache?.heartbeats==null&&(this._heartbeatsCache=await this._heartbeatsCachePromise,this._heartbeatsCache?.heartbeats==null)||this._heartbeatsCache.lastSentHeartbeatDate===i||this._heartbeatsCache.heartbeats.some(r=>r.date===i))return;if(this._heartbeatsCache.heartbeats.push({date:i,agent:n}),this._heartbeatsCache.heartbeats.length>ti){const r=oi(this._heartbeatsCache.heartbeats);this._heartbeatsCache.heartbeats.splice(r,1)}return this._storage.overwrite(this._heartbeatsCache)}catch(t){T.warn(t)}}async getHeartbeatsHeader(){try{if(this._heartbeatsCache===null&&await this._heartbeatsCachePromise,this._heartbeatsCache?.heartbeats==null||this._heartbeatsCache.heartbeats.length===0)return"";const t=ke(),{heartbeatsToSend:n,unsentEntries:i}=ii(this._heartbeatsCache.heartbeats),r=Ye(JSON.stringify({version:2,heartbeats:n}));return this._heartbeatsCache.lastSentHeartbeatDate=t,i.length>0?(this._heartbeatsCache.heartbeats=i,await this._storage.overwrite(this._heartbeatsCache)):(this._heartbeatsCache.heartbeats=[],this._storage.overwrite(this._heartbeatsCache)),r}catch(t){return T.warn(t),""}}}function ke(){return new Date().toISOString().substring(0,10)}function ii(e,t=ei){const n=[];let i=e.slice();for(const r of e){const o=n.find(s=>s.agent===r.agent);if(o){if(o.dates.push(r.date),De(n)>t){o.dates.pop();break}}else if(n.push({agent:r.agent,dates:[r.date]}),De(n)>t){n.pop();break}i=i.slice(1)}return{heartbeatsToSend:n,unsentEntries:i}}class ri{constructor(t){this.app=t,this._canUseIndexedDBPromise=this.runIndexedDBEnvironmentCheck()}async runIndexedDBEnvironmentCheck(){return ae()?ce().then(()=>!0).catch(()=>!1):!1}async read(){if(await this._canUseIndexedDBPromise){const n=await Qn(this.app);return n?.heartbeats?n:{heartbeats:[]}}else return{heartbeats:[]}}async overwrite(t){if(await this._canUseIndexedDBPromise){const i=await this.read();return Ce(this.app,{lastSentHeartbeatDate:t.lastSentHeartbeatDate??i.lastSentHeartbeatDate,heartbeats:t.heartbeats})}else return}async add(t){if(await this._canUseIndexedDBPromise){const i=await this.read();return Ce(this.app,{lastSentHeartbeatDate:t.lastSentHeartbeatDate??i.lastSentHeartbeatDate,heartbeats:[...i.heartbeats,...t.heartbeats]})}else return}}function De(e){return Ye(JSON.stringify({version:2,heartbeats:e})).length}function oi(e){if(e.length===0)return-1;let t=0,n=e[0].date;for(let i=1;i<e.length;i++)e[i].date<n&&(n=e[i].date,t=i);return t}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function si(e){E(new w("platform-logger",t=>new yn(t),"PRIVATE")),E(new w("heartbeat",t=>new ni(t),"PRIVATE")),b(ie,Se,e),b(ie,Se,"esm2020"),b("fire-js","")}si("");var ai="firebase",ci="12.7.0";/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */b(ai,ci,"app");const ot="@firebase/installations",ue="0.6.19";/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const st=1e4,at=`w:${ue}`,ct="FIS_v2",li="https://firebaseinstallations.googleapis.com/v1",ui=3600*1e3,di="installations",fi="Installations";/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const hi={"missing-app-config-values":'Missing App configuration value: "{$valueName}"',"not-registered":"Firebase Installation is not registered.","installation-not-found":"Firebase Installation not found.","request-failed":'{$requestName} request failed with error "{$serverCode} {$serverStatus}: {$serverMessage}"',"app-offline":"Could not process request. Application offline.","delete-pending-registration":"Can't delete installation while there is a pending registration request."},_=new N(di,fi,hi);function lt(e){return e instanceof k&&e.code.includes("request-failed")}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function ut({projectId:e}){return`${li}/projects/${e}/installations`}function dt(e){return{token:e.token,requestStatus:2,expiresIn:gi(e.expiresIn),creationTime:Date.now()}}async function ft(e,t){const i=(await t.json()).error;return _.create("request-failed",{requestName:e,serverCode:i.code,serverMessage:i.message,serverStatus:i.status})}function ht({apiKey:e}){return new Headers({"Content-Type":"application/json",Accept:"application/json","x-goog-api-key":e})}function pi(e,{refreshToken:t}){const n=ht(e);return n.append("Authorization",mi(t)),n}async function pt(e){const t=await e();return t.status>=500&&t.status<600?e():t}function gi(e){return Number(e.replace("s","000"))}function mi(e){return`${ct} ${e}`}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function bi({appConfig:e,heartbeatServiceProvider:t},{fid:n}){const i=ut(e),r=ht(e),o=t.getImmediate({optional:!0});if(o){const c=await o.getHeartbeatsHeader();c&&r.append("x-firebase-client",c)}const s={fid:n,authVersion:ct,appId:e.appId,sdkVersion:at},a={method:"POST",headers:r,body:JSON.stringify(s)},l=await pt(()=>fetch(i,a));if(l.ok){const c=await l.json();return{fid:c.fid||n,registrationStatus:2,refreshToken:c.refreshToken,authToken:dt(c.authToken)}}else throw await ft("Create Installation",l)}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function gt(e){return new Promise(t=>{setTimeout(t,e)})}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function wi(e){return btoa(String.fromCharCode(...e)).replace(/\+/g,"-").replace(/\//g,"_")}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const yi=/^[cdef][\w-]{21}$/,se="";function Ii(){try{const e=new Uint8Array(17);(self.crypto||self.msCrypto).getRandomValues(e),e[0]=112+e[0]%16;const n=Ti(e);return yi.test(n)?n:se}catch{return se}}function Ti(e){return wi(e).substr(0,22)}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function V(e){return`${e.appName}!${e.appId}`}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const mt=new Map;function bt(e,t){const n=V(e);wt(n,t),Ei(n,t)}function wt(e,t){const n=mt.get(e);if(n)for(const i of n)i(t)}function Ei(e,t){const n=vi();n&&n.postMessage({key:e,fid:t}),Ai()}let S=null;function vi(){return!S&&"BroadcastChannel"in self&&(S=new BroadcastChannel("[Firebase] FID Change"),S.onmessage=e=>{wt(e.data.key,e.data.fid)}),S}function Ai(){mt.size===0&&S&&(S.close(),S=null)}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const Si="firebase-installations-database",_i=1,C="firebase-installations-store";let Y=null;function de(){return Y||(Y=H(Si,_i,{upgrade:(e,t)=>{switch(t){case 0:e.createObjectStore(C)}}})),Y}async function x(e,t){const n=V(e),r=(await de()).transaction(C,"readwrite"),o=r.objectStore(C),s=await o.get(n);return await o.put(t,n),await r.done,(!s||s.fid!==t.fid)&&bt(e,t.fid),t}async function yt(e){const t=V(e),i=(await de()).transaction(C,"readwrite");await i.objectStore(C).delete(t),await i.done}async function U(e,t){const n=V(e),r=(await de()).transaction(C,"readwrite"),o=r.objectStore(C),s=await o.get(n),a=t(s);return a===void 0?await o.delete(n):await o.put(a,n),await r.done,a&&(!s||s.fid!==a.fid)&&bt(e,a.fid),a}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function fe(e){let t;const n=await U(e.appConfig,i=>{const r=Ci(i),o=ki(e,r);return t=o.registrationPromise,o.installationEntry});return n.fid===se?{installationEntry:await t}:{installationEntry:n,registrationPromise:t}}function Ci(e){const t=e||{fid:Ii(),registrationStatus:0};return It(t)}function ki(e,t){if(t.registrationStatus===0){if(!navigator.onLine){const r=Promise.reject(_.create("app-offline"));return{installationEntry:t,registrationPromise:r}}const n={fid:t.fid,registrationStatus:1,registrationTime:Date.now()},i=Di(e,n);return{installationEntry:n,registrationPromise:i}}else return t.registrationStatus===1?{installationEntry:t,registrationPromise:Mi(e)}:{installationEntry:t}}async function Di(e,t){try{const n=await bi(e,t);return x(e.appConfig,n)}catch(n){throw lt(n)&&n.customData.serverCode===409?await yt(e.appConfig):await x(e.appConfig,{fid:t.fid,registrationStatus:0}),n}}async function Mi(e){let t=await Me(e.appConfig);for(;t.registrationStatus===1;)await gt(100),t=await Me(e.appConfig);if(t.registrationStatus===0){const{installationEntry:n,registrationPromise:i}=await fe(e);return i||n}return t}function Me(e){return U(e,t=>{if(!t)throw _.create("installation-not-found");return It(t)})}function It(e){return Fi(e)?{fid:e.fid,registrationStatus:0}:e}function Fi(e){return e.registrationStatus===1&&e.registrationTime+st<Date.now()}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function Oi({appConfig:e,heartbeatServiceProvider:t},n){const i=Ri(e,n),r=pi(e,n),o=t.getImmediate({optional:!0});if(o){const c=await o.getHeartbeatsHeader();c&&r.append("x-firebase-client",c)}const s={installation:{sdkVersion:at,appId:e.appId}},a={method:"POST",headers:r,body:JSON.stringify(s)},l=await pt(()=>fetch(i,a));if(l.ok){const c=await l.json();return dt(c)}else throw await ft("Generate Auth Token",l)}function Ri(e,{fid:t}){return`${ut(e)}/${t}/authTokens:generate`}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function he(e,t=!1){let n;const i=await U(e.appConfig,o=>{if(!Tt(o))throw _.create("not-registered");const s=o.authToken;if(!t&&$i(s))return o;if(s.requestStatus===1)return n=Ni(e,t),o;{if(!navigator.onLine)throw _.create("app-offline");const a=Li(o);return n=Pi(e,a),a}});return n?await n:i.authToken}async function Ni(e,t){let n=await Fe(e.appConfig);for(;n.authToken.requestStatus===1;)await gt(100),n=await Fe(e.appConfig);const i=n.authToken;return i.requestStatus===0?he(e,t):i}function Fe(e){return U(e,t=>{if(!Tt(t))throw _.create("not-registered");const n=t.authToken;return xi(n)?{...t,authToken:{requestStatus:0}}:t})}async function Pi(e,t){try{const n=await Oi(e,t),i={...t,authToken:n};return await x(e.appConfig,i),n}catch(n){if(lt(n)&&(n.customData.serverCode===401||n.customData.serverCode===404))await yt(e.appConfig);else{const i={...t,authToken:{requestStatus:0}};await x(e.appConfig,i)}throw n}}function Tt(e){return e!==void 0&&e.registrationStatus===2}function $i(e){return e.requestStatus===2&&!Bi(e)}function Bi(e){const t=Date.now();return t<e.creationTime||e.creationTime+e.expiresIn<t+ui}function Li(e){const t={requestStatus:1,requestTime:Date.now()};return{...e,authToken:t}}function xi(e){return e.requestStatus===1&&e.requestTime+st<Date.now()}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function ji(e){const t=e,{installationEntry:n,registrationPromise:i}=await fe(t);return i?i.catch(console.error):he(t).catch(console.error),n.fid}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function Hi(e,t=!1){const n=e;return await Vi(n),(await he(n,t)).token}async function Vi(e){const{registrationPromise:t}=await fe(e);t&&await t}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function Ui(e){if(!e||!e.options)throw J("App Configuration");if(!e.name)throw J("App Name");const t=["projectId","apiKey","appId"];for(const n of t)if(!e.options[n])throw J(n);return{appName:e.name,projectId:e.options.projectId,apiKey:e.options.apiKey,appId:e.options.appId}}function J(e){return _.create("missing-app-config-values",{valueName:e})}/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const Et="installations",qi="installations-internal",Ki=e=>{const t=e.getProvider("app").getImmediate(),n=Ui(t),i=P(t,"heartbeat");return{app:t,appConfig:n,heartbeatServiceProvider:i,_delete:()=>Promise.resolve()}},zi=e=>{const t=e.getProvider("app").getImmediate(),n=P(t,Et).getImmediate();return{getId:()=>ji(n),getToken:r=>Hi(n,r)}};function Wi(){E(new w(Et,Ki,"PUBLIC")),E(new w(qi,zi,"PRIVATE"))}Wi();b(ot,ue);b(ot,ue,"esm2020");/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const j="analytics",Gi="firebase_id",Yi="origin",Ji=60*1e3,Xi="https://firebase.googleapis.com/v1alpha/projects/-/apps/{app-id}/webConfig",pe="https://www.googletagmanager.com/gtag/js";/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const f=new Ze("@firebase/analytics");/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const Zi={"already-exists":"A Firebase Analytics instance with the appId {$id}  already exists. Only one Firebase Analytics instance can be created for each appId.","already-initialized":"initializeAnalytics() cannot be called again with different options than those it was initially called with. It can be called again with the same options to return the existing instance, or getAnalytics() can be used to get a reference to the already-initialized instance.","already-initialized-settings":"Firebase Analytics has already been initialized.settings() must be called before initializing any Analytics instanceor it will have no effect.","interop-component-reg-failed":"Firebase Analytics Interop Component failed to instantiate: {$reason}","invalid-analytics-context":"Firebase Analytics is not supported in this environment. Wrap initialization of analytics in analytics.isSupported() to prevent initialization in unsupported environments. Details: {$errorInfo}","indexeddb-unavailable":"IndexedDB unavailable or restricted in this environment. Wrap initialization of analytics in analytics.isSupported() to prevent initialization in unsupported environments. Details: {$errorInfo}","fetch-throttle":"The config fetch request timed out while in an exponential backoff state. Unix timestamp in milliseconds when fetch request throttling ends: {$throttleEndTimeMillis}.","config-fetch-failed":"Dynamic config fetch failed: [{$httpStatus}] {$responseMessage}","no-api-key":'The "apiKey" field is empty in the local Firebase config. Firebase Analytics requires this field tocontain a valid API key.',"no-app-id":'The "appId" field is empty in the local Firebase config. Firebase Analytics requires this field tocontain a valid app ID.',"no-client-id":'The "client_id" field is empty.',"invalid-gtag-resource":"Trusted Types detected an invalid gtag resource: {$gtagURL}."},g=new N("analytics","Analytics",Zi);/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function Qi(e){if(!e.startsWith(pe)){const t=g.create("invalid-gtag-resource",{gtagURL:e});return f.warn(t.message),""}return e}function vt(e){return Promise.all(e.map(t=>t.catch(n=>n)))}function er(e,t){let n;return window.trustedTypes&&(n=window.trustedTypes.createPolicy(e,t)),n}function tr(e,t){const n=er("firebase-js-sdk-policy",{createScriptURL:Qi}),i=document.createElement("script"),r=`${pe}?l=${e}&id=${t}`;i.src=n?n?.createScriptURL(r):r,i.async=!0,document.head.appendChild(i)}function nr(e){let t=[];return Array.isArray(window[e])?t=window[e]:window[e]=t,t}async function ir(e,t,n,i,r,o){const s=i[r];try{if(s)await t[s];else{const l=(await vt(n)).find(c=>c.measurementId===r);l&&await t[l.appId]}}catch(a){f.error(a)}e("config",r,o)}async function rr(e,t,n,i,r){try{let o=[];if(r&&r.send_to){let s=r.send_to;Array.isArray(s)||(s=[s]);const a=await vt(n);for(const l of s){const c=a.find(m=>m.measurementId===l),h=c&&t[c.appId];if(h)o.push(h);else{o=[];break}}}o.length===0&&(o=Object.values(t)),await Promise.all(o),e("event",i,r||{})}catch(o){f.error(o)}}function or(e,t,n,i){async function r(o,...s){try{if(o==="event"){const[a,l]=s;await rr(e,t,n,a,l)}else if(o==="config"){const[a,l]=s;await ir(e,t,n,i,a,l)}else if(o==="consent"){const[a,l]=s;e("consent",a,l)}else if(o==="get"){const[a,l,c]=s;e("get",a,l,c)}else if(o==="set"){const[a]=s;e("set",a)}else e(o,...s)}catch(a){f.error(a)}}return r}function sr(e,t,n,i,r){let o=function(...s){window[i].push(arguments)};return window[r]&&typeof window[r]=="function"&&(o=window[r]),window[r]=or(o,e,t,n),{gtagCore:o,wrappedGtag:window[r]}}function ar(e){const t=window.document.getElementsByTagName("script");for(const n of Object.values(t))if(n.src&&n.src.includes(pe)&&n.src.includes(e))return n;return null}/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const cr=30,lr=1e3;class ur{constructor(t={},n=lr){this.throttleMetadata=t,this.intervalMillis=n}getThrottleMetadata(t){return this.throttleMetadata[t]}setThrottleMetadata(t,n){this.throttleMetadata[t]=n}deleteThrottleMetadata(t){delete this.throttleMetadata[t]}}const At=new ur;function dr(e){return new Headers({Accept:"application/json","x-goog-api-key":e})}async function fr(e){const{appId:t,apiKey:n}=e,i={method:"GET",headers:dr(n)},r=Xi.replace("{app-id}",t),o=await fetch(r,i);if(o.status!==200&&o.status!==304){let s="";try{const a=await o.json();a.error?.message&&(s=a.error.message)}catch{}throw g.create("config-fetch-failed",{httpStatus:o.status,responseMessage:s})}return o.json()}async function hr(e,t=At,n){const{appId:i,apiKey:r,measurementId:o}=e.options;if(!i)throw g.create("no-app-id");if(!r){if(o)return{measurementId:o,appId:i};throw g.create("no-api-key")}const s=t.getThrottleMetadata(i)||{backoffCount:0,throttleEndTimeMillis:Date.now()},a=new mr;return setTimeout(async()=>{a.abort()},Ji),St({appId:i,apiKey:r,measurementId:o},s,a,t)}async function St(e,{throttleEndTimeMillis:t,backoffCount:n},i,r=At){const{appId:o,measurementId:s}=e;try{await pr(i,t)}catch(a){if(s)return f.warn(`Timed out fetching this Firebase app's measurement ID from the server. Falling back to the measurement ID ${s} provided in the "measurementId" field in the local Firebase config. [${a?.message}]`),{appId:o,measurementId:s};throw a}try{const a=await fr(e);return r.deleteThrottleMetadata(o),a}catch(a){const l=a;if(!gr(l)){if(r.deleteThrottleMetadata(o),s)return f.warn(`Failed to fetch this Firebase app's measurement ID from the server. Falling back to the measurement ID ${s} provided in the "measurementId" field in the local Firebase config. [${l?.message}]`),{appId:o,measurementId:s};throw a}const c=Number(l?.customData?.httpStatus)===503?Te(n,r.intervalMillis,cr):Te(n,r.intervalMillis),h={throttleEndTimeMillis:Date.now()+c,backoffCount:n+1};return r.setThrottleMetadata(o,h),f.debug(`Calling attemptFetch again in ${c} millis`),St(e,h,i,r)}}function pr(e,t){return new Promise((n,i)=>{const r=Math.max(t-Date.now(),0),o=setTimeout(n,r);e.addEventListener(()=>{clearTimeout(o),i(g.create("fetch-throttle",{throttleEndTimeMillis:t}))})})}function gr(e){if(!(e instanceof k)||!e.customData)return!1;const t=Number(e.customData.httpStatus);return t===429||t===500||t===503||t===504}class mr{constructor(){this.listeners=[]}addEventListener(t){this.listeners.push(t)}abort(){this.listeners.forEach(t=>t())}}async function br(e,t,n,i,r){if(r&&r.global){e("event",n,i);return}else{const o=await t,s={...i,send_to:o};e("event",n,s)}}async function wr(e,t,n,i){if(i&&i.global){const r={};for(const o of Object.keys(n))r[`user_properties.${o}`]=n[o];return e("set",r),Promise.resolve()}else{const r=await t;e("config",r,{update:!0,user_properties:n})}}/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function yr(){if(ae())try{await ce()}catch(e){return f.warn(g.create("indexeddb-unavailable",{errorInfo:e?.toString()}).message),!1}else return f.warn(g.create("indexeddb-unavailable",{errorInfo:"IndexedDB is not available in this environment."}).message),!1;return!0}async function Ir(e,t,n,i,r,o,s){const a=hr(e);a.then(p=>{n[p.measurementId]=p.appId,e.options.measurementId&&p.measurementId!==e.options.measurementId&&f.warn(`The measurement ID in the local Firebase config (${e.options.measurementId}) does not match the measurement ID fetched from the server (${p.measurementId}). To ensure analytics events are always sent to the correct Analytics property, update the measurement ID field in the local config or remove it from the local config.`)}).catch(p=>f.error(p)),t.push(a);const l=yr().then(p=>{if(p)return i.getId()}),[c,h]=await Promise.all([a,l]);ar(o)||tr(o,c.measurementId),r("js",new Date);const m=s?.config??{};return m[Yi]="firebase",m.update=!0,h!=null&&(m[Gi]=h),r("config",c.measurementId,m),c.measurementId}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */class Tr{constructor(t){this.app=t}_delete(){return delete D[this.app.options.appId],Promise.resolve()}}let D={},Oe=[];const Re={};let X="dataLayer",Er="gtag",Ne,ge,Pe=!1;function vr(){const e=[];if(zt()&&e.push("This is a browser extension environment."),Xe()||e.push("Cookies are not available."),e.length>0){const t=e.map((i,r)=>`(${r+1}) ${i}`).join(" "),n=g.create("invalid-analytics-context",{errorInfo:t});f.warn(n.message)}}function Ar(e,t,n){vr();const i=e.options.appId;if(!i)throw g.create("no-app-id");if(!e.options.apiKey)if(e.options.measurementId)f.warn(`The "apiKey" field is empty in the local Firebase config. This is needed to fetch the latest measurement ID for this Firebase app. Falling back to the measurement ID ${e.options.measurementId} provided in the "measurementId" field in the local Firebase config.`);else throw g.create("no-api-key");if(D[i]!=null)throw g.create("already-exists",{id:i});if(!Pe){nr(X);const{wrappedGtag:o,gtagCore:s}=sr(D,Oe,Re,X,Er);ge=o,Ne=s,Pe=!0}return D[i]=Ir(e,Oe,Re,t,Ne,X,n),new Tr(e)}function Sr(e=nt()){e=M(e);const t=P(e,j);return t.isInitialized()?t.getImmediate():_r(e)}function _r(e,t={}){const n=P(e,j);if(n.isInitialized()){const r=n.getImmediate();if(B(t,n.getOptions()))return r;throw g.create("already-initialized")}return n.initialize({options:t})}function Cr(e,t,n){e=M(e),wr(ge,D[e.app.options.appId],t,n).catch(i=>f.error(i))}function kr(e,t,n,i){e=M(e),br(ge,D[e.app.options.appId],t,n,i).catch(r=>f.error(r))}const $e="@firebase/analytics",Be="0.10.19";function Dr(){E(new w(j,(t,{options:n})=>{const i=t.getProvider("app").getImmediate(),r=t.getProvider("installations-internal").getImmediate();return Ar(i,r,n)},"PUBLIC")),E(new w("analytics-internal",e,"PRIVATE")),b($e,Be),b($e,Be,"esm2020");function e(t){try{const n=t.getProvider(j).getImmediate();return{logEvent:(i,r,o)=>kr(n,i,r,o),setUserProperties:(i,r)=>Cr(n,i,r)}}catch(n){throw g.create("interop-component-reg-failed",{reason:n})}}}Dr();/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const Mr="/firebase-messaging-sw.js",Fr="/firebase-cloud-messaging-push-scope",_t="BDOU99-h67HcA6JeFXHbSNMu7e2yNNu3RzoMj8TM4W88jITfq7ZmPvIM1Iv-4_l2LxQcYwhqby2xGpWwzjfAnG4",Or="https://fcmregistrations.googleapis.com/v1",Ct="google.c.a.c_id",Rr="google.c.a.c_l",Nr="google.c.a.ts",Pr="google.c.a.e",Le=1e4;var xe;(function(e){e[e.DATA_MESSAGE=1]="DATA_MESSAGE",e[e.DISPLAY_NOTIFICATION=3]="DISPLAY_NOTIFICATION"})(xe||(xe={}));/**
 * @license
 * Copyright 2018 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except
 * in compliance with the License. You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software distributed under the License
 * is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express
 * or implied. See the License for the specific language governing permissions and limitations under
 * the License.
 */var O;(function(e){e.PUSH_RECEIVED="push-received",e.NOTIFICATION_CLICKED="notification-clicked"})(O||(O={}));/**
 * @license
 * Copyright 2017 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function y(e){const t=new Uint8Array(e);return btoa(String.fromCharCode(...t)).replace(/=/g,"").replace(/\+/g,"-").replace(/\//g,"_")}function $r(e){const t="=".repeat((4-e.length%4)%4),n=(e+t).replace(/\-/g,"+").replace(/_/g,"/"),i=atob(n),r=new Uint8Array(i.length);for(let o=0;o<i.length;++o)r[o]=i.charCodeAt(o);return r}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const Z="fcm_token_details_db",Br=5,je="fcm_token_object_Store";async function Lr(e){if("databases"in indexedDB&&!(await indexedDB.databases()).map(o=>o.name).includes(Z))return null;let t=null;return(await H(Z,Br,{upgrade:async(i,r,o,s)=>{if(r<2||!i.objectStoreNames.contains(je))return;const a=s.objectStore(je),l=await a.index("fcmSenderId").get(e);if(await a.clear(),!!l){if(r===2){const c=l;if(!c.auth||!c.p256dh||!c.endpoint)return;t={token:c.fcmToken,createTime:c.createTime??Date.now(),subscriptionOptions:{auth:c.auth,p256dh:c.p256dh,endpoint:c.endpoint,swScope:c.swScope,vapidKey:typeof c.vapidKey=="string"?c.vapidKey:y(c.vapidKey)}}}else if(r===3){const c=l;t={token:c.fcmToken,createTime:c.createTime,subscriptionOptions:{auth:y(c.auth),p256dh:y(c.p256dh),endpoint:c.endpoint,swScope:c.swScope,vapidKey:y(c.vapidKey)}}}else if(r===4){const c=l;t={token:c.fcmToken,createTime:c.createTime,subscriptionOptions:{auth:y(c.auth),p256dh:y(c.p256dh),endpoint:c.endpoint,swScope:c.swScope,vapidKey:y(c.vapidKey)}}}}}})).close(),await z(Z),await z("fcm_vapid_details_db"),await z("undefined"),xr(t)?t:null}function xr(e){if(!e||!e.subscriptionOptions)return!1;const{subscriptionOptions:t}=e;return typeof e.createTime=="number"&&e.createTime>0&&typeof e.token=="string"&&e.token.length>0&&typeof t.auth=="string"&&t.auth.length>0&&typeof t.p256dh=="string"&&t.p256dh.length>0&&typeof t.endpoint=="string"&&t.endpoint.length>0&&typeof t.swScope=="string"&&t.swScope.length>0&&typeof t.vapidKey=="string"&&t.vapidKey.length>0}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const jr="firebase-messaging-database",Hr=1,R="firebase-messaging-store";let Q=null;function kt(){return Q||(Q=H(jr,Hr,{upgrade:(e,t)=>{switch(t){case 0:e.createObjectStore(R)}}})),Q}async function Vr(e){const t=Dt(e),i=await(await kt()).transaction(R).objectStore(R).get(t);if(i)return i;{const r=await Lr(e.appConfig.senderId);if(r)return await me(e,r),r}}async function me(e,t){const n=Dt(e),r=(await kt()).transaction(R,"readwrite");return await r.objectStore(R).put(t,n),await r.done,t}function Dt({appConfig:e}){return e.appId}/**
 * @license
 * Copyright 2017 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const Ur={"missing-app-config-values":'Missing App configuration value: "{$valueName}"',"only-available-in-window":"This method is available in a Window context.","only-available-in-sw":"This method is available in a service worker context.","permission-default":"The notification permission was not granted and dismissed instead.","permission-blocked":"The notification permission was not granted and blocked instead.","unsupported-browser":"This browser doesn't support the API's required to use the Firebase SDK.","indexed-db-unsupported":"This browser doesn't support indexedDb.open() (ex. Safari iFrame, Firefox Private Browsing, etc)","failed-service-worker-registration":"We are unable to register the default service worker. {$browserErrorMessage}","token-subscribe-failed":"A problem occurred while subscribing the user to FCM: {$errorInfo}","token-subscribe-no-token":"FCM returned no token when subscribing the user to push.","token-unsubscribe-failed":"A problem occurred while unsubscribing the user from FCM: {$errorInfo}","token-update-failed":"A problem occurred while updating the user from FCM: {$errorInfo}","token-update-no-token":"FCM returned no token when updating the user to push.","use-sw-after-get-token":"The useServiceWorker() method may only be called once and must be called before calling getToken() to ensure your service worker is used.","invalid-sw-registration":"The input to useServiceWorker() must be a ServiceWorkerRegistration.","invalid-bg-handler":"The input to setBackgroundMessageHandler() must be a function.","invalid-vapid-key":"The public VAPID key must be a string.","use-vapid-key-after-get-token":"The usePublicVapidKey() method may only be called once and must be called before calling getToken() to ensure your VAPID key is used."},d=new N("messaging","Messaging",Ur);/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function qr(e,t){const n=await we(e),i=Mt(t),r={method:"POST",headers:n,body:JSON.stringify(i)};let o;try{o=await(await fetch(be(e.appConfig),r)).json()}catch(s){throw d.create("token-subscribe-failed",{errorInfo:s?.toString()})}if(o.error){const s=o.error.message;throw d.create("token-subscribe-failed",{errorInfo:s})}if(!o.token)throw d.create("token-subscribe-no-token");return o.token}async function Kr(e,t){const n=await we(e),i=Mt(t.subscriptionOptions),r={method:"PATCH",headers:n,body:JSON.stringify(i)};let o;try{o=await(await fetch(`${be(e.appConfig)}/${t.token}`,r)).json()}catch(s){throw d.create("token-update-failed",{errorInfo:s?.toString()})}if(o.error){const s=o.error.message;throw d.create("token-update-failed",{errorInfo:s})}if(!o.token)throw d.create("token-update-no-token");return o.token}async function zr(e,t){const i={method:"DELETE",headers:await we(e)};try{const o=await(await fetch(`${be(e.appConfig)}/${t}`,i)).json();if(o.error){const s=o.error.message;throw d.create("token-unsubscribe-failed",{errorInfo:s})}}catch(r){throw d.create("token-unsubscribe-failed",{errorInfo:r?.toString()})}}function be({projectId:e}){return`${Or}/projects/${e}/registrations`}async function we({appConfig:e,installations:t}){const n=await t.getToken();return new Headers({"Content-Type":"application/json",Accept:"application/json","x-goog-api-key":e.apiKey,"x-goog-firebase-installations-auth":`FIS ${n}`})}function Mt({p256dh:e,auth:t,endpoint:n,vapidKey:i}){const r={web:{endpoint:n,auth:t,p256dh:e}};return i!==_t&&(r.web.applicationPubKey=i),r}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const Wr=10080*60*1e3;async function Gr(e){const t=await Jr(e.swRegistration,e.vapidKey),n={vapidKey:e.vapidKey,swScope:e.swRegistration.scope,endpoint:t.endpoint,auth:y(t.getKey("auth")),p256dh:y(t.getKey("p256dh"))},i=await Vr(e.firebaseDependencies);if(i){if(Xr(i.subscriptionOptions,n))return Date.now()>=i.createTime+Wr?Yr(e,{token:i.token,createTime:Date.now(),subscriptionOptions:n}):i.token;try{await zr(e.firebaseDependencies,i.token)}catch(r){console.warn(r)}return He(e.firebaseDependencies,n)}else return He(e.firebaseDependencies,n)}async function Yr(e,t){try{const n=await Kr(e.firebaseDependencies,t),i={...t,token:n,createTime:Date.now()};return await me(e.firebaseDependencies,i),n}catch(n){throw n}}async function He(e,t){const i={token:await qr(e,t),createTime:Date.now(),subscriptionOptions:t};return await me(e,i),i.token}async function Jr(e,t){const n=await e.pushManager.getSubscription();return n||e.pushManager.subscribe({userVisibleOnly:!0,applicationServerKey:$r(t)})}function Xr(e,t){const n=t.vapidKey===e.vapidKey,i=t.endpoint===e.endpoint,r=t.auth===e.auth,o=t.p256dh===e.p256dh;return n&&i&&r&&o}/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function Ve(e){const t={from:e.from,collapseKey:e.collapse_key,messageId:e.fcmMessageId};return Zr(t,e),Qr(t,e),eo(t,e),t}function Zr(e,t){if(!t.notification)return;e.notification={};const n=t.notification.title;n&&(e.notification.title=n);const i=t.notification.body;i&&(e.notification.body=i);const r=t.notification.image;r&&(e.notification.image=r);const o=t.notification.icon;o&&(e.notification.icon=o)}function Qr(e,t){t.data&&(e.data=t.data)}function eo(e,t){if(!t.fcmOptions&&!t.notification?.click_action)return;e.fcmOptions={};const n=t.fcmOptions?.link??t.notification?.click_action;n&&(e.fcmOptions.link=n);const i=t.fcmOptions?.analytics_label;i&&(e.fcmOptions.analyticsLabel=i)}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function to(e){return typeof e=="object"&&!!e&&Ct in e}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function no(e){if(!e||!e.options)throw ee("App Configuration Object");if(!e.name)throw ee("App Name");const t=["projectId","apiKey","appId","messagingSenderId"],{options:n}=e;for(const i of t)if(!n[i])throw ee(i);return{appName:e.name,projectId:n.projectId,apiKey:n.apiKey,appId:n.appId,senderId:n.messagingSenderId}}function ee(e){return d.create("missing-app-config-values",{valueName:e})}/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */class io{constructor(t,n,i){this.deliveryMetricsExportedToBigQueryEnabled=!1,this.onBackgroundMessageHandler=null,this.onMessageHandler=null,this.logEvents=[],this.isLogServiceStarted=!1;const r=no(t);this.firebaseDependencies={app:t,appConfig:r,installations:n,analyticsProvider:i}}_delete(){return Promise.resolve()}}/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function ro(e){try{e.swRegistration=await navigator.serviceWorker.register(Mr,{scope:Fr}),e.swRegistration.update().catch(()=>{}),await oo(e.swRegistration)}catch(t){throw d.create("failed-service-worker-registration",{browserErrorMessage:t?.message})}}async function oo(e){return new Promise((t,n)=>{const i=setTimeout(()=>n(new Error(`Service worker not registered after ${Le} ms`)),Le),r=e.installing||e.waiting;e.active?(clearTimeout(i),t()):r?r.onstatechange=o=>{o.target?.state==="activated"&&(r.onstatechange=null,clearTimeout(i),t())}:(clearTimeout(i),n(new Error("No incoming service worker found.")))})}/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function so(e,t){if(!t&&!e.swRegistration&&await ro(e),!(!t&&e.swRegistration)){if(!(t instanceof ServiceWorkerRegistration))throw d.create("invalid-sw-registration");e.swRegistration=t}}/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function ao(e,t){t?e.vapidKey=t:e.vapidKey||(e.vapidKey=_t)}/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function Ft(e,t){if(!navigator)throw d.create("only-available-in-window");if(Notification.permission==="default"&&await Notification.requestPermission(),Notification.permission!=="granted")throw d.create("permission-blocked");return await ao(e,t?.vapidKey),await so(e,t?.serviceWorkerRegistration),Gr(e)}/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function co(e,t,n){const i=lo(t);(await e.firebaseDependencies.analyticsProvider.get()).logEvent(i,{message_id:n[Ct],message_name:n[Rr],message_time:n[Nr],message_device_time:Math.floor(Date.now()/1e3)})}function lo(e){switch(e){case O.NOTIFICATION_CLICKED:return"notification_open";case O.PUSH_RECEIVED:return"notification_foreground";default:throw new Error}}/**
 * @license
 * Copyright 2017 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function uo(e,t){const n=t.data;if(!n.isFirebaseMessaging)return;e.onMessageHandler&&n.messageType===O.PUSH_RECEIVED&&(typeof e.onMessageHandler=="function"?e.onMessageHandler(Ve(n)):e.onMessageHandler.next(Ve(n)));const i=n.data;to(i)&&i[Pr]==="1"&&await co(e,n.messageType,i)}const Ue="@firebase/messaging",qe="0.12.23";/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const fo=e=>{const t=new io(e.getProvider("app").getImmediate(),e.getProvider("installations-internal").getImmediate(),e.getProvider("analytics-internal"));return navigator.serviceWorker.addEventListener("message",n=>uo(t,n)),t},ho=e=>{const t=e.getProvider("messaging").getImmediate();return{getToken:i=>Ft(t,i)}};function po(){E(new w("messaging",fo,"PUBLIC")),E(new w("messaging-internal",ho,"PRIVATE")),b(Ue,qe),b(Ue,qe,"esm2020")}/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function go(){try{await ce()}catch{return!1}return typeof window<"u"&&ae()&&Xe()&&"serviceWorker"in navigator&&"PushManager"in window&&"Notification"in window&&"fetch"in window&&ServiceWorkerRegistration.prototype.hasOwnProperty("showNotification")&&PushSubscription.prototype.hasOwnProperty("getKey")}/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function mo(e,t){if(!navigator)throw d.create("only-available-in-window");return e.onMessageHandler=t,()=>{e.onMessageHandler=null}}/**
 * @license
 * Copyright 2017 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function bo(e=nt()){return go().then(t=>{if(!t)throw d.create("unsupported-browser")},t=>{throw d.create("indexed-db-unsupported")}),P(M(e),"messaging").getImmediate()}async function wo(e,t){return e=M(e),Ft(e,t)}function yo(e,t){return e=M(e),mo(e,t)}po();const Io={apiKey:"AIzaSyB-h_XCrdqpO38AFP2ZvUdXd0Yq4MB104w",authDomain:"sels-e407c.firebaseapp.com",projectId:"sels-e407c",storageBucket:"sels-e407c.firebasestorage.app",messagingSenderId:"689946728760",appId:"1:689946728760:web:605322353c7706bb8409cd",measurementId:"G-SZF8EVP8Y1"},Ot=tt(Io);typeof window<"u"&&Sr(Ot);let Rt=null;if(typeof window<"u"&&"serviceWorker"in navigator)try{Rt=bo(Ot)}catch(e){console.warn("Firebase Messaging initialization failed:",e)}class To{constructor(){this.messaging=Rt,this.currentToken=null}async requestPermission(){if(!this.messaging)return console.warn("Firebase Messaging is not available"),!1;try{return await Notification.requestPermission()==="granted"?(console.log("Notification permission granted."),!0):(console.log("Notification permission denied."),!1)}catch(t){return console.error("Error requesting notification permission:",t),!1}}async getToken(t=null){if(console.log("[FCM] getToken called",{hasVapidKey:!!t}),!this.messaging)return console.warn("[FCM] Firebase Messaging is not available"),null;try{if(console.log("[FCM] Checking notification permission:",Notification.permission),Notification.permission!=="granted"){if(console.log("[FCM] Permission not granted, requesting permission..."),!await this.requestPermission())return console.warn("[FCM] Permission request denied or failed"),null;console.log("[FCM] Permission granted successfully")}console.log("[FCM] Getting service worker registration...");const n=await navigator.serviceWorker.ready;console.log("[FCM] Service worker ready:",!!n),console.log("[FCM] Requesting FCM token from Firebase...");const i=await wo(this.messaging,{vapidKey:t||void 0,serviceWorkerRegistration:n});return i?(this.currentToken=i,console.log("[FCM] ✅ FCM Registration token obtained:",i.substring(0,50)+"..."),i):(console.warn("[FCM] ⚠️ No registration token available from Firebase"),null)}catch(n){return console.error("[FCM] ❌ Error occurred while retrieving token:",n),console.error("[FCM] Error details:",{name:n.name,message:n.message,stack:n.stack}),null}}onMessage(t){if(!this.messaging){console.warn("Firebase Messaging is not available");return}yo(this.messaging,n=>{console.log("Message received in foreground:",n),t&&typeof t=="function"&&t(n)})}async sendTokenToServer(t,n="/api/fcm/token"){if(console.log("[FCM] sendTokenToServer called",{endpoint:n,tokenLength:t?.length||0,tokenPreview:t?t.substring(0,50)+"...":"null"}),!t)return console.error("[FCM] ❌ Cannot send token: token is null or empty"),!1;try{const i=document.querySelector('meta[name="csrf-token"]')?.getAttribute("content")||"",r={"Content-Type":"application/json",Accept:"application/json","X-Requested-With":"XMLHttpRequest"};i?(r["X-CSRF-TOKEN"]=i,console.log("[FCM] CSRF token found and added to headers")):console.warn("[FCM] ⚠️ CSRF token not found in meta tag"),console.log("[FCM] Sending POST request to:",n),console.log("[FCM] Request headers:",{...r,"X-CSRF-TOKEN":i?"***":"missing"}),console.log("[FCM] Request body:",{token:t.substring(0,50)+"..."});const o=await fetch(n,{method:"POST",headers:r,credentials:"same-origin",body:JSON.stringify({token:t})});if(console.log("[FCM] Response received:",{status:o.status,statusText:o.statusText,ok:o.ok,headers:Object.fromEntries([...o.headers.entries()])}),o.ok){const s=await o.json();return console.log("[FCM] ✅ Token sent to server successfully:",s),!0}else{let s;try{s=await o.json()}catch{s={message:await o.text()||"Unknown error"}}return console.error("[FCM] ❌ Failed to send token to server:",{status:o.status,statusText:o.statusText,error:s}),!1}}catch(i){return console.error("[FCM] ❌ Exception occurred while sending token to server:",i),console.error("[FCM] Error details:",{name:i.name,message:i.message,stack:i.stack}),!1}}async init(t={}){console.log("[FCM] Initializing Firebase Messaging...",{hasVapidKey:!!t.vapidKey,tokenEndpoint:t.tokenEndpoint,hasCallback:!!t.onMessageCallback,autoRequestPermission:t.autoRequestPermission});const{vapidKey:n=null,tokenEndpoint:i="/api/fcm/token",onMessageCallback:r=null,autoRequestPermission:o=!1}=t;if(!this.messaging)return console.warn("[FCM] ❌ Firebase Messaging is not available. Make sure service worker is registered."),!1;try{r&&(console.log("[FCM] Setting up foreground message listener..."),this.onMessage(r)),o&&(console.log("[FCM] Auto-requesting notification permission..."),await this.requestPermission()||console.warn("[FCM] ⚠️ Permission not granted, token may not be available")),console.log("[FCM] Getting FCM token...");const s=await this.getToken(n);return s&&i?(console.log("[FCM] Token obtained, sending to server..."),await this.sendTokenToServer(s,i)?console.log("[FCM] ✅ Firebase Messaging initialized and token saved successfully"):console.error("[FCM] ❌ Failed to send token to server")):(s||console.warn("[FCM] ⚠️ No token obtained, cannot send to server"),i||console.warn("[FCM] ⚠️ No token endpoint provided")),!0}catch(s){return console.error("[FCM] ❌ Error during initialization:",s),console.error("[FCM] Error details:",{name:s.name,message:s.message,stack:s.stack}),!1}}}const Ke=new To;async function Eo(e={}){const n={...{tokenEndpoint:"/api/fcm/token",onMessageCallback:i=>{console.log("Foreground message received:",i)},autoRequestPermission:!0},...e};try{if("serviceWorker"in navigator){const r=await navigator.serviceWorker.register("/firebase-messaging-sw.js");console.log("Service Worker registered:",r)}return await Ke.init(n)?(console.log("Firebase Messaging initialized successfully"),Ke):(console.warn("Firebase Messaging initialization failed"),null)}catch(i){return console.error("Error initializing Firebase Messaging:",i),null}}function vo(){try{const e=new Audio("/sounds/notification.mp3");e.volume=.5,e.play().catch(t=>{console.log("[Notifications] Audio file not found, using Web Audio API fallback:",t),ze()})}catch(e){console.log("[Notifications] Using Web Audio API fallback:",e),ze()}}function ze(){try{const e=new(window.AudioContext||window.webkitAudioContext),t=e.createOscillator(),n=e.createGain();t.connect(n),n.connect(e.destination),t.frequency.setValueAtTime(800,e.currentTime),t.type="sine",n.gain.setValueAtTime(.3,e.currentTime),n.gain.exponentialRampToValueAtTime(.01,e.currentTime+.1),t.start(e.currentTime),t.stop(e.currentTime+.1),setTimeout(()=>{const i=e.createOscillator(),r=e.createGain();i.connect(r),r.connect(e.destination),i.frequency.setValueAtTime(600,e.currentTime),i.type="sine",r.gain.setValueAtTime(.3,e.currentTime),r.gain.exponentialRampToValueAtTime(.01,e.currentTime+.15),i.start(e.currentTime),i.stop(e.currentTime+.15)},100)}catch(e){console.warn("[Notifications] Could not play notification sound:",e)}}function Ao(){const e=document.querySelector('meta[name="fcm-vapid-key"]');return e?e.getAttribute("content"):window.FCM_VAPID_KEY?window.FCM_VAPID_KEY:null}document.addEventListener("DOMContentLoaded",async()=>{if(!document.querySelector('meta[name="csrf-token"]')){console.log("User not authenticated, skipping Firebase initialization");return}const t=Ao();if(!t){console.warn("FCM VAPID Key not found. Please add it to your .env file and pass it to the view.");return}try{console.log("[Notifications] Initializing Firebase Messaging with options:",{hasVapidKey:!!t,tokenEndpoint:"/api/admin/fcm/token",autoRequestPermission:!0}),await Eo({vapidKey:t,tokenEndpoint:"/api/admin/fcm/token",autoRequestPermission:!0,onMessageCallback:i=>{if(console.log("[Notifications] Foreground notification received:",i),vo(),Notification.permission==="granted"){const r=i.notification||{},o=i.data||{},s=new Notification(r.title||"إشعار جديد",{body:r.body||"",icon:"/images/logo-sm.png",badge:"/images/logo-sm.png",tag:o.notification_id||"notification",data:o});s.onclick=function(a){a.preventDefault(),window.focus(),o.url?window.location.href=o.url:o.notification_id?window.location.href=`/notifications/${o.notification_id}`:window.location.href="/notifications",this.close()},setTimeout(()=>{s.close()},5e3)}window.topbarNotifications&&typeof window.topbarNotifications.updateBadgeCount=="function"&&window.topbarNotifications.updateBadgeCount(),window.location.pathname.includes("/notifications")&&window.dispatchEvent(new CustomEvent("notification-received",{detail:i}))}})?console.log("[Notifications] ✅ Firebase Messaging initialized successfully"):console.error("[Notifications] ❌ Firebase Messaging initialization returned false")}catch(n){console.error("[Notifications] ❌ Error initializing Firebase Messaging:",n),console.error("[Notifications] Error details:",{name:n.name,message:n.message,stack:n.stack})}});
