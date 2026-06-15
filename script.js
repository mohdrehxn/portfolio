/**
 * Mohd Tadveed Rehan - Premium Portfolio Core Script Engine
 * Native ES6+ Optimized Operations Layout Execution 
 */

document.addEventListener('DOMContentLoaded', () => {
    const cfg = createConfig();
    const reducedMotion = isReducedMotionEnabled(cfg);
    
    // Apply reduced-motion data attribute
    document.documentElement.dataset.reducedMotion = reducedMotion ? 'true' : 'false';

    // Initialize engines
    initThemeEngine();
    initNavigationEngine();
    
    if (!reducedMotion) {
        initCustomCursor();
        initParticleCanvas();
    }
    
    initTypingEngine();
    initScrollEngine();
    initProjectFiltering();
    initFormValidationEngine();
    initSpotlightEffect();
});

function createConfig() {
    return {
        navScrollThreshold: 50,
        backToTopThreshold: 400,
        sectionTopOffset: 160,
        typingStartDelayMs: 500,
        
        form: {
            nameMinLen: 2,
            emailRegex: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            subjectMinLen: 3,
            messageMinLen: 10,
            mockDelayMs: 1500,
            resetDelayMs: 4000,
        },

        motion: {
            reducedMotionQuery: '(prefers-reduced-motion: reduce)',
            disableAnimationsOnReducedMotion: true,
        },
    };
}

function isReducedMotionEnabled(config) {
    try {
        return config.motion.disableAnimationsOnReducedMotion && window.matchMedia(config.motion.reducedMotionQuery).matches;
    } catch {
        return false;
    }
}

// RequestAnimationFrame Throttle Helper
function rafThrottle(fn) {
    let ticking = false;
    let lastArgs;
    return (...args) => {
        lastArgs = args;
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(() => {
            ticking = false;
            fn(...lastArgs);
        });
    };
}

/* ==========================================================================
   THEME SWITCHING INFRASTRUCTURE
   ========================================================================== */
function initThemeEngine() {
    const themeToggle = document.getElementById('theme-toggle');
    if (!themeToggle) return;

    // Read stored theme or default to system preference
    const storedTheme = localStorage.getItem('portfolio-theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const currentTheme = storedTheme || (systemPrefersDark ? 'dark' : 'light');

    document.documentElement.setAttribute('data-theme', currentTheme);
    updateThemeIcon(currentTheme);

    themeToggle.addEventListener('click', () => {
        const activeTheme = document.documentElement.getAttribute('data-theme');
        const targetTheme = activeTheme === 'dark' ? 'light' : 'dark';

        document.documentElement.setAttribute('data-theme', targetTheme);
        localStorage.setItem('portfolio-theme', targetTheme);
        updateThemeIcon(targetTheme);
    });
}

function updateThemeIcon(theme) {
    const icon = document.querySelector('#theme-toggle i');
    if (!icon) return;
    
    if (theme === 'light') {
        icon.className = 'fas fa-sun';
    } else {
        icon.className = 'fas fa-moon';
    }
}

/* ==========================================================================
   NAVIGATION DRAWER & MENU ACTIONS
   ========================================================================== */
function initNavigationEngine() {
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('nav-menu');
    const header = document.querySelector('.header');
    const navLinks = document.querySelectorAll('.nav-link');

    if (!hamburger || !navMenu || !header) return;

    const closeMenu = () => {
        hamburger.classList.remove('open');
        navMenu.classList.remove('open');
    };

    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('open');
        navMenu.classList.toggle('open');
    });

    navLinks.forEach(link => {
        link.addEventListener('click', closeMenu);
    });

    // Simple scrolled state toggler
    const checkScrollState = () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    };
    
    checkScrollState();
    window.addEventListener('scroll', checkScrollState, { passive: true });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!navMenu.contains(e.target) && !hamburger.contains(e.target)) {
            closeMenu();
        }
    });

    // Close menu on scroll
    window.addEventListener('scroll', closeMenu, { passive: true });
}

/* ==========================================================================
   FLUID CUSTOM LERPING POINTER ENGINE
   ========================================================================== */
