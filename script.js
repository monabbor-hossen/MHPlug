
// ═══ Preloader Logic (Run Immediately) ═══
(function() {
    const preloader = document.querySelector('.preloader');
    const loaderBar = document.querySelector('.loader-bar-fill');
    
    if (preloader) {
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 25;
            if (progress >= 100) {
                progress = 100;
                clearInterval(interval);
                setTimeout(() => {
                    preloader.classList.add('loaded');
                    document.body.style.overflow = 'auto'; // Re-enable scroll
                }, 400);
            }
            if (loaderBar) loaderBar.style.width = progress + '%';
        }, 120);

        // Ultimate Safety: Force hide after 4 seconds
        setTimeout(() => {
            preloader.classList.add('loaded');
            document.body.style.overflow = 'auto';
        }, 4000);
    }
})();

// ═══ Document Initialization ═══
document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Scroll Reveal Intersection Observer
    try {
        const revealOptions = { threshold: 0.1, rootMargin: "0px 0px -50px 0px" };
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in');
                }
            });
        }, revealOptions);
        document.querySelectorAll('.rv-up, .rv-scale').forEach(el => revealObserver.observe(el));
    } catch(e) { console.error("Reveal Error", e); }

    // 2. Dynamic Abstract Shape Movement
    const shapes = document.querySelectorAll('.shape-blob');
    window.addEventListener('mousemove', (e) => {
        const x = (e.clientX / window.innerWidth - 0.5) * 40;
        const y = (e.clientY / window.innerHeight - 0.5) * 40;
        shapes.forEach((shape, index) => {
            const speed = (index + 1) * 1.5;
            shape.style.transform = `translate(${x * speed}px, ${y * speed}px)`;
        });
    });

    // 3. Nav & Scroll Effects
    const nav = document.querySelector('.floating-nav');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            if (nav) {
                nav.style.background = 'rgba(15, 23, 42, 0.8)';
                nav.style.boxShadow = '0 10px 30px rgba(0,0,0,0.5)';
            }
        } else {
            if (nav) {
                nav.style.background = 'rgba(15, 23, 42, 0.4)';
                nav.style.boxShadow = '0 8px 32px 0 rgba(0, 0, 0, 0.3)';
            }
        }
    }, { passive: true });

    // 4. 3D Tilt for Bento Items
    const bentoItems = document.querySelectorAll('.bento-item');
    bentoItems.forEach(item => {
        item.addEventListener('mousemove', (e) => {
            const rect = item.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = (y - centerY) / 20;
            const rotateY = (centerX - x) / 20;
            item.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-8px)`;
        });
        item.addEventListener('mouseleave', () => {
            item.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(0)';
        });
    });

    // 5. FAQ Accordion Logic
    document.querySelectorAll('.faq-trigger').forEach(trigger => {
        trigger.addEventListener('click', () => {
            const item = trigger.parentElement;
            const isActive = item.classList.contains('active');
            document.querySelectorAll('.faq-item').forEach(f => f.classList.remove('active'));
            if (!isActive) item.classList.add('active');
        });
    });

    // 6. Performance Bar Animation
    try {
        const perfObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const fills = entry.target.querySelectorAll('.perf-bar-fill');
                    fills.forEach(fill => {
                        const styleWidth = fill.getAttribute('style');
                        const match = styleWidth ? styleWidth.match(/width:\s*(\d+)%/) : null;
                        if (match) {
                            const finalWidth = match[1];
                            fill.style.width = '0%';
                            setTimeout(() => { fill.style.width = finalWidth + '%'; }, 100);
                        }
                    });
                    perfObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        const perfSection = document.querySelector('#performance');
        if (perfSection) perfObserver.observe(perfSection);
    } catch(e) { console.error("Perf Error", e); }

    // 7. Quick Nav Scroll Sync
    const sections = document.querySelectorAll('section[id]');
    const qDots = document.querySelectorAll('.q-dot');
    window.addEventListener('scroll', () => {
        let current = "";
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            if (window.scrollY >= (sectionTop - 300)) {
                current = section.getAttribute('id');
            }
        });
        qDots.forEach(dot => {
            dot.classList.remove('active');
            if (dot.getAttribute('href') === '#' + current) {
                dot.classList.add('active');
            }
        });
    }, { passive: true });

});

// 9. Live Visualizer Switcher
function switchHeader(headerId) {
    const stage = document.getElementById('viz-stage');
    const buttons = document.querySelectorAll('.viz-btn');
    if (stage) {
        stage.classList.remove('active-h1', 'active-h2', 'active-h3');
        stage.classList.add('active-' + headerId);
    }
    buttons.forEach(btn => {
        btn.classList.remove('active');
        if (btn.id === 'btn-' + headerId) btn.classList.add('active');
    });
}

// 10. Copy Code Function
function copyCode(btn) {
    const code = btn.closest('.code-window').querySelector('code').innerText;
    navigator.clipboard.writeText(code).then(() => {
        const originalText = btn.innerText;
        btn.innerText = 'COPIED!';
        btn.style.color = '#00d4ff';
        setTimeout(() => {
            btn.innerText = originalText;
            btn.style.color = '';
        }, 2000);
    });
}
