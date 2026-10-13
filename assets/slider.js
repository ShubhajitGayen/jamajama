(function(){
 var s=document.getElementById('slider'); if(!s) return;
 var slides=[].slice.call(s.querySelectorAll('.slide')), dots=[].slice.call(s.querySelectorAll('.dots button')), i=0, t;
 function go(n){ i=(n+slides.length)%slides.length;
  slides.forEach(function(el,k){el.classList.toggle('active',k===i);el.setAttribute('aria-hidden',k===i?'false':'true');});
  dots.forEach(function(d,k){d.classList.toggle('on',k===i);}); }
 function start(){ stop(); if(slides.length>1 && !matchMedia('(prefers-reduced-motion: reduce)').matches) t=setInterval(function(){go(i+1);},6000); }
 function stop(){ clearInterval(t); }
 var p=s.querySelector('.prev'), n=s.querySelector('.next');
 if(p) p.onclick=function(){go(i-1);start();}; if(n) n.onclick=function(){go(i+1);start();};
 dots.forEach(function(d,k){d.onclick=function(){go(k);start();};});
 s.addEventListener('keydown',function(e){ if(e.key==='ArrowLeft'){go(i-1);start();} if(e.key==='ArrowRight'){go(i+1);start();} });
 s.addEventListener('mouseenter',stop); s.addEventListener('mouseleave',start);
 s.addEventListener('focusin',stop); s.addEventListener('focusout',start);
 start();
})();