function initCustomCursor() {
    const cursor = document.querySelector('.custom-cursor');
    const dot = document.querySelector('.custom-cursor-dot');

    const isCoarsePointer = window.matchMedia('(pointer: coarse)').matches;
    const isSmallScreen = window.matchMedia('(max-width: 768px)').matches;

    if (!cursor || !dot) return;
    if (isSmallScreen || isCoarsePointer) {
        cursor.remove();
        dot.remove();
        return;
    }

    let targetX = 0, targetY = 0; // Mouse coords
    let cursorX = 0, cursorY = 0; // Ring coords
    let dotX = 0, dotY = 0;       // Dot coords
    let hasMoved = false;

    // Track mouse coordinate values
    document.addEventListener('mousemove', (e) => {
        targetX = e.clientX;
        targetY = e.clientY;
        
        if (!hasMoved) {
            hasMoved = true;
            // Instantly snap to first position to avoid jumping from (0,0)
            cursorX = dotX = targetX;
            cursorY = dotY = targetY;
            cursor.style.opacity = '1';
            dot.style.opacity = '1';
            document.body.classList.add('has-custom-cursor');
        }
    }, { passive: true });

    // Smooth animation loop for custom cursor (Lerping)
    function animateCursor() {
        if (hasMoved) {
            // Lerp Ring (delay factor 0.15)
            cursorX += (targetX - cursorX) * 0.15;
            cursorY += (targetY - cursorY) * 0.15;
            
            // Lerp Dot (delay factor 0.35)
            dotX += (targetX - dotX) * 0.35;
            dotY += (targetY - dotY) * 0.35;

            // Apply transforms (calc offset handled by translate3d)
            cursor.style.transform = `translate3d(calc(${cursorX}px - 50%), calc(${cursorY}px - 50%), 0)`;
            dot.style.transform = `translate3d(calc(${dotX}px - 50%), calc(${dotY}px - 50%), 0)`;
        }
        requestAnimationFrame(animateCursor);
    }
    animateCursor();

    // Hover states listeners
    const interactives = document.querySelectorAll('a, button, .filter-btn, .project-card, input, textarea');
    interactives.forEach(item => {
        item.addEventListener('mouseenter', () => {
            cursor.style.width = '48px';
            cursor.style.height = '48px';
            cursor.style.backgroundColor = 'rgba(255, 107, 74, 0.1)';
            cursor.style.borderColor = 'var(--accent-secondary)';
        });
        item.addEventListener('mouseleave', () => {
            cursor.style.width = '32px';
            cursor.style.height = '32px';
            cursor.style.backgroundColor = 'transparent';
            cursor.style.borderColor = 'var(--accent-primary)';
        });
    });

    // Hide custom cursor when mouse leaves the document window
    document.addEventListener('mouseleave', () => {
        cursor.style.opacity = '0';
        dot.style.opacity = '0';
    });
    document.addEventListener('mouseenter', () => {
        if (hasMoved) {
            cursor.style.opacity = '1';
            dot.style.opacity = '1';
        }
    });
}

/* ==========================================================================
   DYNAMIC BACKGROUND VECTOR PARTICLES
   ========================================================================== */
function initParticleCanvas() {
    const canvas = document.getElementById('particle-canvas');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    let particlesArray = [];
    let resizeTimeout;

    function resizeCanvas() {
        canvas.width = canvas.parentElement.offsetWidth;
        canvas.height = canvas.parentElement.offsetHeight;
        initParticles();
    }

    // Initialize/Throttle resize event
    resizeCanvas();
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(resizeCanvas, 200);
    });

    class Particle {
        constructor() {
            this.x = Math.random() * canvas.width;
            this.y = Math.random() * canvas.height;
            this.size = Math.random() * 2 + 1;
            this.speedX = Math.random() * 0.4 - 0.2;
            this.speedY = Math.random() * 0.4 - 0.2;
        }
        update() {
            this.x += this.speedX;
            this.y += this.speedY;
            if (this.x > canvas.width || this.x < 0) this.speedX = -this.speedX;
            if (this.y > canvas.height || this.y < 0) this.speedY = -this.speedY;
        }
        draw(theme) {
            ctx.fillStyle = theme === 'light' ? 'rgba(232, 81, 42, 0.25)' : 'rgba(255, 107, 74, 0.2)';
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    function initParticles() {
        particlesArray = [];
        const count = Math.floor((canvas.width * canvas.height) / 15000);
        const limitCount = Math.min(count, 120); // Cap particles for performance
        for (let i = 0; i < limitCount; i++) {
            particlesArray.push(new Particle());
        }
    }

    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
        
        particlesArray.forEach(p => {
            p.update();
            p.draw(currentTheme);
        });
        requestAnimationFrame(animate);
    }
    
    animate();
}

