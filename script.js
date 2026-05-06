// ═══ Particle Canvas Background ═══
(function(){
  const c=document.getElementById('bg-canvas');
  if(!c) return;
  const ctx=c.getContext('2d');
  let W,H,pts=[];
  function resize(){W=c.width=window.innerWidth;H=c.height=window.innerHeight;}
  resize();window.addEventListener('resize',resize);
  
  // Create particles
  for(let i=0;i<60;i++){
    pts.push({
      x:Math.random()*W,
      y:Math.random()*H,
      vx:(Math.random()-.5)*.4,
      vy:(Math.random()-.5)*.4,
      r:Math.random()*1.5+0.5
    });
  }
  
  function draw(){
    ctx.clearRect(0,0,W,H);
    pts.forEach(p=>{
      p.x+=p.vx;p.y+=p.vy;
      if(p.x<0||p.x>W)p.vx*=-1;
      if(p.y<0||p.y>H)p.vy*=-1;
      ctx.beginPath();ctx.arc(p.x,p.y,p.r,0,Math.PI*2);
      ctx.fillStyle='rgba(0, 212, 255, 0.4)';
      ctx.fill();
    });
    
    // Draw connecting lines based on proximity
    pts.forEach((a,i)=>pts.slice(i+1).forEach(b=>{
      const d=Math.hypot(a.x-b.x,a.y-b.y);
      if(d<150){
        ctx.beginPath();
        ctx.moveTo(a.x,a.y);
        ctx.lineTo(b.x,b.y);
        ctx.strokeStyle=`rgba(0, 66, 101, ${0.4*(1-d/150)})`;
        ctx.lineWidth=0.5;
        ctx.stroke();
      }
    }));
    requestAnimationFrame(draw);
  }
  draw();
})();

// ═══ Document Initialization ═══
document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Scroll Reveal Intersection Observer
    const revealOptions = { threshold: 0.1, rootMargin: "0px 0px -50px 0px" };
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in');
                // Optional: Unobserve after revealing if you don't want it to repeat
                // revealObserver.unobserve(entry.target); 
            }
        });
    }, revealOptions);

    // Target the new reveal classes
    document.querySelectorAll('.rv-up, .rv-scale').forEach(el => revealObserver.observe(el));

    // 2. Dynamic Abstract Shape Movement (Mouse Parallax)
    const shapes = document.querySelectorAll('.shape-blob');
    window.addEventListener('mousemove', (e) => {
        const x = (e.clientX / window.innerWidth - 0.5) * 40;
        const y = (e.clientY / window.innerHeight - 0.5) * 40;
        
        shapes.forEach((shape, index) => {
            const speed = (index + 1) * 1.5;
            shape.style.transform = `translate(${x * speed}px, ${y * speed}px)`;
        });
    });

    // 3. Floating Nav Transparency Toggle
    const nav = document.querySelector('.floating-nav');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            nav.style.background = 'rgba(15, 23, 42, 0.8)';
            nav.style.boxShadow = '0 10px 30px rgba(0,0,0,0.5)';
        } else {
            nav.style.background = 'rgba(15, 23, 42, 0.4)';
            nav.style.boxShadow = '0 8px 32px 0 rgba(0, 0, 0, 0.3)';
        }
    }, { passive: true });

});
