/**
 * Mohd Tadveed Rehan - Premium Architecture Core Script Engine
 * Native ES6+ Structural Operations Layout Execution 
 */

document.addEventListener('DOMContentLoaded', () => {
    initThemeEngine();
    initNavigationEngine();
    initCustomCursor();
    initParticleCanvas();
    initTypingEngine();
    initScrollEngine();
    initProjectFiltering();
    initFormValidationEngine();
});

/* ==========================================================================
   THEME SWITCHING INFRASTRUCTURE
   ========================================================================== */
function initThemeEngine() {
    const themeToggle = document.getElementById('theme-toggle');
    const currentTheme = localStorage.getItem('portfolio-theme') || 'dark';
    
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
    if (theme === 'light') {
        icon.className = 'fas fa-sun';
    } else {
        icon.className = 'fas fa-moon';
    }
}

/* ==========================================================================
   MOBILE TRACKING DRAWER & MENU ACTIONS
   ========================================================================== */
function initNavigationEngine() {
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('nav-menu');
    const header = document.querySelector('.header');
    const navLinks = document.querySelectorAll('.nav-link');

    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('open');
        navMenu.classList.toggle('open');
    });

    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            hamburger.classList.remove('open');
            navMenu.classList.remove('open');
        });
    });

    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
}

/* ==========================================================================
   FLUID CUSTOM POINTER ENGINE
   ========================================================================== */
function initCustomCursor() {
    const cursor = document.querySelector('.custom-cursor');
    const dot = document.querySelector('.custom-cursor-dot');
    
    if (window.matchMedia('(max-width: 768px)').matches) return;

    document.addEventListener('mousemove', (e) => {
        cursor.style.left = `${e.clientX}px`;
        cursor.style.top = `${e.clientY}px`;
        
        dot.style.left = `${e.clientX}px`;
        dot.style.top = `${e.clientY}px`;
    });

    const standardInteractiveItems = document.querySelectorAll('a, button, .filter-btn, .project-card');
    standardInteractiveItems.forEach(item => {
        item.addEventListener('mouseenter', () => {
            cursor.style.width = '50px';
            cursor.style.height = '50px';
            cursor.style.backgroundColor = 'rgba(255, 107, 74, 0.1)';
        });
        item.addEventListener('mouseleave', () => {
            cursor.style.width = '32px';
            cursor.style.height = '32px';
            cursor.style.backgroundColor = 'transparent';
        });
    });
}

/* ==========================================================================
   DYNAMIC BACKGROUND VECTOR PARTICLES
   ========================================================================== */
function initParticleCanvas() {
    const canvas = document.getElementById('particle-canvas');
    const ctx = canvas.getContext('2d');
    let particlesArray = [];
    
    function resizeCanvas() {
        canvas.width = canvas.parentElement.offsetWidth;
        canvas.height = canvas.parentElement.offsetHeight;
    }
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

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
        draw() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            ctx.fillStyle = currentTheme === 'light' ? 'rgba(232, 81, 42, 0.25)' : 'rgba(255, 107, 74, 0.2)';
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    function init() {
        particlesArray = [];
        const numberOfParticles = Math.floor((canvas.width * canvas.height) / 14000);
        for (let i = 0; i < numberOfParticles; i++) {
            particlesArray.push(new Particle());
        }
    }
    init();

    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        particlesArray.forEach(p => {
            p.update();
            p.draw();
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
    
    const words = JSON.parse(targetElement.getAttribute('data-words'));
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
            typingSpeed = 2000; // Pause window state at finish
            isDeleting = true;
        } else if (isDeleting && charIndex === 0) {
            isDeleting = false;
            wordIndex = (wordIndex + 1) % words.length;
            typingSpeed = 400; // Recalibration break
        }

        setTimeout(type, typingSpeed);
    }
    
    setTimeout(type, 500);
}

/* ==========================================================================
   SCROLL ENGINE (AOS EXTENSION, INTERSECTION OBSERVERS & METRICS)
   ========================================================================== */
function initScrollEngine() {
    const progressIndicator = document.getElementById('scroll-progress');
    const backToTop = document.getElementById('back-to-top');
    const reveals = document.querySelectorAll('.scroll-reveal');
    const navLinks = document.querySelectorAll('.nav-link');
    const sections = document.querySelectorAll('section');

    // Scroll Progress & Back-To-Top Engine Trigger
    window.addEventListener('scroll', () => {
        const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (winScroll / height) * 100;
        progressIndicator.style.width = `${scrolled}%`;

        if (winScroll > 400) {
            backToTop.classList.add('visible');
        } else {
            backToTop.classList.remove('visible');
        }

        // Active Link Highlighting Matrix Switcher
        let activeId = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 160;
            if (winScroll >= sectionTop) {
                activeId = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${activeId}`) {
                link.classList.add('active');
            }
        });
    });

    backToTop.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Intersection Elements Loader (Scroll Reveal + Counters + Bars)
    const elementObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                
                // If section target contains progressive elements, run animations
                if(entry.target.id === 'skills') animateProgressBars();
                if(entry.target.id === 'about') animateStatCounters();
            }
        });
    }, { threshold: 0.15 });

    reveals.forEach(el => elementObserver.observe(el));
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
        const speed = target / 40; 

        const updateCount = () => {
            count += speed;
            if (count < target) {
                counter.textContent = Math.ceil(count);
                setTimeout(updateCount, 25);
            } else {
                counter.textContent = target;
                counter.classList.add('counted');
            }
        };
        updateCount();
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
                card.style.transform = 'scale(0.92)';
                card.style.opacity = '0';
                
                setTimeout(() => {
                    if (criteria === 'all' || card.getAttribute('data-category') === criteria) {
                        card.style.display = 'flex';
                        setTimeout(() => {
                            card.style.transform = 'scale(1)';
                            card.style.opacity = '1';
                        }, 50);
                    } else {
                        card.style.display = 'none';
                    }
                }, 300);
            });
        });
    });
}

/* ==========================================================================
   CONTACT DISPATCH ASYNC ACCESSIBILITY VALIDATION ENGINE
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

        // Regex Architecture Rules
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
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span>Processing Link...</span> <i class="fas fa-spinner fa-spin"></i>`;
            
            // Mock Async Processing Endpoint Delay
            setTimeout(() => {
                submitBtn.innerHTML = `<span>Securely Transmitted!</span> <i class="fas fa-check-circle"></i>`;
                submitBtn.style.backgroundColor = '#22c55e';
                form.reset();
                
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    submitBtn.style.backgroundColor = '';
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