/* ==========================================================================
   DYNAMIC TYPING CHARACTER STRING PIPELINE
   ========================================================================== */
function initTypingEngine() {
    const targetElement = document.querySelector('.typing-text');
    if (!targetElement) return;

    let words = ["Web Developer", "Web Designer", "BCA Student"];
    try {
        const parsed = JSON.parse(targetElement.getAttribute('data-words'));
        if (Array.isArray(parsed) && parsed.length > 0) {
            words = parsed;
        }
    } catch (e) {
        console.warn("Failed parsing data-words in typing engine, using fallback lists.");
    }

    let wordIndex = 0;
    let charIndex = 0;
    let isDeleting = false;
    let typingSpeed = 100;

    function type() {
        const currentWord = words[wordIndex];

        if (isDeleting) {
            targetElement.textContent = currentWord.substring(0, charIndex - 1);
            charIndex--;
            typingSpeed = 40;
        } else {
            targetElement.textContent = currentWord.substring(0, charIndex + 1);
            charIndex++;
            typingSpeed = 120;
        }

        if (!isDeleting && charIndex === currentWord.length) {
            typingSpeed = 2000; // Pause at word completion
            isDeleting = true;
        } else if (isDeleting && charIndex === 0) {
            isDeleting = false;
            wordIndex = (wordIndex + 1) % words.length;
            typingSpeed = 400; // Pause before typing next word
        }

        setTimeout(type, typingSpeed);
    }

    setTimeout(type, 500);
}

/* ==========================================================================
   SCROLL ENGINE (SCROLL PROGRESS, BACK TO TOP, INTERSECTION OBSERVER ACTIVE NAV)
   ========================================================================== */
