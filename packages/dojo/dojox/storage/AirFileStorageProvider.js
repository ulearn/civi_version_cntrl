/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojox.storage.AirFileStorageProvider"]){
dojo._hasResource["dojox.storage.AirFileStorageProvider"]=true;
dojo.provide("dojox.storage.AirFileStorageProvider");
dojo.require("dojox.storage.manager");
dojo.require("dojox.storage.Provider");
if(dojo.isAIR){
(function(){
if(!_1){
var _1={};
}
_1.File=window.runtime.flash.filesystem.File;
_1.FileStream=window.runtime.flash.filesystem.FileStream;
_1.FileMode=window.runtime.flash.filesystem.FileMode;
dojo.declare("dojox.storage.AirFileStorageProvider",[dojox.storage.Provider],{initialized:false,_storagePath:"__DOJO_STORAGE/",initialize:function(){
this.initialized=false;
try{
var _2=_1.File.applicationStorageDirectory.resolvePath(this._storagePath);
if(!_2.exists){
_2.createDirectory();
}
this.initialized=true;
}
catch(e){
console.debug("dojox.storage.AirFileStorageProvider.initialize:",e);
}
dojox.storage.manager.loaded();
},isAvailable:function(){
return true;
},put:function(_3,_4,_5,_6){
if(this.isValidKey(_3)==false){
throw new Error("Invalid key given: "+_3);
}
_6=_6||this.DEFAULT_NAMESPACE;
if(this.isValidKey(_6)==false){
throw new Error("Invalid namespace given: "+_6);
}
try{
this.remove(_3,_6);
var _7=_1.File.applicationStorageDirectory.resolvePath(this._storagePath+_6);
if(!_7.exists){
_7.createDirectory();
}
var _8=_7.resolvePath(_3);
var _9=new _1.FileStream();
_9.open(_8,_1.FileMode.WRITE);
_9.writeObject(_4);
_9.close();
}
catch(e){
console.debug("dojox.storage.AirFileStorageProvider.put:",e);
_5(this.FAILED,_3,e.toString());
return;
}
if(_5){
_5(this.SUCCESS,_3,null);
}
},get:function(_a,_b){
if(this.isValidKey(_a)==false){
throw new Error("Invalid key given: "+_a);
}
_b=_b||this.DEFAULT_NAMESPACE;
var _c=null;
var _d=_1.File.applicationStorageDirectory.resolvePath(this._storagePath+_b+"/"+_a);
if(_d.exists&&!_d.isDirectory){
var _e=new _1.FileStream();
_e.open(_d,_1.FileMode.READ);
_c=_e.readObject();
_e.close();
}
return _c;
},getNamespaces:function(){
var _f=[this.DEFAULT_NAMESPACE];
var dir=_1.File.applicationStorageDirectory.resolvePath(this._storagePath);
var _11=dir.getDirectoryListing();
for(i=0;i<_11.length;i++){
if(_11[i].isDirectory&&_11[i].name!=this.DEFAULT_NAMESPACE){
_f.push(_11[i].name);
}
}
return _f;
},getKeys:function(_12){
_12=_12||this.DEFAULT_NAMESPACE;
if(this.isValidKey(_12)==false){
throw new Error("Invalid namespace given: "+_12);
}
var _13=[];
var dir=_1.File.applicationStorageDirectory.resolvePath(this._storagePath+_12);
if(dir.exists&&dir.isDirectory){
var _15=dir.getDirectoryListing();
for(i=0;i<_15.length;i++){
_13.push(_15[i].name);
}
}
return _13;
},clear:function(_16){
if(this.isValidKey(_16)==false){
throw new Error("Invalid namespace given: "+_16);
}
var dir=_1.File.applicationStorageDirectory.resolvePath(this._storagePath+_16);
if(dir.exists&&dir.isDirectory){
dir.deleteDirectory(true);
}
},remove:function(key,_19){
_19=_19||this.DEFAULT_NAMESPACE;
var _1a=_1.File.applicationStorageDirectory.resolvePath(this._storagePath+_19+"/"+key);
if(_1a.exists&&!_1a.isDirectory){
_1a.deleteFile();
}
},putMultiple:function(_1b,_1c,_1d,_1e){
if(this.isValidKeyArray(_1b)===false||!_1c instanceof Array||_1b.length!=_1c.length){
throw new Error("Invalid arguments: keys = ["+_1b+"], values = ["+_1c+"]");
}
if(_1e==null||typeof _1e=="undefined"){
_1e=this.DEFAULT_NAMESPACE;
}
if(this.isValidKey(_1e)==false){
throw new Error("Invalid namespace given: "+_1e);
}
this._statusHandler=_1d;
try{
for(var i=0;i<_1b.length;i++){
this.put(_1b[i],value[i],null,_1e);
}
}
catch(e){
console.debug("dojox.storage.AirFileStorageProvider.putMultiple:",e);
if(_1d){
_1d(this.FAILED,_1b,e.toString());
}
return;
}
if(_1d){
_1d(this.SUCCESS,key,null);
}
},getMultiple:function(_20,_21){
if(this.isValidKeyArray(_20)===false){
throw new Error("Invalid key array given: "+_20);
}
if(_21==null||typeof _21=="undefined"){
_21=this.DEFAULT_NAMESPACE;
}
if(this.isValidKey(_21)==false){
throw new Error("Invalid namespace given: "+_21);
}
var _22=[];
for(var i=0;i<_20.length;i++){
_22[i]=this.get(_20[i],_21);
}
return _22;
},removeMultiple:function(_24,_25){
_25=_25||this.DEFAULT_NAMESPACE;
for(var i=0;i<_24.length;i++){
this.remove(_24[i],_25);
}
},isPermanent:function(){
return true;
},getMaximumSize:function(){
return this.SIZE_NO_LIMIT;
},hasSettingsUI:function(){
return false;
},showSettingsUI:function(){
throw new Error(this.declaredClass+" does not support a storage settings user-interface");
},hideSettingsUI:function(){
throw new Error(this.declaredClass+" does not support a storage settings user-interface");
}});
dojox.storage.manager.register("dojox.storage.AirFileStorageProvider",new dojox.storage.AirFileStorageProvider());
dojox.storage.manager.initialize();
})();
}
}
