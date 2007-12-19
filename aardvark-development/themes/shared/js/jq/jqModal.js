/*
 * jqModal - Minimalist Modaling with jQuery
 *
 * Copyright (c) 2007 Brice Burgess <bhb@iceburg.net>, http://www.iceburg.net
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * $Version: ??/??/???? +r12 beta
 * 
 * 
 * AJAX target is now cleared before load; TODO: add clearText/Img?
 * 
 */
(function($) {
$.fn.jqm=function(o){
var _o = {
zIndex: 3000,
overlay: 50,
overlayClass: 'jqmOverlay',
closeClass: 'jqmClose',
trigger: '.jqModal',
ajax: false,
target: false,
modal: false,
toTop: false,
onShow: false,
onHide: false,
onLoad: false
};
return this.each(function(){if(this._jqm)return; s++; this._jqm=s;
H[s]={c:$.extend(_o, o),a:false,w:$(this).addClass('jqmID'+s),s:s};
if(_o.trigger)$(this).jqmAddTrigger(_o.trigger);
});};

$.fn.jqmAddClose=function(e){hs(this,e,'jqmHide'); return this;};
$.fn.jqmAddTrigger=function(e){hs(this,e,'jqmShow'); return this;};
$.fn.jqmShow=function(t){return this.each(function(){if(!H[this._jqm].a)$.jqm.open(this._jqm,t)});};
$.fn.jqmHide=function(t){return this.each(function(){if(H[this._jqm].a)$.jqm.close(this._jqm,t)});};

$.jqm = {
hash:{},
open:function(s,t){var h=H[s],c=h.c,cc='.'+c.closeClass,z=(/^\d+$/.test(h.w.css('z-index')))?h.w.css('z-index'):c.zIndex,o=$('<div></div>').css({height:'100%',width:'100%',position:'fixed',left:0,top:0,'z-index':z-1,opacity:c.overlay/100});h.t=t;h.a=true;h.w.css('z-index',z);
 if(c.modal) {if(!A[0])F('bind');A.push(s);o.css('cursor','wait');}
 else if(c.overlay > 0)h.w.jqmAddClose(o);
 else o=false;

 h.o=(o)?o.addClass(c.overlayClass).prependTo('body'):false;
 if(ie6){$('html,body').css({height:'100%',width:'100%'});if(o){o=o.css({position:'absolute'})[0];for(var y in {Top:1,Left:1})o.style.setExpression(y.toLowerCase(),"(_=(document.documentElement.scroll"+y+" || document.body.scroll"+y+"))+'px'");}}

 if(c.ajax) {var r=c.target||h.w,u=c.ajax,r=(typeof r == 'string')?$(r,h.w):$(r),u=(u.substr(0,1) == '@')?$(t).attr(u.substring(1)):u;
  r.html('').load(u,function(){if(c.onLoad)c.onLoad.call(this,h);if(cc)h.w.jqmAddClose($(cc,h.w));e(h);});}
 else if(cc)h.w.jqmAddClose($(cc,h.w));

 if(c.toTop&&h.o)h.w.before('<span id="jqmP'+h.w[0]._jqm+'"></span>').insertAfter(h.o);	
 (c.onShow)?c.onShow(h):h.w.show();e(h);return false;
},
close:function(s){var h=H[s];h.a=false;
 if(A[0]){A.pop();if(!A[0])F('unbind');}
 if(h.c.toTop&&h.o)$('#jqmP'+h.w[0]._jqm).after(h.w).remove();
 if(h.c.onHide)h.c.onHide(h);else{h.w.hide();if(h.o)h.o.remove();} return false;
}};
var s=0,H=$.jqm.hash,A=[],ie6=$.browser.msie&&($.browser.version == "6.0"),
i=$('<iframe src="javascript:false;document.write(\'\');" class="jqm"></iframe>').css({opacity:0}),
e=function(h){if(ie6)if(h.o)h.o.html('<p style="width:100%;height:100%"/>').prepend(i);else if(!$('iframe.jqm',h.w)[0])h.w.prepend(i); f(h);},
f=function(h){try{$(':input:visible',h.w)[0].focus();}catch(e){}},
F=function(t){$()[t]("keypress",m)[t]("keydown",m)[t]("mousedown",m);},
m=function(e){var h=H[A[A.length-1]],r=(!$(e.target).parents('.jqmID'+h.s)[0]);if(r)f(h);return !r;},
hs=function(w,e,y){var s=[];w.each(function(){s.push(this._jqm)});
 $(e).each(function(){if(this[y])$.extend(this[y],s);else{this[y]=s;$(this).click(function(){for(var i in {jqmShow:1,jqmHide:1})for(var s in this[i])if(H[this[i][s]])H[this[i][s]].w[i](this);return false;});}});};
})(jQuery);



