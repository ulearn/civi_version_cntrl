/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojox.data.KeyValueStore"]){
dojo._hasResource["dojox.data.KeyValueStore"]=true;
dojo.provide("dojox.data.KeyValueStore");
dojo.require("dojo.data.util.filter");
dojo.require("dojo.data.util.simpleFetch");
dojo.declare("dojox.data.KeyValueStore",null,{constructor:function(_1){
if(_1.url){
this.url=_1.url;
}
this._keyValueString=_1.data;
this._keyValueVar=_1.dataVar;
this._keyAttribute="key";
this._valueAttribute="value";
this._storeProp="_keyValueStore";
this._features={"dojo.data.api.Read":true,"dojo.data.api.Identity":true};
this._loadInProgress=false;
this._queuedFetches=[];
},url:"",data:"",_assertIsItem:function(_2){
if(!this.isItem(_2)){
throw new Error("dojox.data.KeyValueStore: a function was passed an item argument that was not an item");
}
},_assertIsAttribute:function(_3,_4){
if(!dojo.isString(_4)){
throw new Error("dojox.data.KeyValueStore: a function was passed an attribute argument that was not an attribute object nor an attribute name string");
}
},getValue:function(_5,_6,_7){
this._assertIsItem(_5);
this._assertIsAttribute(_5,_6);
if(_6==this._keyAttribute){
return _5[this._keyAttribute];
}
return _5[this._valueAttribute];
},getValues:function(_8,_9){
var _a=this.getValue(_8,_9);
return (_a?[_a]:[]);
},getAttributes:function(_b){
return [this._keyAttribute,this._valueAttribute,_b[this._keyAttribute]];
},hasAttribute:function(_c,_d){
this._assertIsItem(_c);
this._assertIsAttribute(_c,_d);
return (_d==this._keyAttribute||_d==this._valueAttribute||_d==_c[this._keyAttribute]);
},containsValue:function(_e,_f,_10){
var _11=undefined;
if(typeof _10==="string"){
_11=dojo.data.util.filter.patternToRegExp(_10,false);
}
return this._containsValue(_e,_f,_10,_11);
},_containsValue:function(_12,_13,_14,_15){
var _16=this.getValues(_12,_13);
for(var i=0;i<_16.length;++i){
var _18=_16[i];
if(typeof _18==="string"&&_15){
return (_18.match(_15)!==null);
}else{
if(_14===_18){
return true;
}
}
}
return false;
},isItem:function(_19){
if(_19&&_19[this._storeProp]===this){
return true;
}
return false;
},isItemLoaded:function(_1a){
return this.isItem(_1a);
},loadItem:function(_1b){
},getFeatures:function(){
return this._features;
},close:function(_1c){
},getLabel:function(_1d){
return _1d[this._keyAttribute];
},getLabelAttributes:function(_1e){
return [this._keyAttribute];
},_fetchItems:function(_1f,_20,_21){
var _22=this;
var _23=function(_24,_25){
var _26=null;
if(_24.query){
_26=[];
var _27=_24.queryOptions?_24.queryOptions.ignoreCase:false;
var _28={};
for(var key in _24.query){
var _2a=_24.query[key];
if(typeof _2a==="string"){
_28[key]=dojo.data.util.filter.patternToRegExp(_2a,_27);
}
}
for(var i=0;i<_25.length;++i){
var _2c=true;
var _2d=_25[i];
for(var key in _24.query){
var _2a=_24.query[key];
if(!_22._containsValue(_2d,key,_2a,_28[key])){
_2c=false;
}
}
if(_2c){
_26.push(_2d);
}
}
}else{
if(_24.identity){
_26=[];
var _2e;
for(var key in _25){
_2e=_25[key];
if(_2e[_22._keyAttribute]==_24.identity){
_26.push(_2e);
break;
}
}
}else{
if(_25.length>0){
_26=_25.slice(0,_25.length);
}
}
}
_20(_26,_24);
};
if(this._loadFinished){
_23(_1f,this._arrayOfAllItems);
}else{
if(this.url!==""){
if(this._loadInProgress){
this._queuedFetches.push({args:_1f,filter:_23});
}else{
this._loadInProgress=true;
var _2f={url:_22.url,handleAs:"json-comment-filtered"};
var _30=dojo.xhrGet(_2f);
_30.addCallback(function(_31){
_22._processData(_31);
_23(_1f,_22._arrayOfAllItems);
_22._handleQueuedFetches();
});
_30.addErrback(function(_32){
_22._loadInProgress=false;
throw _32;
});
}
}else{
if(this._keyValueString){
this._processData(eval(this._keyValueString));
this._keyValueString=null;
_23(_1f,this._arrayOfAllItems);
}else{
if(this._keyValueVar){
this._processData(this._keyValueVar);
this._keyValueVar=null;
_23(_1f,this._arrayOfAllItems);
}else{
throw new Error("dojox.data.KeyValueStore: No source data was provided as either URL, String, or Javascript variable data input.");
}
}
}
}
},_handleQueuedFetches:function(){
if(this._queuedFetches.length>0){
for(var i=0;i<this._queuedFetches.length;i++){
var _34=this._queuedFetches[i];
var _35=_34.filter;
var _36=_34.args;
if(_35){
_35(_36,this._arrayOfAllItems);
}else{
this.fetchItemByIdentity(_34.args);
}
}
this._queuedFetches=[];
}
},_processData:function(_37){
this._arrayOfAllItems=[];
for(var i=0;i<_37.length;i++){
this._arrayOfAllItems.push(this._createItem(_37[i]));
}
this._loadFinished=true;
this._loadInProgress=false;
},_createItem:function(_39){
var _3a={};
_3a[this._storeProp]=this;
for(var i in _39){
_3a[this._keyAttribute]=i;
_3a[this._valueAttribute]=_39[i];
break;
}
return _3a;
},getIdentity:function(_3c){
if(this.isItem(_3c)){
return _3c[this._keyAttribute];
}
return null;
},getIdentityAttributes:function(_3d){
return [this._keyAttribute];
},fetchItemByIdentity:function(_3e){
_3e.oldOnItem=_3e.onItem;
_3e.onItem=null;
_3e.onComplete=this._finishFetchItemByIdentity;
this.fetch(_3e);
},_finishFetchItemByIdentity:function(_3f,_40){
var _41=_40.scope||dojo.global;
if(_3f.length){
_40.oldOnItem.call(_41,_3f[0]);
}else{
_40.oldOnItem.call(_41,null);
}
}});
dojo.extend(dojox.data.KeyValueStore,dojo.data.util.simpleFetch);
}
