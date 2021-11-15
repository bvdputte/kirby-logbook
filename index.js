(()=>{(function(){"use strict";var u=function(){var e=this,s=e.$createElement,t=e._self._c||s;return t("k-inside",[t("k-view",{staticClass:"k-logbook-view"},[t("k-header",[e._v(" "+e._s(e.title)+" ")]),e.logfiles.length>0?t("section",{staticClass:"k-logbook-actions"},[t("k-grid",{attrs:{gutter:"medium"}},[t("k-column",{staticClass:"k-logbook-column-select",attrs:{width:"1/3"}},[t("k-select-field",{attrs:{options:e.logfilesOptions,label:"Logfile",name:"k-logbook__log-select",type:"select",icon:"angle-down"},on:{input:function(i){return e.fetch()}},model:{value:e.selectedLogfile,callback:function(i){e.selectedLogfile=i},expression:"selectedLogfile"}})],1),this.logLevels.length>1?t("k-column",{attrs:{width:"1/3"}},[t("k-select-field",{attrs:{options:e.logLevels,label:"Level",type:"select",icon:"angle-down"},on:{input:function(i){return e.filter()}},model:{value:e.filterLevel,callback:function(i){e.filterLevel=i},expression:"filterLevel"}})],1):e._e(),t("k-column",{attrs:{width:"1/3"}},[t("k-text-field",{attrs:{type:"text",label:"Search",placeholder:"Search",icon:"search"},on:{change:function(i){return e.filter()}},model:{value:e.filterText,callback:function(i){e.filterText=i},expression:"filterText"}})],1),e.logData.length==e.maxLogLines?t("k-column",[t("k-info-field",{attrs:{text:e.maxLogLinesReachedMessage}})],1):e._e()],1)],1):e._e(),this.total>0?t("section",{staticClass:"k-system-view-section"},[t("div",{staticClass:"k-system-info-box k-logbook-pane"},[e.isKirbyLogPluginLog?[t("table",[t("thead",[t("tr",[t("th",{staticClass:"column-timestamp"},[e._v("Timestamp")]),t("th",{staticClass:"column-level"},[e._v("Level")]),t("th",[e._v("Entry")])])]),t("tbody",e._l(e.pagedLogLines,function(i){return t("tr",{key:i},[t("td",{staticClass:"column-timestamp"},[t("pre",[e._v(e._s(i.timestamp))])]),t("td",{staticClass:"column-level"},[t("pre",[e._v(e._s(i.type))])]),t("td",[t("pre",[e._v(e._s(i.content))])])])}),0)])]:[t("ol",e._l(e.pagedLogLines,function(i,a){return t("li",{key:a},[t("pre",[e._v(e._s(i))])])}),0)]],2),t("div",{staticClass:"k-logbook-pane-caption"},[t("k-grid",{attrs:{gutter:"medium"}},[t("k-column",{staticClass:"k-logbook-column-pagination-summary",attrs:{width:"2/3"}},[e._v(" Showing "+e._s(e.pageStart+1)+" to "+e._s(e.pageEnd)+" from "+e._s(e.total)+" log entries ")]),t("k-column",{staticClass:"k-logbook-column-pagination",attrs:{width:"1/3"}},[t("k-pagination",{attrs:{details:!1,page:e.page,limit:e.limit,total:e.total},on:{paginate:e.paginate}})],1)],1)],1)]):e._e(),this.total==0&&e.logfiles.length!=0?t("section",{staticClass:"k-system-view-section"},[t("div",{staticClass:"k-system-info-box"},[t("k-empty",{attrs:{icon:"book"}},[e._v("Logfile is empty")])],1)]):e._e(),e.logfiles.length==0?t("section",{staticClass:"k-system-view-section"},[t("div",{staticClass:"k-system-info-box"},[t("k-empty",{attrs:{icon:"book"}},[e._v("No logfiles found")])],1)]):e._e()],1)],1)},p=[],C="";function d(e,s,t,i,a,c,g,k){var o=typeof e=="function"?e.options:e;s&&(o.render=s,o.staticRenderFns=t,o._compiled=!0),i&&(o.functional=!0),c&&(o._scopeId="data-v-"+c);var l;if(g?(l=function(n){n=n||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext,!n&&typeof __VUE_SSR_CONTEXT__!="undefined"&&(n=__VUE_SSR_CONTEXT__),a&&a.call(this,n),n&&n._registeredComponents&&n._registeredComponents.add(g)},o._ssrRegister=l):a&&(l=k?function(){a.call(this,(o.functional?this.parent:this).$root.$options.shadowRoot)}:a),l)if(o.functional){o._injectStyles=l;var b=o.render;o.render=function(y,h){return l.call(h),b(y,h)}}else{var f=o.beforeCreate;o.beforeCreate=f?[].concat(f,l):[l]}return{exports:e,options:o}}const v={name:"LogBookArea",props:["title","logfiles","selectedLogfile","hasKirbyLogPlugin","maxLogLines","limit"],data(){return{page:1,logData:[],logLines:[],logLevels:[],filterLevel:"",filterText:""}},methods:{fetch:function(){fetch("/kirbylogbook/"+this.selectedLogfile).then(e=>e.json()).then(e=>{this.initializeComponent(e)})},initializeComponent:function(e){if(this.page=1,this.logLevels=[],this.filterLevel="",this.filterText="",Array.isArray(e)){this.logLines=this.logData=e;var s=this.logLines.map(i=>i.type),t=[...new Set(s)];this.logLevels=t.map(i=>({value:i,text:i}))}else this.logLines=this.logData=Object.entries(e).map(i=>i[1])},filter:function(){var e=this.logData;this.filterLevel!=""&&(console.log("level "+this.filterLevel),e=e.filter(s=>s.type==this.filterLevel)),this.filterText!=""&&(console.log("search "+this.filterText),this.isKirbyLogPluginLog?e=e.filter(s=>s.content.includes(this.filterText)):e=e.filter(s=>s.includes(this.filterText))),this.logLines=e},paginate:function({page:e,limit:s}){this.page=e,this.limit=s}},computed:{logfilesOptions:function(){return this.logfiles.map(e=>({value:e,text:e}))},isKirbyLogPluginLog:function(){if(this.logLines[0]==null)return!1;var e=["timestamp","type","content"];return this.hasKirbyLogPlugin&&JSON.stringify(Object.keys(this.logLines[0]))==JSON.stringify(e)},pagedLogLines:function(){return this.logLines.slice(this.pageStart,this.pageEnd)},total:function(){return this.logLines.length},pageStart:function(){return(this.page-1)*this.limit},pageEnd:function(){var e=(this.page-1)*this.limit+this.limit;return e>=this.total?this.total:e},maxLogLinesReachedMessage:function(){return"\u26A0\uFE0F Only last "+this.maxLogLines+" log lines fetched.<br>Increase threshold to fetch more, or tail log on the server."}},created(){this.fetch()}},r={};var m=d(v,u,p,!1,_,null,null,null);function _(e){for(let s in r)this[s]=r[s]}var L=function(){return m.exports}();window.panel.plugin("bvdputte/logbook",{components:{"k-logbook-area":L}})})();})();
