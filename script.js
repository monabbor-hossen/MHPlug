// ── Particle Background ──
(function(){
  const c=document.getElementById('bg-canvas');
  if(!c) return;
  const ctx=c.getContext('2d');
  let W,H,pts=[];
  function resize(){W=c.width=window.innerWidth;H=c.height=window.innerHeight;}
  resize();window.addEventListener('resize',resize);
  for(let i=0;i<90;i++)pts.push({x:Math.random()*W,y:Math.random()*H,vx:(Math.random()-.5)*.4,vy:(Math.random()-.5)*.4,r:Math.random()*1.5+.5});
  function draw(){
    ctx.clearRect(0,0,W,H);
    pts.forEach(p=>{
      p.x+=p.vx;p.y+=p.vy;
      if(p.x<0||p.x>W)p.vx*=-1;
      if(p.y<0||p.y>H)p.vy*=-1;
      ctx.beginPath();ctx.arc(p.x,p.y,p.r,0,Math.PI*2);
      ctx.fillStyle='rgba(0,212,255,.5)';ctx.fill();
    });
    pts.forEach((a,i)=>pts.slice(i+1).forEach(b=>{
      const d=Math.hypot(a.x-b.x,a.y-b.y);
      if(d<130){ctx.beginPath();ctx.moveTo(a.x,a.y);ctx.lineTo(b.x,b.y);ctx.strokeStyle=`rgba(0,168,232,${.18*(1-d/130)})`;ctx.lineWidth=.6;ctx.stroke();}
    }));
    requestAnimationFrame(draw);
  }
  draw();
})();

// ── FAQ ──
function tFaq(btn){
  const body=btn.nextElementSibling,ico=btn.querySelector('.faq-ico');
  const open=body.classList.contains('open');
  document.querySelectorAll('.faq-body').forEach(b=>b.classList.remove('open'));
  document.querySelectorAll('.faq-ico').forEach(i=>i.classList.remove('open'));
  if(!open){body.classList.add('open');ico.classList.add('open');}
}

// ── Document Loaded Init ──
document.addEventListener("DOMContentLoaded", function() {
    // ── Scroll Reveal ──
    const obs=new IntersectionObserver(es=>es.forEach(e=>{if(e.isIntersecting)e.target.classList.add('in');}),{threshold:.1});
    document.querySelectorAll('.rv').forEach(el=>obs.observe(el));

    // ── 3D Card Tilt ──
    document.querySelectorAll('.card3d').forEach(card=>{
      card.addEventListener('mousemove',e=>{
        const r=card.getBoundingClientRect();
        const rx=((e.clientY-r.top)/r.height-.5)*-14;
        const ry=((e.clientX-r.left)/r.width-.5)*14;
        card.style.transform=`perspective(900px) rotateX(${rx}deg) rotateY(${ry}deg) translateZ(22px)`;
      });
      card.addEventListener('mouseleave',()=>{card.style.transform='';});
    });

    // ── Nav active link highlight ──
    const navLinks=document.querySelectorAll('nav a[href^="#"]');
    window.addEventListener('scroll',()=>{
      const pos=window.scrollY+100;
      navLinks.forEach(a=>{
        const sec=document.querySelector(a.getAttribute('href'));
        if(sec&&pos>=sec.offsetTop&&pos<sec.offsetTop+sec.offsetHeight)
          a.style.color='#00d4ff';
        else a.style.color='';
      });
    },{ passive:true });
});
