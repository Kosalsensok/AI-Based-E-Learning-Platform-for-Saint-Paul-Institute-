import{J as e,L as t,Ot as n,P as r,jt as i,l as a,s as o,u as s,w as c,z as l}from"./runtime-core.esm-bundler-BHiBOJt4.js";import{Q as u,n as d}from"./service-D1NDTXli.js";import{t as f}from"./ripple-VbLpaTLi.js";import{s as p}from"./focustrap-87xbODR2.js";var m=d.extend({name:`togglebutton`,theme:function(e){var t=e.dt;return`
.p-togglebutton {
    display: inline-flex;
    cursor: pointer;
    user-select: none;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
    color: ${t(`togglebutton.color`)};
    background: ${t(`togglebutton.background`)};
    border: 1px solid ${t(`togglebutton.border.color`)};
    padding: ${t(`togglebutton.padding`)};
    font-size: 1rem;
    font-family: inherit;
    font-feature-settings: inherit;
    transition: background ${t(`togglebutton.transition.duration`)}, color ${t(`togglebutton.transition.duration`)}, border-color ${t(`togglebutton.transition.duration`)},
        outline-color ${t(`togglebutton.transition.duration`)}, box-shadow ${t(`togglebutton.transition.duration`)};
    border-radius: ${t(`togglebutton.border.radius`)};
    outline-color: transparent;
    font-weight: ${t(`togglebutton.font.weight`)};
}

.p-togglebutton-content {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: ${t(`togglebutton.gap`)};
}

.p-togglebutton-label,
.p-togglebutton-icon {
    position: relative;
    transition: none;
}

.p-togglebutton::before {
    content: "";
    background: transparent;
    transition: background ${t(`togglebutton.transition.duration`)}, color ${t(`togglebutton.transition.duration`)}, border-color ${t(`togglebutton.transition.duration`)},
            outline-color ${t(`togglebutton.transition.duration`)}, box-shadow ${t(`togglebutton.transition.duration`)};
    position: absolute;
    inset-inline-start: ${t(`togglebutton.content.left`)};
    inset-block-start: ${t(`togglebutton.content.top`)};
    width: calc(100% - calc(2 * ${t(`togglebutton.content.left`)}));
    height: calc(100% - calc(2 * ${t(`togglebutton.content.top`)}));
    border-radius: ${t(`togglebutton.border.radius`)};
}

.p-togglebutton.p-togglebutton-checked::before {
    background: ${t(`togglebutton.content.checked.background`)};
    box-shadow: ${t(`togglebutton.content.checked.shadow`)};
}

.p-togglebutton:not(:disabled):not(.p-togglebutton-checked):hover {
    background: ${t(`togglebutton.hover.background`)};
    color: ${t(`togglebutton.hover.color`)};
}

.p-togglebutton.p-togglebutton-checked {
    background: ${t(`togglebutton.checked.background`)};
    border-color: ${t(`togglebutton.checked.border.color`)};
    color: ${t(`togglebutton.checked.color`)};
}

.p-togglebutton:focus-visible {
    box-shadow: ${t(`togglebutton.focus.ring.shadow`)};
    outline: ${t(`togglebutton.focus.ring.width`)} ${t(`togglebutton.focus.ring.style`)} ${t(`togglebutton.focus.ring.color`)};
    outline-offset: ${t(`togglebutton.focus.ring.offset`)};
}

.p-togglebutton.p-invalid {
    border-color: ${t(`togglebutton.invalid.border.color`)};
}

.p-togglebutton:disabled {
    opacity: 1;
    cursor: default;
    background: ${t(`togglebutton.disabled.background`)};
    border-color: ${t(`togglebutton.disabled.border.color`)};
    color: ${t(`togglebutton.disabled.color`)};
}

.p-togglebutton-icon {
    color: ${t(`togglebutton.icon.color`)};
}

.p-togglebutton:not(:disabled):not(.p-togglebutton-checked):hover .p-togglebutton-icon {
    color: ${t(`togglebutton.icon.hover.color`)};
}

.p-togglebutton.p-togglebutton-checked .p-togglebutton-icon {
    color: ${t(`togglebutton.icon.checked.color`)};
}

.p-togglebutton:disabled .p-togglebutton-icon {
    color: ${t(`togglebutton.icon.disabled.color`)};
}

.p-togglebutton-sm {
    padding: ${t(`togglebutton.sm.padding`)};
    font-size: ${t(`togglebutton.sm.font.size`)};
}

.p-togglebutton-lg {
    padding: ${t(`togglebutton.lg.padding`)};
    font-size: ${t(`togglebutton.lg.font.size`)};
}
`},classes:{root:function(e){var t=e.instance,n=e.props;return[`p-togglebutton p-component`,{"p-togglebutton-checked":t.active,"p-invalid":t.$invalid,"p-togglebutton-sm p-inputfield-sm":n.size===`small`,"p-togglebutton-lg p-inputfield-lg":n.size===`large`}]},content:`p-togglebutton-content`,icon:`p-togglebutton-icon`,label:`p-togglebutton-label`}}),h={name:`ToggleButton`,extends:{name:`BaseToggleButton`,extends:p,props:{onIcon:String,offIcon:String,onLabel:{type:String,default:`Yes`},offLabel:{type:String,default:`No`},iconPos:{type:String,default:`left`},readonly:{type:Boolean,default:!1},tabindex:{type:Number,default:null},ariaLabelledby:{type:String,default:null},ariaLabel:{type:String,default:null},size:{type:String,default:null}},style:m,provide:function(){return{$pcToggleButton:this,$parentInstance:this}}},inheritAttrs:!1,emits:[`change`],methods:{getPTOptions:function(e){return(e===`root`?this.ptmi:this.ptm)(e,{context:{active:this.active,disabled:this.disabled}})},onChange:function(e){!this.disabled&&!this.readonly&&(this.writeValue(!this.d_value,e),this.$emit(`change`,e))},onBlur:function(e){var t,n;(t=(n=this.formField).onBlur)==null||t.call(n,e)}},computed:{active:function(){return this.d_value===!0},hasLabel:function(){return u(this.onLabel)&&u(this.offLabel)},label:function(){return this.hasLabel?this.d_value?this.onLabel:this.offLabel:`&nbsp;`}},directives:{ripple:f}},g=[`tabindex`,`disabled`,`aria-pressed`,`aria-labelledby`,`data-p-checked`,`data-p-disabled`];function _(u,d,f,p,m,h){var _=l(`ripple`);return e((r(),s(`button`,c({type:`button`,class:u.cx(`root`),tabindex:u.tabindex,disabled:u.disabled,"aria-pressed":u.d_value,onClick:d[0]||=function(){return h.onChange&&h.onChange.apply(h,arguments)},onBlur:d[1]||=function(){return h.onBlur&&h.onBlur.apply(h,arguments)}},h.getPTOptions(`root`),{"aria-labelledby":u.ariaLabelledby,"data-p-checked":h.active,"data-p-disabled":u.disabled}),[o(`span`,c({class:u.cx(`content`)},h.getPTOptions(`content`)),[t(u.$slots,`default`,{},function(){return[t(u.$slots,`icon`,{value:u.d_value,class:n(u.cx(`icon`))},function(){return[u.onIcon||u.offIcon?(r(),s(`span`,c({key:0,class:[u.cx(`icon`),u.d_value?u.onIcon:u.offIcon]},h.getPTOptions(`icon`)),null,16)):a(``,!0)]}),o(`span`,c({class:u.cx(`label`)},h.getPTOptions(`label`)),i(h.label),17)]})],16)],16,g)),[[_]])}h.render=_;export{h as t};