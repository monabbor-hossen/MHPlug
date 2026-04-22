/* ═══════════════════════════════════════════════════════════
   MH PLUG — PREMIUM LANDING PAGE SCRIPTS
   ═══════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

    /* ─── STICKY HEADER ─────────────────────────────────── */
    const header = document.getElementById('siteHeader');
    let lastScroll = 0;

    const handleScroll = () => {
        const currentScroll = window.scrollY;
        header.classList.toggle('scrolled', currentScroll > 60);
        lastScroll = currentScroll;
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();

    /* ─── MOBILE NAVIGATION ─────────────────────────────── */
    const mobileToggle = document.getElementById('mobileToggle');
    const mainNav = document.getElementById('mainNav');

    if (mobileToggle && mainNav) {
        mobileToggle.addEventListener('click', () => {
            const isOpen = mainNav.classList.toggle('open');
            mobileToggle.classList.toggle('open');
            mobileToggle.setAttribute('aria-expanded', isOpen);
            document.body.style.overflow = isOpen ? 'hidden' : '';
        });

        // Close nav on link click
        mainNav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mainNav.classList.remove('open');
                mobileToggle.classList.remove('open');
                mobileToggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            });
        });
    }

    /* ─── FAQ ACCORDION ─────────────────────────────────── */
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');

        question.addEventListener('click', () => {
            const isOpen = item.classList.contains('open');

            // Close all other items
            faqItems.forEach(other => {
                if (other !== item) {
                    other.classList.remove('open');
                    other.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
                }
            });

            // Toggle current item
            item.classList.toggle('open', !isOpen);
            question.setAttribute('aria-expanded', !isOpen);
        });
    });

    /* ─── WIDGET TABS ───────────────────────────────────── */
    const tabs = document.querySelectorAll('.widget-tab');
    const panels = document.querySelectorAll('.widget-panel');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const targetTab = tab.dataset.tab;

            // Update tabs
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            // Update panels
            panels.forEach(panel => {
                panel.classList.remove('active');
                if (panel.id === 'panel-' + targetTab) {
                    panel.classList.add('active');
                }
            });
        });
    });

    /* ─── 3D TILT EFFECT ────────────────────────────────── */
    const tiltCards = document.querySelectorAll('[data-tilt]');

    tiltCards.forEach(card => {
        const maxTilt = parseFloat(card.dataset.tiltMax) || 8;

        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            const rotateX = ((y - centerY) / centerY) * -maxTilt;
            const rotateY = ((x - centerX) / centerX) * maxTilt;

            card.style.transform =
                `perspective(800px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-8px) scale(1.02)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(800px) rotateX(0) rotateY(0) translateY(0) scale(1)';
            card.style.transition = 'transform 0.5s ease';
        });

        card.addEventListener('mouseenter', () => {
            card.style.transition = 'transform 0.1s ease';
        });
    });

    /* ─── HERO 3D BOX MOUSE TRACKING ────────────────────── */
    const floatingBox = document.getElementById('floatingBox');

    if (floatingBox) {
        const heroSection = document.querySelector('.hero');

        heroSection.addEventListener('mousemove', (e) => {
            const rect = heroSection.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width - 0.5;
            const y = (e.clientY - rect.top) / rect.height - 0.5;

            const rotateY = x * 15 - 8;
            const rotateX = -y * 10 + 5;

            floatingBox.style.animation = 'none';
            floatingBox.style.transform =
                `rotateY(${rotateY}deg) rotateX(${rotateX}deg)`;
        });

        heroSection.addEventListener('mouseleave', () => {
            floatingBox.style.transition = 'transform 0.6s ease';
            floatingBox.style.animation = 'boxFloat 6s ease-in-out infinite';
            floatingBox.style.transform = '';

            setTimeout(() => {
                floatingBox.style.transition = '';
            }, 600);
        });
    }

    /* ─── SCROLL REVEAL ANIMATION ───────────────────────── */
    const revealElements = () => {
        const reveals = [
            ...document.querySelectorAll('.feature-card'),
            ...document.querySelectorAll('.step-card'),
            ...document.querySelectorAll('.faq-item'),
            ...document.querySelectorAll('.section-header'),
            ...document.querySelectorAll('.terminal-card'),
            ...document.querySelectorAll('.cta-inner'),
        ];

        reveals.forEach(el => {
            if (!el.classList.contains('reveal')) {
                el.classList.add('reveal');
            }

            const rect = el.getBoundingClientRect();
            const viewHeight = window.innerHeight;

            if (rect.top < viewHeight - 80) {
                el.classList.add('visible');
            }
        });
    };

    window.addEventListener('scroll', revealElements, { passive: true });
    // Trigger on load with a slight delay for initial position
    setTimeout(revealElements, 100);

    /* ─── SMOOTH SCROLL FOR ANCHOR LINKS ────────────────── */
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', (e) => {
            const targetId = link.getAttribute('href');
            if (targetId === '#') return;

            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                const headerOffset = 80;
                const elementPosition = target.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.scrollY - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    /* ─── HERO BUTTON RIPPLE EFFECT ─────────────────────── */
    const heroBtn = document.getElementById('heroBtn');

    if (heroBtn) {
        heroBtn.addEventListener('click', function(e) {
            e.preventDefault();

            const ripple = document.createElement('span');
            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.3);
                pointer-events: none;
                transform: scale(0);
                animation: rippleEffect 0.6s ease-out forwards;
            `;

            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
            ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';

            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });

        // Add ripple keyframe dynamically
        const style = document.createElement('style');
        style.textContent = `
            @keyframes rippleEffect {
                to { transform: scale(2.5); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }

    /* ─── PARALLAX on ORBS ──────────────────────────────── */
    const orbs = document.querySelectorAll('.orb');

    if (orbs.length > 0) {
        window.addEventListener('scroll', () => {
            const scrollY = window.scrollY;
            orbs.forEach((orb, i) => {
                const speed = (i + 1) * 0.03;
                orb.style.transform = `translateY(${scrollY * speed}px)`;
            });
        }, { passive: true });
    }

    /* ─── MOCK TOGGLE ANIMATION ─────────────────────────── */
    const mockToggles = document.querySelectorAll('.mock-toggle');
    let toggleIndex = 0;

    setInterval(() => {
        if (mockToggles.length === 0) return;

        const toggle = mockToggles[toggleIndex];
        toggle.classList.toggle('on');

        toggleIndex = (toggleIndex + 1) % mockToggles.length;
    }, 3000);
});
