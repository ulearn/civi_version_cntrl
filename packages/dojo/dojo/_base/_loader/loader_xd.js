/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojo._base._loader.loader_xd"]){
dojo._hasResource["dojo._base._loader.loader_xd"]=true;
dojo.provide("dojo._base._loader.loader_xd");
dojo._xdReset=function(){
this._isXDomain=dojo.config.useXDomain||false;
this._xdTimer=0;
this._xdInFlight={};
this._xdOrderedReqs=[];
this._xdDepMap={};
this._xdContents=[];
this._xdDefList=[];
};
dojo._xdReset();
dojo._xdCreateResource=function(_1,_2,_3){
var _4=_1.replace(/(\/\*([\s\S]*?)\*\/|\/\/(.*)$)/mg,"");
var _5=[];
var _6=/dojo.(require|requireIf|provide|requireAfterIf|platformRequire|requireLocalization)\(([\w\W]*?)\)/mg;
var _7;
while((_7=_6.exec(_4))!=null){
if(_7[1]=="requireLocalization"){
eval(_7[0]);
}else{
_5.push("\""+_7[1]+"\", "+_7[2]);
}
}
var _8=[];
_8.push(dojo._scopeName+"._xdResourceLoaded({\n");
if(_5.length>0){
_8.push("depends: [");
for(var i=0;i<_5.length;i++){
if(i>0){
_8.push(",\n");
}
_8.push("["+_5[i]+"]");
}
_8.push("],");
}
_8.push("\ndefineResource: function("+dojo._scopePrefixArgs+"){");
if(!dojo.config["debugAtAllCosts"]||_2=="dojo._base._loader.loader_debug"){
_8.push(_1);
}
_8.push("\n}, resourceName: '"+_2+"', resourcePath: '"+_3+"'});");
return _8.join("");
};
dojo._xdIsXDomainPath=function(_a){
var _b=_a.indexOf(":");
var _c=_a.indexOf("/");
if(_b>0&&_b<_c){
return true;
}else{
var _d=this.baseUrl;
_b=_d.indexOf(":");
_c=_d.indexOf("/");
if(_b>0&&_b<_c&&(!location.host||_d.indexOf("http://"+location.host)!=0)){
return true;
}
}
return false;
};
dojo._loadPath=function(_e,_f,cb){
var _11=this._xdIsXDomainPath(_e);
this._isXDomain|=_11;
var uri=this.baseUrl+_e;
if(_11){
var _13=_e.indexOf(":");
var _14=_e.indexOf("/");
if(_13>0&&_13<_14){
uri=_e;
}
}
try{
return ((!_f||this._isXDomain)?this._loadUri(uri,cb,_11,_f):this._loadUriAndCheck(uri,_f,cb));
}
catch(e){
console.debug(e);
return false;
}
};
dojo._loadUri=function(uri,cb,_17,_18){
if(this._loadedUrls[uri]){
return 1;
}
if(this._isXDomain&&_18&&_18!="dojo.i18n"){
this._xdOrderedReqs.push(_18);
if(_17||uri.indexOf("/nls/")==-1){
this._xdInFlight[_18]=true;
this._inFlightCount++;
}
if(!this._xdTimer){
if(dojo.isAIR){
this._xdTimer=setInterval(function(){
dojo._xdWatchInFlight();
},100);
}else{
this._xdTimer=setInterval(dojo._scopeName+"._xdWatchInFlight();",100);
}
}
this._xdStartTime=(new Date()).getTime();
}
if(_17){
var _19=uri.lastIndexOf(".");
if(_19<=0){
_19=uri.length-1;
}
var _1a=uri.substring(0,_19)+".xd";
if(_19!=uri.length-1){
_1a+=uri.substring(_19,uri.length);
}
if(dojo.isAIR){
_1a=_1a.replace("app:/","/");
}
var _1b=document.createElement("script");
_1b.type="text/javascript";
_1b.src=_1a;
if(!this.headElement){
this._headElement=document.getElementsByTagName("head")[0];
if(!this._headElement){
this._headElement=document.getElementsByTagName("html")[0];
}
}
this._headElement.appendChild(_1b);
}else{
var _1c=this._getText(uri,null,true);
if(_1c==null){
return 0;
}
if(this._isXDomain&&uri.indexOf("/nls/")==-1&&_18!="dojo.i18n"){
var res=this._xdCreateResource(_1c,_18,uri);
dojo.eval(res);
}else{
if(cb){
_1c="("+_1c+")";
}else{
_1c=this._scopePrefix+_1c+this._scopeSuffix;
}
var _1e=dojo["eval"](_1c+"\r\n//@ sourceURL="+uri);
if(cb){
cb(_1e);
}
}
}
this._loadedUrls[uri]=true;
this._loadedUrls.push(uri);
return true;
};
dojo._xdResourceLoaded=function(res){
var _20=res.depends;
var _21=null;
var _22=null;
var _23=[];
if(_20&&_20.length>0){
var dep=null;
var _25=0;
var _26=false;
for(var i=0;i<_20.length;i++){
dep=_20[i];
if(dep[0]=="provide"){
_23.push(dep[1]);
}else{
if(!_21){
_21=[];
}
if(!_22){
_22=[];
}
var _28=this._xdUnpackDependency(dep);
if(_28.requires){
_21=_21.concat(_28.requires);
}
if(_28.requiresAfter){
_22=_22.concat(_28.requiresAfter);
}
}
var _29=dep[0];
var _2a=_29.split(".");
if(_2a.length==2){
dojo[_2a[0]][_2a[1]].apply(dojo[_2a[0]],dep.slice(1));
}else{
dojo[_29].apply(dojo,dep.slice(1));
}
}
if(_23.length==1&&_23[0]=="dojo._base._loader.loader_debug"){
res.defineResource(dojo);
}else{
var _2b=this._xdContents.push({content:res.defineResource,resourceName:res["resourceName"],resourcePath:res["resourcePath"],isDefined:false})-1;
for(var i=0;i<_23.length;i++){
this._xdDepMap[_23[i]]={requires:_21,requiresAfter:_22,contentIndex:_2b};
}
}
for(var i=0;i<_23.length;i++){
this._xdInFlight[_23[i]]=false;
}
}
};
dojo._xdLoadFlattenedBundle=function(_2c,_2d,_2e,_2f){
_2e=_2e||"root";
var _30=dojo.i18n.normalizeLocale(_2e).replace("-","_");
var _31=[_2c,"nls",_2d].join(".");
var _32=dojo["provide"](_31);
_32[_30]=_2f;
var _33=[_2c,_30,_2d].join(".");
var _34=dojo._xdBundleMap[_33];
if(_34){
for(var _35 in _34){
_32[_35]=_2f;
}
}
};
dojo._xdInitExtraLocales=function(){
var _36=dojo.config.extraLocale;
if(_36){
if(!_36 instanceof Array){
_36=[_36];
}
dojo._xdReqLoc=dojo.xdRequireLocalization;
dojo.xdRequireLocalization=function(m,b,_39,_3a){
dojo._xdReqLoc(m,b,_39,_3a);
if(_39){
return;
}
for(var i=0;i<_36.length;i++){
dojo._xdReqLoc(m,b,_36[i],_3a);
}
};
}
};
dojo._xdBundleMap={};
dojo.xdRequireLocalization=function(_3c,_3d,_3e,_3f){
if(dojo._xdInitExtraLocales){
dojo._xdInitExtraLocales();
dojo._xdInitExtraLocales=null;
dojo.xdRequireLocalization.apply(dojo,arguments);
return;
}
var _40=_3f.split(",");
var _41=dojo.i18n.normalizeLocale(_3e);
var _42="";
for(var i=0;i<_40.length;i++){
if(_41.indexOf(_40[i])==0){
if(_40[i].length>_42.length){
_42=_40[i];
}
}
}
var _44=_42.replace("-","_");
var _45=dojo.getObject([_3c,"nls",_3d].join("."));
if(_45&&_45[_44]){
bundle[_41.replace("-","_")]=_45[_44];
}else{
var _46=[_3c,(_44||"root"),_3d].join(".");
var _47=dojo._xdBundleMap[_46];
if(!_47){
_47=dojo._xdBundleMap[_46]={};
}
_47[_41.replace("-","_")]=true;
dojo.require(_3c+".nls"+(_42?"."+_42:"")+"."+_3d);
}
};
dojo._xdRealRequireLocalization=dojo.requireLocalization;
dojo.requireLocalization=function(_48,_49,_4a,_4b){
var _4c=this.moduleUrl(_48).toString();
if(this._xdIsXDomainPath(_4c)){
return dojo.xdRequireLocalization.apply(dojo,arguments);
}else{
return dojo._xdRealRequireLocalization.apply(dojo,arguments);
}
};
dojo._xdUnpackDependency=function(dep){
var _4e=null;
var _4f=null;
switch(dep[0]){
case "requireIf":
case "requireAfterIf":
if(dep[1]===true){
_4e=[{name:dep[2],content:null}];
}
break;
case "platformRequire":
var _50=dep[1];
var _51=_50["common"]||[];
var _4e=(_50[dojo.hostenv.name_])?_51.concat(_50[dojo.hostenv.name_]||[]):_51.concat(_50["default"]||[]);
if(_4e){
for(var i=0;i<_4e.length;i++){
if(_4e[i] instanceof Array){
_4e[i]={name:_4e[i][0],content:null};
}else{
_4e[i]={name:_4e[i],content:null};
}
}
}
break;
case "require":
_4e=[{name:dep[1],content:null}];
break;
case "i18n._preloadLocalizations":
dojo.i18n._preloadLocalizations.apply(dojo.i18n._preloadLocalizations,dep.slice(1));
break;
}
if(dep[0]=="requireAfterIf"||dep[0]=="requireIf"){
_4f=_4e;
_4e=null;
}
return {requires:_4e,requiresAfter:_4f};
};
dojo._xdWalkReqs=function(){
var _53=null;
var req;
for(var i=0;i<this._xdOrderedReqs.length;i++){
req=this._xdOrderedReqs[i];
if(this._xdDepMap[req]){
_53=[req];
_53[req]=true;
this._xdEvalReqs(_53);
}
}
};
dojo._xdEvalReqs=function(_56){
while(_56.length>0){
var req=_56[_56.length-1];
var res=this._xdDepMap[req];
if(res){
var _59=res.requires;
if(_59&&_59.length>0){
var _5a;
for(var i=0;i<_59.length;i++){
_5a=_59[i].name;
if(_5a&&!_56[_5a]){
_56.push(_5a);
_56[_5a]=true;
this._xdEvalReqs(_56);
}
}
}
var _5c=this._xdContents[res.contentIndex];
if(!_5c.isDefined){
var _5d=_5c.content;
_5d["resourceName"]=_5c["resourceName"];
_5d["resourcePath"]=_5c["resourcePath"];
this._xdDefList.push(_5d);
_5c.isDefined=true;
}
this._xdDepMap[req]=null;
var _59=res.requiresAfter;
if(_59&&_59.length>0){
var _5a;
for(var i=0;i<_59.length;i++){
_5a=_59[i].name;
if(_5a&&!_56[_5a]){
_56.push(_5a);
_56[_5a]=true;
this._xdEvalReqs(_56);
}
}
}
}
_56.pop();
}
};
dojo._xdClearInterval=function(){
clearInterval(this._xdTimer);
this._xdTimer=0;
};
dojo._xdWatchInFlight=function(){
var _5e="";
var _5f=(dojo.config.xdWaitSeconds||15)*1000;
var _60=(this._xdStartTime+_5f)<(new Date()).getTime();
for(var _61 in this._xdInFlight){
if(this._xdInFlight[_61]===true){
if(_60){
_5e+=_61+" ";
}else{
return;
}
}
}
this._xdClearInterval();
if(_60){
throw "Could not load cross-domain resources: "+_5e;
}
this._xdWalkReqs();
var _62=this._xdDefList.length;
for(var i=0;i<_62;i++){
var _64=dojo._xdDefList[i];
if(dojo.config["debugAtAllCosts"]&&_64["resourceName"]){
if(!this["_xdDebugQueue"]){
this._xdDebugQueue=[];
}
this._xdDebugQueue.push({resourceName:_64.resourceName,resourcePath:_64.resourcePath});
}else{
_64.apply(dojo.global,dojo._scopeArgs);
}
}
for(var i=0;i<this._xdContents.length;i++){
var _65=this._xdContents[i];
if(_65.content&&!_65.isDefined){
_65.content.apply(dojo.global,dojo._scopeArgs);
}
}
this._xdReset();
if(this["_xdDebugQueue"]&&this._xdDebugQueue.length>0){
this._xdDebugFileLoaded();
}else{
this._xdNotifyLoaded();
}
};
dojo._xdNotifyLoaded=function(){
this._inFlightCount=0;
if(this._initFired&&!this._loadNotifying){
this._callLoaded();
}
};
}