/*
 * jqDnR - Minimalistic Drag'n'Resize for jQuery.
 *
 * Copyright (c) 2007 Brice Burgess <bhb@iceburg.net>, http://www.iceburg.net
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * $Version: 2007.08.19 +r2
 */

(function($){
$.fn.jqDrag=function(h){return i(this,h,'d');};
$.fn.jqResize=function(h){return i(this,h,'r');};
$.jqDnR={dnr:{},e:0,
drag:function(v){
 if(M.k == 'd')E.css({left:M.X+v.pageX-M.pX,top:M.Y+v.pageY-M.pY});
 else E.css({width:Math.max(v.pageX-M.pX+M.W,0),height:Math.max(v.pageY-M.pY+M.H,0)});
  return false;},
stop:function(){E.css('opacity',M.o);$().unbind('mousemove',J.drag).unbind('mouseup',J.stop);}
};
var J=$.jqDnR,M=J.dnr,E=J.e,
i=function(e,h,k){return e.each(function(){h=(h)?$(h,e):e;
 h.bind('mousedown',{e:e,k:k},function(v){var d=v.data,p={};E=d.e;
 // attempt utilization of dimensions plugin to fix IE issues
 if(E.css('position') != 'relative'){try{E.position(p);}catch(e){}}
 M={X:p.left||f('left')||0,Y:p.top||f('top')||0,W:f('width')||E[0].scrollWidth||0,H:f('height')||E[0].scrollHeight||0,pX:v.pageX,pY:v.pageY,k:d.k,o:E.css('opacity')};
 E.css({opacity:0.8});$().mousemove($.jqDnR.drag).mouseup($.jqDnR.stop);
 return false;
 });
});},
f=function(k){return parseInt(E.css(k))||false;};
})(jQuery);



/* Copyright (c) 2007 Paul Bakaus (paul.bakaus@googlemail.com) and Brandon Aaron (brandon.aaron@gmail.com || http://brandonaaron.net)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 * $LastChangedDate$
 * $Rev$
 *
 * Version: 1.1.2
 *
 * Requires: jQuery 1.1.3+
 */

