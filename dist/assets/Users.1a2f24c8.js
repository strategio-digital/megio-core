import{_ as B}from"./Layout.vue_vue_type_script_setup_true_lang.ea2d6e90.js";import{f as x,s as H,v as j,x as F,j as R,e as O,r as f,o as n,c as d,b as e,a as u,w as s,m as E,d as A,u as L,y as M,g as V,z as w,F as $,t as k,p as h,h as I}from"./index.3584bddb.js";import"./strategio.04befae5.js";const q=o=>{const p=x(!0),m=x({currentPage:1,lastPage:1}),r=x([]),a=x([]),v=x(!1),_=()=>a.value=v.value?[]:r.value,y=()=>{console.log(a.value)},g=c=>{console.log(c)},i=async()=>{p.value=!0,r.value=[],a.value=[];let c=await R.collections.showAll(o,{currentPage:m.value.currentPage,itemsPerPage:15});if(m.value={currentPage:c.data.currentPage,lastPage:c.data.lastPage},c.data.lastPage<m.value.currentPage)return m.value.currentPage=c.data.lastPage,await i();r.value=c.data.items,p.value=!1};return H(()=>m.value.currentPage,()=>i()),j(()=>{r.value.length!==0&&(v.value=a.value.length===r.value.length)}),F(()=>i()),{refresh:i,selectAll:_,removeOne:g,removeSelected:y,page:m,selectedItems:a,items:r,loading:p,selected:v}},G={class:"d-flex justify-space-between align-center"},J={class:"d-flex ms-3"},K=O({__name:"DatagridHeader",props:{breadcrumbItems:null,refresh:null},setup(o){return(p,m)=>{const r=f("v-breadcrumbs"),a=f("v-btn"),v=f("v-tooltip");return n(),d("div",G,[e(r,{items:o.breadcrumbItems,class:"pa-0",style:{"font-size":"1.4rem"}},null,8,["items"]),u("div",J,[e(v,{location:"top",text:"Obnovit data",offset:"-5"},{activator:s(({props:_})=>[e(a,E(_,{variant:"plain",icon:"mdi-refresh",size:"small",color:"",onClick:o.refresh}),null,16,["onClick"])]),_:1}),e(v,{location:"top",text:"Upravit kolekci",offset:"-5"},{activator:s(({props:_})=>[e(a,E(_,{variant:"plain",icon:"mdi-cog",size:"small",color:""}),null,16)]),_:1}),e(a,{variant:"tonal","prepend-icon":"mdi-plus",class:"ms-3"},{default:s(()=>[A(" P\u0159idat ")]),_:1})])])}}}),Q=o=>{const p=o.date.split(" "),m=p[1].split("."),[r,a,v]=p[0].split("-"),[_,y,g]=m[0].split(":");return`${v}.${a}.${r} ${_}:${y}:${g}`},W=()=>({toCzDateTime:Q}),X={style:{width:"90px"}},Y={class:"d-flex align-center"},Z=u("th",null,"ID",-1),ee=u("th",{class:"text-right"},"Vytvo\u0159eno",-1),te=u("th",{class:"text-right"},"Upraveno",-1),le=u("th",{class:"text-right"},null,-1),ae={key:0},ne=["href"],se=["href"],oe={key:2},re={key:1},ce={class:"text-right"},ie={class:"text-right"},ue={class:"text-right"},de=O({__name:"DatagridTable",props:{columns:null,items:null,removeOne:null,removeSelected:null,selectAll:null,batchActions:null,rowActions:null,routeDetailName:null},setup(o){const p=o,m=L(),{toCzDateTime:r}=W(),a=x([]),v=x(!1),_=i=>m.push({name:p.routeDetailName,params:{id:i}}),y=M(()=>p.items.map(i=>({...i,createdAt:r(i.createdAt),updatedAt:r(i.updatedAt)}))),g=(i,c)=>c[i];return(i,c)=>{const D=f("v-checkbox"),C=f("v-btn"),b=f("v-list-item-title"),P=f("v-list-item"),z=f("v-list"),U=f("v-menu"),N=f("v-chip"),S=f("v-table");return o.items.length?(n(),V(S,{key:0,density:"default",hover:"",class:"mt-5"},{default:s(()=>[u("thead",null,[u("tr",null,[u("th",X,[u("div",Y,[e(D,{onClick:o.selectAll,modelValue:v.value,"onUpdate:modelValue":c[0]||(c[0]=t=>v.value=t),color:"primary",class:"d-flex"},null,8,["onClick","modelValue"]),e(C,{icon:"mdi-dots-vertical",variant:"plain",size:"small",id:"menu-activator",disabled:a.value.length===0},null,8,["disabled"]),e(U,{activator:"#menu-activator"},{default:s(()=>[e(z,null,{default:s(()=>[(n(!0),d($,null,w(o.batchActions,(t,l)=>(n(),V(P,{key:l,value:l},{default:s(()=>[e(b,{onClick:t.handler},{default:s(()=>[A(k(t.title)+" ("+k(a.value.length)+"x) ",1)]),_:2},1032,["onClick"])]),_:2},1032,["value"]))),128)),e(P,{value:"revoke"},{default:s(()=>[e(b,{onClick:o.removeSelected,class:"text-red font-weight-bold"},{default:s(()=>[A(" Trvale odstranit ("+k(a.value.length)+"x) ",1)]),_:1},8,["onClick"])]),_:1})]),_:1})]),_:1})])]),Z,(n(!0),d($,null,w(o.columns,t=>(n(),d("th",{key:t.key,class:"text-left"},k(t.name),1))),128)),ee,te,le])]),u("tbody",null,[(n(!0),d($,null,w(h(y),t=>(n(),d("tr",{key:t.id,class:"text-no-wrap"},[u("td",null,[e(D,{modelValue:a.value,"onUpdate:modelValue":c[1]||(c[1]=l=>a.value=l),value:t,color:"primary",class:"d-flex"},null,8,["modelValue","value"])]),u("td",null,[e(N,{size:"small",color:"",style:{cursor:"pointer"},onClick:l=>_(t.id)},{default:s(()=>[A(k(t.id),1)]),_:2},1032,["onClick"])]),(n(!0),d($,null,w(o.columns,l=>(n(),d("td",{key:l.key},[g(l.key,t)?(n(),d("div",ae,[l.type==="email"?(n(),d("a",{key:0,target:"_blank",href:`mailto:${g(l.key,t)}`},k(g(l.key,t)),9,ne)):l.type==="phone"?(n(),d("a",{key:1,target:"_blank",href:`tel:${g(l.key,t)}`},k(g(l.key,t)),9,se)):(n(),d("span",oe,k(g(l.key,t)),1))])):(n(),d("div",re,"-"))]))),128)),u("td",ce,k(t.updatedAt),1),u("td",ie,k(t.updatedAt),1),u("td",ue,[e(U,null,{activator:s(({props:l})=>[e(C,E({icon:"mdi-dots-vertical"},l,{size:"small",variant:"plain"}),null,16)]),default:s(()=>[e(z,null,{default:s(()=>[(n(!0),d($,null,w(o.rowActions,(l,T)=>(n(),V(P,{key:T,value:T},{default:s(()=>[e(b,{onClick:me=>l.handler(t)},{default:s(()=>[A(k(l.title),1)]),_:2},1032,["onClick"])]),_:2},1032,["value"]))),128)),e(P,{value:"revoke"},{default:s(()=>[e(b,{onClick:l=>o.removeOne(t),class:"text-red font-weight-bold"},{default:s(()=>[A(" Trvale odstranit ")]),_:2},1032,["onClick"])]),_:2},1024)]),_:2},1024)]),_:2},1024),e(C,{icon:"mdi-arrow-right",variant:"plain",size:"small",onClick:l=>_(t.id)},null,8,["onClick"])])]))),128))])]),_:1})):I("",!0)}}}),pe=O({__name:"Users",setup(o){const{refresh:p,loading:m,page:r,items:a,selected:v,selectedItems:_,selectAll:y,removeOne:g,removeSelected:i}=q("user"),c=()=>{console.log(_.value)},D=C=>{console.log(C)};return(C,b)=>{const P=f("v-pagination");return n(),V(B,{loading:h(m)},{default:s(()=>[e(K,{"breadcrumb-items":["U\u017Eivatel\xE9"],refresh:h(p)},null,8,["refresh"]),e(de,{columns:[{name:"Role",key:"role",type:"string"},{name:"E-mail",key:"email",type:"email"}],items:h(a),"remove-one":h(g),"remove-selected":h(i),selected:h(v),"selected-items":h(_),"select-all":h(y),batchActions:[{title:"Odhl\xE1sit u\u017Eivatele",handler:c}],rowActions:[{title:"Odhl\xE1sit u\u017Eivatele",handler:D}],"route-detail-name":"UserDetail"},null,8,["items","remove-one","remove-selected","selected","selected-items","select-all","batchActions","rowActions"]),h(a).length?(n(),V(P,{key:0,modelValue:h(r).currentPage,"onUpdate:modelValue":b[0]||(b[0]=z=>h(r).currentPage=z),length:h(r).lastPage,"total-visible":5,class:"mt-5"},null,8,["modelValue","length"])):I("",!0)]),_:1},8,["loading"])}}});export{pe as default};
