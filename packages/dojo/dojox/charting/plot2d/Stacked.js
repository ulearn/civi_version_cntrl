/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojox.charting.plot2d.Stacked"]){
dojo._hasResource["dojox.charting.plot2d.Stacked"]=true;
dojo.provide("dojox.charting.plot2d.Stacked");
dojo.require("dojox.charting.plot2d.common");
dojo.require("dojox.charting.plot2d.Default");
dojo.require("dojox.lang.functional");
dojo.require("dojox.lang.functional.sequence");
dojo.require("dojox.lang.functional.reversed");
(function(){
var df=dojox.lang.functional,dc=dojox.charting.plot2d.common,_3=df.lambda("item.purgeGroup()");
dojo.declare("dojox.charting.plot2d.Stacked",dojox.charting.plot2d.Default,{calculateAxes:function(_4){
var _5=dc.collectStackedStats(this.series);
this._maxRunLength=_5.hmax;
this._calc(_4,_5);
return this;
},render:function(_6,_7){
var _8=df.repeat(this._maxRunLength,"-> 0",0);
for(var i=0;i<this.series.length;++i){
var _a=this.series[i];
for(var j=0;j<_a.data.length;++j){
var v=_a.data[j];
if(isNaN(v)){
v=0;
}
_8[j]+=v;
}
}
if(this.dirty){
dojo.forEach(this.series,_3);
this.cleanGroup();
var s=this.group;
df.forEachRev(this.series,function(_e){
_e.cleanGroup(s);
});
}
function curve(_f,_10){
var p=dojo.map(_f,function(_12,i){
if(i==0){
return "M"+_12.x+","+_12.y;
}
var dx=_12.x-_f[i-1].x,dy=_f[i-1].y;
return "C"+(_12.x-(_10-1)*(dx/_10))+","+dy+" "+(_12.x-(dx/_10))+","+_12.y+" "+_12.x+","+_12.y;
});
return p.join(" ");
};
var t=this.chart.theme,_17,_18,_19,_1a;
for(var i=this.series.length-1;i>=0;--i){
var _a=this.series[i];
if(!this.dirty&&!_a.dirty){
continue;
}
_a.cleanGroup();
var s=_a.group,_1b=dojo.map(_8,function(v,i){
return {x:this._hScaler.scale*(i+1-this._hScaler.bounds.lower)+_7.l,y:_6.height-_7.b-this._vScaler.scale*(v-this._vScaler.bounds.lower)};
},this);
if(!_a.fill||!_a.stroke){
_19=new dojo.Color(t.next("color"));
}
var _1e="";
if(this.opt.tension){
_1e=curve(_1b,this.opt.tension);
}
if(this.opt.areas){
var _1f=dojo.clone(_1b);
var _20=_a.fill?_a.fill:dc.augmentFill(t.series.fill,_19);
if(this.opt.tension){
var p=curve(_1f,this.opt.tension);
p+=" L"+_1b[_1b.length-1].x+","+(_6.height-_7.b)+" "+"L"+_1b[0].x+","+(_6.height-_7.b)+" "+"L"+_1b[0].x+","+_1b[0].y;
s.createPath(p).setFill(_20);
}else{
_1f.push({x:_1b[_1b.length-1].x,y:_6.height-_7.b});
_1f.push({x:_1b[0].x,y:_6.height-_7.b});
_1f.push(_1b[0]);
s.createPolyline(_1f).setFill(_20);
}
}
if(this.opt.lines||this.opt.markers){
_17=_a.stroke?dc.makeStroke(_a.stroke):dc.augmentStroke(t.series.stroke,_19);
if(_a.outline||t.series.outline){
_18=dc.makeStroke(_a.outline?_a.outline:t.series.outline);
_18.width=2*_18.width+_17.width;
}
}
if(this.opt.markers){
_1a=_a.marker?_a.marker:t.next("marker");
}
if(this.opt.shadows&&_17){
var sh=this.opt.shadows,_23=new dojo.Color([0,0,0,0.3]),_24=dojo.map(_1b,function(c){
return {x:c.x+sh.dx,y:c.y+sh.dy};
}),_26=dojo.clone(_18?_18:_17);
_26.color=_23;
_26.width+=sh.dw?sh.dw:0;
if(this.opt.lines){
if(this.opt.tension){
s.createPath(curve(_24,this.opt.tension)).setStroke(_26);
}else{
s.createPolyline(_24).setStroke(_26);
}
}
if(this.opt.markers){
dojo.forEach(_24,function(c){
s.createPath("M"+c.x+" "+c.y+" "+_1a).setStroke(_26).setFill(_23);
},this);
}
}
if(this.opt.lines){
if(_18){
if(this.opt.tension){
s.createPath(_1e).setStroke(_18);
}else{
s.createPolyline(_1b).setStroke(_18);
}
}
if(this.opt.tension){
s.createPath(_1e).setStroke(_17);
}else{
s.createPolyline(_1b).setStroke(_17);
}
}
if(this.opt.markers){
dojo.forEach(_1b,function(c){
var _29="M"+c.x+" "+c.y+" "+_1a;
if(_18){
s.createPath(_29).setStroke(_18);
}
s.createPath(_29).setStroke(_17).setFill(_17.color);
},this);
}
_a.dirty=false;
for(var j=0;j<_a.data.length;++j){
var v=_a.data[j];
if(isNaN(v)){
v=0;
}
_8[j]-=v;
}
}
this.dirty=false;
return this;
}});
})();
}