eval(function(p,a,c,k,e,d){e=function(c){return(c<a?"":e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--){d[e(c)]=k[c]||e(c)}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('(b($){j w=$.1j.w,m=$.1j.m;$.1j.J({w:b(){4(!1[0])q();4(1[0]==k)4($.a.R||($.a.B&&N($.a.W)>X))8 f.1c-(($(9).w()>f.1c)?1A():0);g 4($.a.B)8 f.1c;g 8 $.F&&9.T.1K||9.l.1K;4(1[0]==9)8 1F.1U(($.F&&9.T.1R||9.l.1R),9.l.1y);8 w.1V(1,1W)},m:b(){4(!1[0])q();4(1[0]==k)4($.a.R||($.a.B&&N($.a.W)>X))8 f.1d-(($(9).m()>f.1d)?1A():0);g 4($.a.B)8 f.1d;g 8 $.F&&9.T.1S||9.l.1S;4(1[0]==9)4($.a.1I){j e=f.1f;f.1b(2d,f.1h);j 17=f.1f;f.1b(e,f.1h);8 9.l.15+17}g 8 1F.1U((($.F&&!$.a.B)&&9.T.17||9.l.17),9.l.15);8 m.1V(1,1W)},1c:b(){4(!1[0])q();8 1[0]==k||1[0]==9?1.w():1.V(\':L\')?1[0].1y-3(1,\'p\')-3(1,\'1X\'):1.w()+3(1,\'1s\')+3(1,\'1Y\')},1d:b(){4(!1[0])q();8 1[0]==k||1[0]==9?1.m():1.V(\':L\')?1[0].15-3(1,\'n\')-3(1,\'1L\'):1.m()+3(1,\'1q\')+3(1,\'1B\')},2e:b(6){4(!1[0])q();6=$.J({z:v},6||{});8 1[0]==k||1[0]==9?1.w():1.V(\':L\')?1[0].1y+(6.z?(3(1,\'I\')+3(1,\'1Z\')):0):1.w()+3(1,\'p\')+3(1,\'1X\')+3(1,\'1s\')+3(1,\'1Y\')+(6.z?(3(1,\'I\')+3(1,\'1Z\')):0)},20:b(6){4(!1[0])q();6=$.J({z:v},6||{});8 1[0]==k||1[0]==9?1.m():1.V(\':L\')?1[0].15+(6.z?(3(1,\'K\')+3(1,\'1D\')):0):1.m()+3(1,\'n\')+3(1,\'1L\')+3(1,\'1q\')+3(1,\'1B\')+(6.z?(3(1,\'K\')+3(1,\'1D\')):0)},e:b(G){4(!1[0])q();4(G!=1C)8 1.1E(b(){4(1==k||1==9)k.1b(G,$(k).s());g 1.e=G});4(1[0]==k||1[0]==9)8 f.1f||$.F&&9.T.e||9.l.e;8 1[0].e},s:b(G){4(!1[0])q();4(G!=1C)8 1.1E(b(){4(1==k||1==9)k.1b($(k).e(),G);g 1.s=G});4(1[0]==k||1[0]==9)8 f.1h||$.F&&9.T.s||9.l.s;8 1[0].s},10:b(A){8 1.1H({z:v,E:v,u:1.c()},A)},1H:b(6,A){4(!1[0])q();j x=0,y=0,o=0,r=0,7=1[0],5=1[0],M,Y,Z=$.C(7,\'10\'),H=$.a.1I,P=$.a.24,18=$.a.R,1m=$.a.B,O=$.a.B&&N($.a.W)>X,1o=v,1r=v,6=$.J({z:Q,1e:v,1p:v,E:Q,1J:v,u:9.l},6||{});4(6.1J)8 1.1P(6,A);4(6.u.1g)6.u=6.u[0];4(7.D==\'U\'){x=7.11;y=7.13;4(H){x+=3(7,\'K\')+(3(7,\'n\')*2);y+=3(7,\'I\')+(3(7,\'p\')*2)}g 4(18){x+=3(7,\'K\');y+=3(7,\'I\')}g 4((P&&1z.F)){x+=3(7,\'n\');y+=3(7,\'p\')}g 4(O){x+=3(7,\'K\')+3(7,\'n\');y+=3(7,\'I\')+3(7,\'p\')}}g{1a{Y=$.C(5,\'10\');x+=5.11;y+=5.13;4((H&&!5.D.1M(/^t[d|h]$/i))||P||O){x+=3(5,\'n\');y+=3(5,\'p\');4(H&&Y==\'1t\')1o=Q;4(P&&Y==\'26\')1r=Q}M=5.c||9.l;4(6.E||H){1a{4(6.E){o+=5.e;r+=5.s}4(18&&($.C(5,\'27\')||\'\').1M(/28-29|2a/)){o=o-((5.e==5.11)?5.e:0);r=r-((5.s==5.13)?5.s:0)}4(H&&5!=7&&$.C(5,\'1n\')!=\'L\'){x+=3(5,\'n\');y+=3(5,\'p\')}5=5.1Q}12(5!=M)}5=M;4(5==6.u&&!(5.D==\'U\'||5.D==\'1u\')){4(H&&5!=7&&$.C(5,\'1n\')!=\'L\'){x+=3(5,\'n\');y+=3(5,\'p\')}4(((1m&&!O)||18)&&Y!=\'1x\'){x-=3(M,\'n\');y-=3(M,\'p\')}1O}4(5.D==\'U\'||5.D==\'1u\'){4(((1m&&!O)||(P&&$.F))&&Z!=\'1t\'&&Z!=\'1N\'){x+=3(5,\'K\');y+=3(5,\'I\')}4(O||(H&&!1o&&Z!=\'1N\')||(P&&Z==\'1x\'&&!1r)){x+=3(5,\'n\');y+=3(5,\'p\')}1O}}12(5)}j S=1i(7,6,x,y,o,r);4(A){$.J(A,S);8 1}g{8 S}},1P:b(6,A){4(!1[0])q();j x=0,y=0,o=0,r=0,5=1[0],c,6=$.J({z:Q,1e:v,1p:v,E:Q,u:9.l},6||{});4(6.u.1g)6.u=6.u[0];1a{x+=5.11;y+=5.13;c=5.c||9.l;4(6.E){1a{o+=5.e;r+=5.s;5=5.1Q}12(5!=c)}5=c}12(5&&5.D!=\'U\'&&5.D!=\'1u\'&&5!=6.u);j S=1i(1[0],6,x,y,o,r);4(A){$.J(A,S);8 1}g{8 S}},c:b(){4(!1[0])q();j c=1[0].c;12(c&&(c.D!=\'U\'&&$.C(c,\'10\')==\'1x\'))c=c.c;8 $(c)}});j q=b(){2f"2g: 1z 21 V 22"};j 3=b(14,1G){8 N($.C(14.1g?14[0]:14,1G))||0};j 1i=b(7,6,x,y,o,r){4(!6.z){x-=3(7,\'K\');y-=3(7,\'I\')}4(6.1e&&(($.a.B&&N($.a.W)<X)||$.a.R)){x+=3(7,\'n\');y+=3(7,\'p\')}g 4(!6.1e&&!(($.a.B&&N($.a.W)<X)||$.a.R)){x-=3(7,\'n\');y-=3(7,\'p\')}4(6.1p){x+=3(7,\'1q\');y+=3(7,\'1s\')}4(6.E&&(!$.a.R||7.11!=7.e&&7.13!=7.e)){o-=7.e;r-=7.s}8 6.E?{1v:y-r,1w:x-o,s:r,e:o}:{1v:y,1w:x}};j 16=0;j 1A=b(){4(!16){j 1k=$(\'<1l>\').C({m:19,w:19,1n:\'2b\',10:\'1t\',1v:-1T,1w:-1T}).2h(\'l\');16=19-1k.23(\'<1l>\').25(\'1l\').C({m:\'19%\',w:2i}).m();1k.2c()}8 16}})(1z);',62,143,'|this||num|if|parent|options|elem|return|document|browser|function|offsetParent||scrollLeft|self|else|||var|window|body|width|borderLeftWidth|sl|borderTopWidth|error|st|scrollTop||relativeTo|false|height|||margin|returnObject|safari|css|tagName|scroll|boxModel|val|mo|marginTop|extend|marginLeft|visible|op|parseInt|sf3|ie|true|opera|returnValue|documentElement|BODY|is|version|520|parPos|elemPos|position|offsetLeft|while|offsetTop|el|offsetWidth|scrollbarWidth|scrollWidth|oa|100|do|scrollTo|innerHeight|innerWidth|border|pageXOffset|jquery|pageYOffset|handleOffsetReturn|fn|testEl|div|sf|overflow|absparent|padding|paddingLeft|relparent|paddingTop|absolute|HTML|top|left|static|offsetHeight|jQuery|getScrollbarWidth|paddingRight|undefined|marginRight|each|Math|prop|offset|mozilla|lite|clientHeight|borderRightWidth|match|fixed|break|offsetLite|parentNode|scrollHeight|clientWidth|1000|max|apply|arguments|borderBottomWidth|paddingBottom|marginBottom|outerWidth|collection|empty|append|msie|find|relative|display|table|row|inline|auto|remove|99999999|outerHeight|throw|Dimensions|appendTo|200'.split('|'),0,{}))

