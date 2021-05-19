var shatter_paths = [];

function shatter(event) {
    event.preventDefault();
    var id = $(this).attr('id');
    var url = $(this).attr('href');
    var width = $(".service_wrapper>img").width();
    var height = $(".service_wrapper>img").height();
    var $canvas = $('#'+id+' canvas');
    $canvas.each(function() {
        this.height = height;
        this.width = width;
    });
    var pos = $('#'+id).offset(),
        x = event.pageX - pos.left,
        y = event.pageY - pos.top;
    currentCenter = { x: x, y: y };
    var $image = $('#'+id+' img');
    if (options = shatter_validate()) {
        options.height = height;
        options.width = width;
        options.center = currentCenter;
        if  (typeof shatter_paths[id] === 'undefined') {
            shatter_paths[id] = [];
        }
        shatter_paths[id] = shatter_paths[id].concat(findCrackEffectPaths(options));
        renderCrackEffectAll($canvas, $image, shatter_paths[id], options);
        setTimeout(function() {window.location.href = url;}, 500);
    }
}

function shatter_validate()
{var f=$('.c-field:not([fieldtype=none])'),b,g,pos,o={};f.each(function(){var $el=$(this),val=null;switch($el.attr('fieldtype')){case 'spinner':val=+$el.find('input')[0].value;break;case 'slider':val=$el.slider('value');break}
b=$el.attr('databind');if(b.indexOf('.')>0){g=b.split('.')[0];b=b.split('.')[1];o[g]=o[g]||{};o[g][b]=val}else{o[b]=val}});return o}
function findCrackEffectPaths(options)
{var imx=0,imy=0,imw=options.width,imh=options.height,ctx,main=[[]],lines=[],level=1,maxl=0,r=15,c=options.center,pt1,pt2,ang,num,num2;num=20;ang=360/(num+1);while(main[0].length<num)
{num2=(ang*main[0].length)+10;pt2=findPointOnCircle(c,5,num2);main[0].push({angle:num2,point:pt2})}
while(r<500)
{main[level]=[];for(num2=0;num2<num;num2++)
{pt1=main[level-1][num2];main[level][num2]=null;if(pt1)
{if((pt1.point.x>imx&&pt1.point.x<imw)&&(pt1.point.y>imy&&pt1.point.y<imh))
{ang=pt1.angle+Math.random()*10/num-10/2/num;if(ang>350)ang=350;pt1=pt1.point;pt2=findPointOnCircle(c,r+Math.random()*r/level-r/(level*2),ang);main[level][num2]={angle:ang,point:{x:pt2.x,y:pt2.y}}}
else if(maxl==0)
{maxl=level}}}
level++;r*=Math.random()*1.5+(1.5-50/100)}
if(maxl==0)maxl=level;var l=1,g=0;for(;l<level;l++)
{for(g=0;g<num;g++)
{pt1=main[l-1][g];pt2=main[l][g];if(pt1&&pt2)
{lines.push({p1:{x:pt1.point.x,y:pt1.point.y},p2:{x:pt2.point.x,y:pt2.point.y},desc:describeLinePath(pt1.point,pt2.point,30/100),level:l});if(Math.random()<(60/100))
{pt1=main[l][(g+1)%num];if(pt1)
{lines.push({p1:{x:pt2.point.x,y:pt2.point.y},p2:{x:pt1.point.x,y:pt1.point.y},desc:describeLinePath(pt2.point,pt1.point,30/100),level:l})}}
if(l<level-1&&Math.random()<(30/100))
{pt1=main[l+1][(g+1)%num];if(pt1)
{lines.push({p1:{x:pt2.point.x,y:pt2.point.y},p2:{x:pt1.point.x,y:pt1.point.y},desc:describeLinePath(pt2.point,pt1.point,30/100),level:l})}}}}}
return lines}
function renderCrackEffectRefract(cvs,img,p1,p2,line)
{var ctx=cvs.getContext('2d'),tx=line.tx,ty=line.ty,cp=line.cpt,ns=3,td=6,x1=line.bbx1,y1=line.bby1,w=line.bbwidth+ns*2,h=line.bbheight+ns*2;if(80===0){return}
ctx.globalAlpha=80||1;ctx.save();ctx.beginPath();ctx.moveTo(p1.x+ns*tx,p1.y+ns*ty);ctx.quadraticCurveTo(cp.x,cp.y,p2.x+ns*tx,p2.y+ns*ty);ctx.lineTo(p2.x-ns*tx,p2.y-ns*ty);ctx.quadraticCurveTo(cp.x,cp.y,p1.x-ns*tx,p1.y-ns*ty);ctx.closePath();ctx.clip();try
{if(x1+td*tx<0){x1=-td*tx}
if(y1+td*ty<0){y1=-td*ty}
if(w+x1+td*tx>ctx.canvas.window.innerWidth){w=ctx.canvas.window.innerWidth-x1+td*tx}
if(h+y1+td*ty>ctx.canvas.window.innerHeight){h=ctx.canvas.window.innerHeight-y1+td*ty}
ctx.drawImage(img,x1+td*tx,y1+td*ty,w,h,x1,y1,w,h)}
catch(e)
{}
ctx.restore()}
function renderCrackEffectReflect(cvs,img,p1,p2,line,options)
{var ctx=cvs.getContext('2d'),tx=line.tx,ty=line.ty,cp=line.cpt,dd=line.dl/3,grd,clr=jQuery.Color('rgb(255,255,255)');if(0.3===0)return;ctx.globalAlpha=0.3||1;try
{grd=ctx.createLinearGradient(p1.x+dd*tx,p1.y+dd*ty,p1.x-dd*tx,p1.y-dd*ty)}
catch(e)
{console.log('x1:'+(p1.x+dd*tx)+',y1:'+(p1.y+dd*ty)+',x2:'+(p1.x-dd*tx)+',y2:'+(p1.y-dd*ty))}
grd.addColorStop(0,clr.alpha(0).toRgbaString());grd.addColorStop(0.5,clr.alpha(0.5).toRgbaString());grd.addColorStop(1,clr.alpha(0).toRgbaString());ctx.fillStyle=grd;ctx.beginPath();ctx.moveTo(p1.x+dd*tx,p1.y+dd*ty);ctx.lineTo(p2.x+dd*tx,p2.y+dd*ty);ctx.lineTo(p2.x-dd*tx,p2.y-dd*ty);ctx.lineTo(p1.x-dd*tx,p1.y-dd*ty);ctx.closePath();ctx.fill()}
function renderCrackEffectFractures(cvs,img,p1,p2,line,options)
{var ctx=cvs.getContext('2d'),tx=line.tx,ty=line.ty,sx=line.sx,sy=line.sy,sz=33,dl=line.dl,mp=dl/2,mpp=line.mpp,cma=line.cma,mpl1=line.mpl1,mpl2=line.mpl2,s,p,c,w,h1,h2,t,clr=jQuery.Color('rgb(255,255,255');if(0.4===0)return;ctx.globalAlpha=0.4||1;ctx.lineWidth=1;for(s=0;s<dl;s++)
{if(s<mpp*dl)
c=cma*(1-Math.pow((mpl1-s)/mpl1,2));else c=cma*(1-Math.pow((mpl2-(dl-s))/mpl2,2));c/=2;p=Math.pow((s>mp?dl-s:s)/mp,2);w=Math.random()*1+1;h1=sz-Math.random()*p*sz+1;h2=sz-Math.random()*p*sz+1;t=Math.random()*20-10;if(Math.random()>p-sz/mp)
{ctx.fillStyle=clr.alpha(Math.round(Math.random()*8+4)/12).toRgbaString();ctx.beginPath();ctx.moveTo(p1.x+s*sx+c*tx,p1.y+s*sy+c*ty);ctx.lineTo(p1.x+(t+s+w/2)*sx+h1*tx+c*tx,p1.y+(-t+s+w/2)*sy+h1*ty+c*ty);ctx.lineTo(p1.x+(s+w)*sx+c*tx,p1.y+(s+w)*sy+c*ty);ctx.lineTo(p1.x+(-t+s+w/2)*sx-h2*tx+c*tx,p1.y+(t+s+w/2)*sy-h2*ty+c*ty);ctx.closePath();ctx.fill()}
s+=mp*(p/2+0.5)}}
function renderCrackEffectMainLine(cvs,img,p1,p2,line,options)
{var ctx=cvs.getContext('2d'),tx=line.tx,ty=line.ty,cp=line.cpt,ns=0.03||1,st=0.14||1,hl=0.2||0,tt=Math.random()*(ns*2)-(ns*2)/2,clr=jQuery.Color('rgb(255,255,255)'),nn=clr.lightness();if(65===0)return;ctx.globalAlpha=65||1;ctx.lineWidth=1;while(st>0)
{ctx.strokeStyle=clr.lightness((nn>0.5?nn:(1-nn))*(1-hl*Math.random())).alpha(Math.round(Math.random()*8+4)/12).toRgbaString();ctx.beginPath();ctx.moveTo(p1.x+(st+tt)*tx,p1.y+(st-tt)*ty);ctx.quadraticCurveTo(cp.x,cp.y,p2.x+(st-tt)*tx,p2.y+(st+tt)*ty);ctx.stroke();st--}}
function renderCrackEffectNoise(cvs,img,p1,p2,line,options)
{var ctx=cvs.getContext('2d'),tx=line.tx,ty=line.ty,sx=line.sx,sy=line.sy,freq=0.4,dl=line.dl,mp=dl/2,mpp=line.mpp,cma=line.cma,mpl1=line.mpl1,mpl2=line.mpl2,dd=dl/3,step=Math.ceil(dd*(1-(freq+0.5)/1.5)+1),c,t,s,pos,cnt,m,clr=jQuery.Color('rgb(255,255,255)');if(1===0)return;ctx.globalAlpha=1||1;ctx.lineWidth=1;for(s=0;s<dl;s++)
{if(s<mpp*dl)
c=cma*(1-Math.pow((mpl1-s)/mpl1,2));else c=cma*(1-Math.pow((mpl2-(dl-s))/mpl2,2));c/=2;for(t=-dd;t<dd;t++)
{if(Math.random()>Math.abs(t)/dd)
{cnt=Math.floor(Math.random()*4+0.5);m=Math.random()*2-1;while(cnt>=0)
{ctx.strokeStyle=clr.alpha(Math.round(Math.random()*10+2)/30).toRgbaString();pos=Math.floor(Math.random()*5+0.5);ctx.beginPath();ctx.moveTo(p1.x+(s-pos)*sx+(m+t)*tx+c*tx,p1.y+(s-pos)*sy+(-m+t)*ty+c*ty);ctx.lineTo(p1.x+(s+pos)*sx+(-m+t)*tx+c*tx,p1.y+(s+pos)*sy+(m+t)*ty+c*ty);ctx.stroke();cnt--;pos++}}
t+=Math.random()*step*2}
s+=Math.random()*step*4}}
function renderCrackEffectAll($canvas,$image,paths,options)
{var i,line,len=paths.length;for(i=0;i<len;i++){line=paths[i];renderCrackEffectRefract($canvas[0],$image[0],line.p1,line.p2,line.desc);renderCrackEffectReflect($canvas[1],$image[0],line.p1,line.p2,line.desc);renderCrackEffectFractures($canvas[2],$image[0],line.p1,line.p2,line.desc);renderCrackEffectMainLine($canvas[3],$image[0],line.p1,line.p2,line.desc);renderCrackEffectNoise($canvas[4],$image[0],line.p1,line.p2,line.desc)}}
var _RAD=Math.PI/180;function findPointOnCircle(c,r,a)
{return{x:c.x+r*Math.cos(a*_RAD)-r*Math.sin(a*_RAD),y:c.y+r*Math.sin(a*_RAD)+r*Math.cos(a*_RAD)}}
function describeLinePath(p1,p2,cv)
{var o={},ll,cv=5*cv;o.dx=(p2.x-p1.x);o.dy=(p2.y-p1.y);o.dl=Math.sqrt(o.dx*o.dx+o.dy*o.dy);o.sx=o.dx/o.dl;o.sy=o.dy/o.dl;o.tx=o.dy/o.dl;o.ty=-o.dx/o.dl;o.mpp=Math.random()*0.5+0.3;o.mpl1=o.dl*o.mpp;o.mpl2=o.dl-o.mpl1;ll=Math.log(o.dl*Math.E);o.cma=Math.random()*ll*cv-ll*cv/2;o.cpt={x:p1.x+o.sx*o.mpl1+o.tx*o.cma,y:p1.y+o.sy*o.mpl1+o.ty*o.cma};o.bbx1=Math.min(p1.x,p2.x,o.cpt.x);o.bby1=Math.min(p1.y,p2.y,o.cpt.y);o.bbx2=Math.max(p1.x,p2.x,o.cpt.x);o.bby2=Math.max(p1.y,p2.y,o.cpt.y);o.bbwidth=o.bbx2-o.bbx1;o.bbheight=o.bby2-o.bby1;return o}