function initScrollEngine() {
    const progressIndicator = document.getElementById('scroll-progress');
    const backToTop = document.getElementById('back-to-top');
    const sections = document.querySelectorAll('section');
    const navLinks = document.querySelectorAll('.nav-link');
    const reveals = document.querySelectorAll('.scroll-reveal');

    // Throttled Scroll Listener (Progress bar & Back-to-top visibility)
    const handleScroll = () => {
        const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = height > 0 ? (winScroll / height) * 100 : 0;
        
        if (progressIndicator) {
            progressIndicator.style.width = `${scrolled}%`;
        }

        if (backToTop) {
            if (winScroll > 400) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        }
    };

    window.addEventListener('scroll', rafThrottle(handleScroll), { passive: true });

    if (backToTop) {
        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // High Performance Intersection Observer for Scroll Reveals, Progress bars & counters
    const sectionObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');

                // Trigger nested animation elements
                if (entry.target.id === 'skills') {
                    animateProgressBars();
                }
                if (entry.target.id === 'about') {
                    animateStatCounters();
                }
            }
        });
    }, { threshold: 0.15 });

    reveals.forEach(el => sectionObserver.observe(el));

    // High Performance Navigation active-state tracking using Intersection Observer
    const activeNavObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const activeId = entry.target.getAttribute('id');
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${activeId}`) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }, { rootMargin: '-25% 0px -55% 0px' }); // Concentrated center-viewport zone

    sections.forEach(sec => activeNavObserver.observe(sec));
}

function animateProgressBars() {
    const fills = document.querySelectorAll('.progress-fill');
    fills.forEach(fill => {
        fill.style.width = fill.getAttribute('data-progress');
    });
}

function animateStatCounters() {
    const counters = document.querySelectorAll('.stat-number');
    counters.forEach(counter => {
        if (counter.classList.contains('counted')) return;
        
        const target = +counter.getAttribute('data-target');
        let count = 0;
        const speed = target / 45; // Speed divisor
        
        const updateCount = () => {
            count += speed;
            if (count < target) {
                counter.textContent = Math.ceil(count);
                setTimeout(updateCount, 22);
            } else {
                counter.textContent = target;
                counter.classList.add('counted');
            }
        };
        updateCount();
    });
}

/* ==========================================================================
   SPOTLIGHT MOUSE HOVER GLOW SYSTEM
   ========================================================================== */
function initSpotlightEffect() {
    const cards = document.querySelectorAll('.project-card');
    
    // Throttle spotlight checks per frame
    const handleSpotlight = (card, e) => {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        card.style.setProperty('--mouse-x', `${x}px`);
        card.style.setProperty('--mouse-y', `${y}px`);
    };

    cards.forEach(card => {
        card.addEventListener('mousemove', (e) => handleSpotlight(card, e), { passive: true });
    });
}

/* ==========================================================================
   GRID FILTER CONTROLLER METRICS
   ========================================================================== */
function initProjectFiltering() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const projectCards = document.querySelectorAll('.project-card');

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            const criteria = button.getAttribute('data-filter');

            projectCards.forEach(card => {
                const category = card.getAttribute('data-category');
                
                // Clear any ongoing timer for this specific card
                if (card.dataset.timeoutId) {
                    clearTimeout(parseInt(card.dataset.timeoutId));
                }

                if (criteria === 'all' || category === criteria) {
                    card.style.display = 'flex';
                    // Trigger reflow to let browser register visual transition changes
                    void card.offsetWidth; 
                    card.classList.remove('hide');
                    card.classList.add('show');
                } else {
                    card.classList.remove('show');
                    card.classList.add('hide');
                    
                    const timeoutId = setTimeout(() => {
                        if (card.classList.contains('hide')) {
                            card.style.display = 'none';
                        }
                    }, 300); // Wait for transition fade out
                    card.dataset.timeoutId = timeoutId.toString();
                }
            });
        });
    });
}

/* ==========================================================================
   CONTACT FORM ASYNC ACCESSIBILITY VALIDATION ENGINE
   ========================================================================== */
function initFormValidationEngine() {
    const form = document.getElementById('portfolio-contact-form');
    if (!form) return;

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        let integrityPassed = true;

        const name = document.getElementById('form-name');
        const email = document.getElementById('form-email');
        const subject = document.getElementById('form-subject');
        const message = document.getElementById('form-message');

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (name.value.trim().length < 2) {
            toggleError(name, true);
            integrityPassed = false;
        } else {
            toggleError(name, false);
        }

        if (!emailRegex.test(email.value.trim())) {
            toggleError(email, true);
            integrityPassed = false;
        } else {
            toggleError(email, false);
        }

        if (subject.value.trim().length < 3) {
            toggleError(subject, true);
            integrityPassed = false;
        } else {
            toggleError(subject, false);
        }

        if (message.value.trim().length < 10) {
            toggleError(message, true);
            integrityPassed = false;
        } else {
            toggleError(message, false);
        }

        if (integrityPassed) {
            const submitBtn = form.querySelector('button[type="submit"]');
            const btnTextElement = submitBtn.querySelector('.btn-text');
            const btnIcon = submitBtn.querySelector('i');
            
            const originalText = btnTextElement ? btnTextElement.textContent : "Send Message";
            const originalIconClass = btnIcon ? btnIcon.className : "fas fa-paper-plane";

            submitBtn.disabled = true;
            if (btnTextElement) btnTextElement.textContent = "Sending...";
            if (btnIcon) btnIcon.className = "fas fa-spinner fa-spin";

            // Mock async endpoint submission delay
            setTimeout(() => {
                if (btnTextElement) btnTextElement.textContent = "Message Sent!";
                if (btnIcon) btnIcon.className = "fas fa-check-circle";
                submitBtn.style.backgroundColor = '#22c55e';
                submitBtn.style.borderColor = '#22c55e';
                form.reset();

                setTimeout(() => {
                    submitBtn.disabled = false;
                    if (btnTextElement) btnTextElement.textContent = originalText;
                    if (btnIcon) btnIcon.className = originalIconClass;
                    submitBtn.style.backgroundColor = '';
                    submitBtn.style.borderColor = '';
                }, 4000);
            }, 1500);
        }
    });

    function toggleError(inputElement, show) {
        const group = inputElement.parentElement;
        if (show) {
            group.classList.add('error');
        } else {
            group.classList.remove('error');
        }
    }
}
