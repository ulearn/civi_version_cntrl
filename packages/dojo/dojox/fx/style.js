/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojox.fx.style"]){
dojo._hasResource["dojox.fx.style"]=true;
dojo.provide("dojox.fx.style");
dojo.experimental("dojox.fx.style");
dojo.require("dojox.fx._base");
dojox.fx.addClass=function(_1){
var _2=(_1.node=dojo.byId(_1.node));
var _3=(function(n){
return function(){
dojo.addClass(n,_1.cssClass);
n.style.cssText=_5;
};
})(_2);
var _6=dojox.fx._getCalculatedStyleChanges(_1,true);
var _5=_2.style.cssText;
var _7=dojo.animateProperty(dojo.mixin({properties:_6},_1));
dojo.connect(_7,"onEnd",_7,_3);
return _7;
};
dojox.fx.removeClass=function(_8){
var _9=(_8.node=dojo.byId(_8.node));
var _a=(function(n){
return function(){
dojo.removeClass(n,_8.cssClass);
n.style.cssText=_c;
};
})(_9);
var _d=dojox.fx._getCalculatedStyleChanges(_8,false);
var _c=_9.style.cssText;
var _e=dojo.animateProperty(dojo.mixin({properties:_d},_8));
dojo.connect(_e,"onEnd",_e,_a);
return _e;
};
dojox.fx.toggleClass=function(_f,_10,_11){
if(typeof _11=="undefined"){
_11=!dojo.hasClass(_f,_10);
}
return dojox.fx[(_11?"addClass":"removeClass")]({node:_f,cssClass:_10});
};
dojox.fx._allowedProperties=["width","height","left","top","backgroundColor","color","borderBottomColor","borderBottomWidth","borderTopColor","borderTopWidth","borderLeftColor","borderLeftWidth","borderRightColor","borderRightWidth","paddingLeft","paddingRight","paddingTop","paddingBottom","marginLeft","marginTop","marginRight","marginBottom","lineHeight","letterSpacing","fontSize"];
dojox.fx._getStyleSnapshot=function(_12){
return dojo.map(dojox.fx._allowedProperties,function(_13){
return _12[_13];
});
};
dojox.fx._getCalculatedStyleChanges=function(_14,_15){
var _16=(_14.node=dojo.byId(_14.node));
var cs=dojo.getComputedStyle(_16);
var _18=dojox.fx._getStyleSnapshot(cs);
dojo[(_15?"addClass":"removeClass")](_16,_14.cssClass);
var _19=dojox.fx._getStyleSnapshot(cs);
dojo[(_15?"removeClass":"addClass")](_16,_14.cssClass);
var _1a={};
var i=0;
dojo.forEach(dojox.fx._allowedProperties,function(_1c){
if(_18[i]!=_19[i]){
_1a[_1c]={end:parseInt(_19[i])};
}
i++;
});
return _1a;
};
}
