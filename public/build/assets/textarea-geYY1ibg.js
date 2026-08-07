import{P as e,u as t,w as n}from"./runtime-core.esm-bundler-BHiBOJt4.js";import{n as r}from"./service-D1NDTXli.js";import{o as i}from"./focustrap-87xbODR2.js";var a=r.extend({name:`textarea`,theme:function(e){var t=e.dt;return`
.p-textarea {
    font-family: inherit;
    font-feature-settings: inherit;
    font-size: 1rem;
    color: ${t(`textarea.color`)};
    background: ${t(`textarea.background`)};
    padding-block: ${t(`textarea.padding.y`)};
    padding-inline: ${t(`textarea.padding.x`)};
    border: 1px solid ${t(`textarea.border.color`)};
    transition: background ${t(`textarea.transition.duration`)}, color ${t(`textarea.transition.duration`)}, border-color ${t(`textarea.transition.duration`)}, outline-color ${t(`textarea.transition.duration`)}, box-shadow ${t(`textarea.transition.duration`)};
    appearance: none;
    border-radius: ${t(`textarea.border.radius`)};
    outline-color: transparent;
    box-shadow: ${t(`textarea.shadow`)};
}

.p-textarea:enabled:hover {
    border-color: ${t(`textarea.hover.border.color`)};
}

.p-textarea:enabled:focus {
    border-color: ${t(`textarea.focus.border.color`)};
    box-shadow: ${t(`textarea.focus.ring.shadow`)};
    outline: ${t(`textarea.focus.ring.width`)} ${t(`textarea.focus.ring.style`)} ${t(`textarea.focus.ring.color`)};
    outline-offset: ${t(`textarea.focus.ring.offset`)};
}

.p-textarea.p-invalid {
    border-color: ${t(`textarea.invalid.border.color`)};
}

.p-textarea.p-variant-filled {
    background: ${t(`textarea.filled.background`)};
}

.p-textarea.p-variant-filled:enabled:focus {
    background: ${t(`textarea.filled.focus.background`)};
}

.p-textarea:disabled {
    opacity: 1;
    background: ${t(`textarea.disabled.background`)};
    color: ${t(`textarea.disabled.color`)};
}

.p-textarea::placeholder {
    color: ${t(`textarea.placeholder.color`)};
}

.p-textarea.p-invalid::placeholder {
    color: ${t(`textarea.invalid.placeholder.color`)};
}

.p-textarea-fluid {
    width: 100%;
}

.p-textarea-resizable {
    overflow: hidden;
    resize: none;
}

.p-textarea-sm {
    font-size: ${t(`textarea.sm.font.size`)};
    padding-block: ${t(`textarea.sm.padding.y`)};
    padding-inline: ${t(`textarea.sm.padding.x`)};
}

.p-textarea-lg {
    font-size: ${t(`textarea.lg.font.size`)};
    padding-block: ${t(`textarea.lg.padding.y`)};
    padding-inline: ${t(`textarea.lg.padding.x`)};
}
`},classes:{root:function(e){var t=e.instance,n=e.props;return[`p-textarea p-component`,{"p-filled":t.$filled,"p-textarea-resizable ":n.autoResize,"p-textarea-sm p-inputfield-sm":n.size===`small`,"p-textarea-lg p-inputfield-lg":n.size===`large`,"p-invalid":t.$invalid,"p-variant-filled":t.$variant===`filled`,"p-textarea-fluid":t.$fluid}]}}}),o={name:`Textarea`,extends:{name:`BaseTextarea`,extends:i,props:{autoResize:Boolean},style:a,provide:function(){return{$pcTextarea:this,$parentInstance:this}}},inheritAttrs:!1,mounted:function(){this.$el.offsetParent&&this.autoResize&&this.resize()},updated:function(){this.$el.offsetParent&&this.autoResize&&this.resize()},methods:{resize:function(){this.$el.style.height=`auto`,this.$el.style.height=this.$el.scrollHeight+`px`,parseFloat(this.$el.style.height)>=parseFloat(this.$el.style.maxHeight)?(this.$el.style.overflowY=`scroll`,this.$el.style.height=this.$el.style.maxHeight):this.$el.style.overflow=`hidden`},onInput:function(e){this.autoResize&&this.resize(),this.writeValue(e.target.value,e)}},computed:{attrs:function(){return n(this.ptmi(`root`,{context:{filled:this.$filled,disabled:this.disabled}}),this.formField)}}},s=[`value`,`disabled`,`aria-invalid`];function c(r,i,a,o,c,l){return e(),t(`textarea`,n({class:r.cx(`root`),value:r.d_value,disabled:r.disabled,"aria-invalid":r.invalid||void 0,onInput:i[0]||=function(){return l.onInput&&l.onInput.apply(l,arguments)}},l.attrs),null,16,s)}o.render=c;export{o as t};