import{L as e,P as t,_ as n,c as r,g as i,h as a,l as o,lt as s,q as c,s as l,u,w as d}from"./runtime-core.esm-bundler-BHiBOJt4.js";import{n as f}from"./service-D1NDTXli.js";import{i as p}from"./ripple-VbLpaTLi.js";import{t as m}from"./AdminLayout-CANnwC8f.js";import{t as h}from"./button-CTMjufjv.js";var g={name:`Card`,extends:{name:`BaseCard`,extends:p,style:f.extend({name:`card`,theme:function(e){var t=e.dt;return`
.p-card {
    background: ${t(`card.background`)};
    color: ${t(`card.color`)};
    box-shadow: ${t(`card.shadow`)};
    border-radius: ${t(`card.border.radius`)};
    display: flex;
    flex-direction: column;
}

.p-card-caption {
    display: flex;
    flex-direction: column;
    gap: ${t(`card.caption.gap`)};
}

.p-card-body {
    padding: ${t(`card.body.padding`)};
    display: flex;
    flex-direction: column;
    gap: ${t(`card.body.gap`)};
}

.p-card-title {
    font-size: ${t(`card.title.font.size`)};
    font-weight: ${t(`card.title.font.weight`)};
}

.p-card-subtitle {
    color: ${t(`card.subtitle.color`)};
}
`},classes:{root:`p-card p-component`,header:`p-card-header`,body:`p-card-body`,caption:`p-card-caption`,title:`p-card-title`,subtitle:`p-card-subtitle`,content:`p-card-content`,footer:`p-card-footer`}}),provide:function(){return{$pcCard:this,$parentInstance:this}}},inheritAttrs:!1};function _(n,r,i,a,s,c){return t(),u(`div`,d({class:n.cx(`root`)},n.ptmi(`root`)),[n.$slots.header?(t(),u(`div`,d({key:0,class:n.cx(`header`)},n.ptm(`header`)),[e(n.$slots,`header`)],16)):o(``,!0),l(`div`,d({class:n.cx(`body`)},n.ptm(`body`)),[n.$slots.title||n.$slots.subtitle?(t(),u(`div`,d({key:0,class:n.cx(`caption`)},n.ptm(`caption`)),[n.$slots.title?(t(),u(`div`,d({key:0,class:n.cx(`title`)},n.ptm(`title`)),[e(n.$slots,`title`)],16)):o(``,!0),n.$slots.subtitle?(t(),u(`div`,d({key:1,class:n.cx(`subtitle`)},n.ptm(`subtitle`)),[e(n.$slots,`subtitle`)],16)):o(``,!0)],16)):o(``,!0),l(`div`,d({class:n.cx(`content`)},n.ptm(`content`)),[e(n.$slots,`content`)],16),n.$slots.footer?(t(),u(`div`,d({key:1,class:n.cx(`footer`)},n.ptm(`footer`)),[e(n.$slots,`footer`)],16)):o(``,!0)],16)],16)}g.render=_;var v={class:`p-6 space-y-6`},y={class:`grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6`},b=[`href`],x=[`href`],S=n({__name:`Index`,setup(e){return(e,n)=>(t(),r(m,{title:`System Reports`},{default:c(()=>[l(`div`,v,[n[8]||=l(`div`,null,[l(`h1`,{class:`text-2xl font-bold`},`System Reports & Exports`),l(`p`,{class:`text-sm text-gray-500`},`Generate analytics and download system data.`)],-1),l(`div`,y,[i(s(g),null,{title:c(()=>[...n[0]||=[a(`Financial Report`,-1)]]),content:c(()=>[n[2]||=l(`p`,{class:`text-sm text-gray-600 mb-4`},`Export all approved payments and revenue.`,-1),l(`a`,{href:e.route(`admin.reports.financials`),class:`p-button p-component p-button-outlined w-full flex justify-center text-primary border-primary`},[...n[1]||=[l(`span`,{class:`pi pi-download mr-2 mt-1`},null,-1),l(`span`,{class:`font-bold`},`Export CSV`,-1)]],8,b)]),_:1}),i(s(g),null,{title:c(()=>[...n[3]||=[a(`Enrollment Data`,-1)]]),content:c(()=>[n[5]||=l(`p`,{class:`text-sm text-gray-600 mb-4`},`Download a complete list of students and enrollments.`,-1),l(`a`,{href:e.route(`admin.reports.enrollments`),class:`p-button p-component p-button-success p-button-outlined w-full flex justify-center text-green-600 border-green-600`},[...n[4]||=[l(`span`,{class:`pi pi-file-excel mr-2 mt-1`},null,-1),l(`span`,{class:`font-bold`},`Export CSV`,-1)]],8,x)]),_:1}),i(s(g),null,{title:c(()=>[...n[6]||=[a(`System Logs`,-1)]]),content:c(()=>[n[7]||=l(`p`,{class:`text-sm text-gray-600 mb-4`},`Export raw system logs for auditing and security review.`,-1),i(s(h),{label:`Download ZIP`,icon:`pi pi-file-zip`,severity:`secondary`,outlined:``,class:`w-full`})]),_:1})])])]),_:1}))}});export{S as default};