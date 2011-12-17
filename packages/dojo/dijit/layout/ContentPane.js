/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dijit.layout.ContentPane"]){
dojo._hasResource["dijit.layout.ContentPane"]=true;
dojo.provide("dijit.layout.ContentPane");
dojo.require("dijit._Widget");
dojo.require("dijit.layout._LayoutWidget");
dojo.require("dojo.parser");
dojo.require("dojo.string");
dojo.requireLocalization("dijit","loading",null,"zh-tw,pt,zh,de,ru,hu,sv,cs,gr,es,fr,ko,ROOT,it,ja,pl");
dojo.declare("dijit.layout.ContentPane",dijit._Widget,{href:"",extractContent:false,parseOnLoad:true,preventCache:false,preload:false,refreshOnShow:false,loadingMessage:"<span class='dijitContentPaneLoading'>${loadingState}</span>",errorMessage:"<span class='dijitContentPaneError'>${errorState}</span>",isLoaded:false,"class":"dijitContentPane",doLayout:"auto",postCreate:function(){
this.domNode.title="";
if(!this.containerNode){
this.containerNode=this.domNode;
}
if(this.preload){
this._loadCheck();
}
var _1=dojo.i18n.getLocalization("dijit","loading",this.lang);
this.loadingMessage=dojo.string.substitute(this.loadingMessage,_1);
this.errorMessage=dojo.string.substitute(this.errorMessage,_1);
var _2=dijit.getWaiRole(this.domNode);
if(!_2){
dijit.setWaiRole(this.domNode,"group");
}
dojo.addClass(this.domNode,this["class"]);
},startup:function(){
if(this._started){
return;
}
if(this.doLayout!="false"&&this.doLayout!==false){
this._checkIfSingleChild();
if(this._singleChild){
this._singleChild.startup();
}
}
this._loadCheck();
this.inherited(arguments);
},_checkIfSingleChild:function(){
var _3=dojo.query(">",this.containerNode||this.domNode),_4=_3.filter("[widgetId]");
if(_3.length==1&&_4.length==1){
this.isContainer=true;
this._singleChild=dijit.byNode(_4[0]);
}else{
delete this.isContainer;
delete this._singleChild;
}
},refresh:function(){
return this._prepareLoad(true);
},setHref:function(_5){
this.href=_5;
return this._prepareLoad();
},setContent:function(_6){
if(!this._isDownloaded){
this.href="";
this._onUnloadHandler();
}
this._setContent(_6||"");
this._isDownloaded=false;
if(this.parseOnLoad){
this._createSubWidgets();
}
if(this.doLayout!="false"&&this.doLayout!==false){
this._checkIfSingleChild();
if(this._singleChild&&this._singleChild.resize){
this._singleChild.startup();
this._singleChild.resize(this._contentBox||dojo.contentBox(this.containerNode||this.domNode));
}
}
this._onLoadHandler();
},cancel:function(){
if(this._xhrDfd&&(this._xhrDfd.fired==-1)){
this._xhrDfd.cancel();
}
delete this._xhrDfd;
},destroy:function(){
if(this._beingDestroyed){
return;
}
this._onUnloadHandler();
this._beingDestroyed=true;
this.inherited("destroy",arguments);
},resize:function(_7){
dojo.marginBox(this.domNode,_7);
var _8=this.containerNode||this.domNode,mb=dojo.mixin(dojo.marginBox(_8),_7||{});
this._contentBox=dijit.layout.marginBox2contentBox(_8,mb);
if(this._singleChild&&this._singleChild.resize){
this._singleChild.resize(this._contentBox);
}
},_prepareLoad:function(_a){
this.cancel();
this.isLoaded=false;
this._loadCheck(_a);
},_isShown:function(){
if("open" in this){
return this.open;
}else{
var _b=this.domNode;
return (_b.style.display!="none")&&(_b.style.visibility!="hidden");
}
},_loadCheck:function(_c){
var _d=this._isShown();
if(this.href&&(_c||(this.preload&&!this._xhrDfd)||(this.refreshOnShow&&_d&&!this._xhrDfd)||(!this.isLoaded&&_d&&!this._xhrDfd))){
this._downloadExternalContent();
}
},_downloadExternalContent:function(){
this._onUnloadHandler();
this._setContent(this.onDownloadStart.call(this));
var _e=this;
var _f={preventCache:(this.preventCache||this.refreshOnShow),url:this.href,handleAs:"text"};
if(dojo.isObject(this.ioArgs)){
dojo.mixin(_f,this.ioArgs);
}
var _10=this._xhrDfd=(this.ioMethod||dojo.xhrGet)(_f);
_10.addCallback(function(_11){
try{
_e.onDownloadEnd.call(_e);
_e._isDownloaded=true;
_e.setContent.call(_e,_11);
}
catch(err){
_e._onError.call(_e,"Content",err);
}
delete _e._xhrDfd;
return _11;
});
_10.addErrback(function(err){
if(!_10.cancelled){
_e._onError.call(_e,"Download",err);
}
delete _e._xhrDfd;
return err;
});
},_onLoadHandler:function(){
this.isLoaded=true;
try{
this.onLoad.call(this);
}
catch(e){
console.error("Error "+this.widgetId+" running custom onLoad code");
}
},_onUnloadHandler:function(){
this.isLoaded=false;
this.cancel();
try{
this.onUnload.call(this);
}
catch(e){
console.error("Error "+this.widgetId+" running custom onUnload code");
}
},_setContent:function(_13){
this.destroyDescendants();
try{
var _14=this.containerNode||this.domNode;
while(_14.firstChild){
dojo._destroyElement(_14.firstChild);
}
if(typeof _13=="string"){
if(this.extractContent){
match=_13.match(/<body[^>]*>\s*([\s\S]+)\s*<\/body>/im);
if(match){
_13=match[1];
}
}
_14.innerHTML=_13;
}else{
if(_13.nodeType){
_14.appendChild(_13);
}else{
dojo.forEach(_13,function(n){
_14.appendChild(n.cloneNode(true));
});
}
}
}
catch(e){
var _16=this.onContentError(e);
try{
_14.innerHTML=_16;
}
catch(e){
console.error("Fatal "+this.id+" could not change content due to "+e.message,e);
}
}
},_onError:function(_17,err,_19){
var _1a=this["on"+_17+"Error"].call(this,err);
if(_19){
console.error(_19,err);
}else{
if(_1a){
this._setContent.call(this,_1a);
}
}
},_createSubWidgets:function(){
var _1b=this.containerNode||this.domNode;
try{
dojo.parser.parse(_1b,true);
}
catch(e){
this._onError("Content",e,"Couldn't create widgets in "+this.id+(this.href?" from "+this.href:""));
}
},onLoad:function(e){
},onUnload:function(e){
},onDownloadStart:function(){
return this.loadingMessage;
},onContentError:function(_1e){
},onDownloadError:function(_1f){
return this.errorMessage;
},onDownloadEnd:function(){
}});
}
