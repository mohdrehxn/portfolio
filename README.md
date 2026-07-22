# Mohd Tadveed Rehan | Portfolio

A premium, single-page developer portfolio built with pure HTML, CSS, and vanilla JavaScript — featuring a dark, futuristic aesthetic with glassmorphism, animated gradients, and smooth scroll-based interactions.

**Live Demo:** [portfolio-gamma-brown-76.vercel.app](https://portfolio-gamma-brown-76.vercel.app)

---

## Introduction

This is my personal portfolio website, designed to showcase my skills, projects, education, and services as a Frontend Developer and BCA student. The site is built entirely with native web technologies — no frameworks, no build tools — to keep it lightweight, fast, and fully self-contained in a single HTML file.

The design leans into a dark, cyberpunk-inspired visual language with glass-panel cards, neon accent gradients, particle backgrounds, and micro-interactions throughout, while remaining fully responsive and accessible.

---

## Technologies

- **HTML5** — Semantic markup structure
- **CSS3** — Custom properties (CSS variables), Grid, Flexbox, keyframe animations, backdrop-filter (glassmorphism)
- **Vanilla JavaScript (ES6+)** — No dependencies; native `IntersectionObserver`, `Canvas API`, `localStorage`, `matchMedia`
- **Font Awesome 6** — Icon library (via CDN)
- **Google Fonts** — Space Grotesk (headings) & Plus Jakarta Sans (body)

---

## Features

- 🌗 **Dark/Light Theme Toggle** — Persisted via `localStorage`, respects system preference on first load
- ✨ **Animated Particle Canvas** — Lightweight, performance-capped particle system in the hero background
- ⌨️ **Typewriter Effect** — Cycles through role titles in the hero subtitle
- 📊 **Animated Skill Bars & Stat Counters** — Triggered on scroll via `IntersectionObserver`
- 🗂️ **Filterable Projects Grid** — Filter by Frontend / Backend / AI category with smooth transitions
- 💡 **Spotlight Hover Effect** — Mouse-tracked radial glow on project cards
- 📱 **Fully Responsive** — Optimized breakpoints for tablet and mobile, including a slide-in mobile nav menu
- 📈 **Scroll Progress Bar & Back-to-Top Button**
- ✅ **Client-Side Form Validation** — Accessible error states with live feedback
- ♿ **Reduced Motion Support** — Disables non-essential animations when `prefers-reduced-motion` is set
- 🧊 **Glassmorphism UI** — Consistent frosted-glass card system across all sections

---

## Process

1. **Structure first** — Built out semantic HTML for each section (Hero, About, Skills, Education, Projects, Certifications, Services, Contact) before any styling.
2. **Design system via CSS variables** — Established a token-based color/spacing/typography system in `:root`, enabling instant theme switching by overriding tokens under `[data-theme="light"]`.
3. **Component styling** — Built reusable classes (`.glass-card`, `.btn`, `.section-padding`) to keep styling DRY across sections.
4. **Progressive JavaScript enhancement** — Layered in interactivity module by module (theme engine, navigation, particles, typing effect, scroll engine, filtering, form validation) via small, single-responsibility functions initialized on `DOMContentLoaded`.
5. **Performance passes** — Throttled scroll handlers with `requestAnimationFrame`, capped particle count, and used `IntersectionObserver` instead of scroll-position polling for reveal animations.
6. **Responsive refinement** — Tested and adjusted breakpoints at 992px and 768px for tablet/mobile layouts.
7. **Accessibility & motion safety** — Added `prefers-reduced-motion` handling and focus-visible states throughout.

---

## Running the Project

No build step or dependencies required — it's a static site.

**Option 1: Open directly**
```bash
# Clone the repo
git clone https://github.com/mohdrehxn/portfolio.git
cd portfolio

# Open index.html in your browser
open index.html   # macOS
start index.html  # Windows
```

**Option 2: Local server (recommended for full functionality)**
```bash
# Using Python
python3 -m http.server 5500

# Using Node (with the 'serve' package)
npx serve .
```
Then visit `http://localhost:5500` in your browser.

**Deployment**
The site is deployed on [Vercel](https://vercel.com) — simply connect the GitHub repo and deploy with zero configuration, since it's a static HTML project.

---

## Preview

| Section | Description |
|---|---|
| **Hero** | Animated intro with typewriter role text, particle canvas, and social links |
| **About** | Bio card alongside animated stat counters |
| **Skills** | Categorized skill bars (Frontend, Backend, Tools, Architecture) |
| **Education** | Timeline layout for academic background |
| **Projects** | Filterable grid with live demo & GitHub links |
| **Certifications** | Cards highlighting completed certifications |
| **Services** | Grid of offered services |
| **Contact** | Contact info panel + validated contact form |

*(Add screenshots or a GIF walkthrough here once available.)*

---

## Project Structure

```
portfolio/
├── index.html          # Main HTML file (structure, embedded CSS, embedded JS)
├── assets/
│   ├── images/
│   │   ├── profile.png     # Hero profile image
│   │   └── preview.png     # Open Graph / social share preview image
│   └── Mohd Rehan Resume.pdf   # Downloadable resume
└── README.md            # Project documentation
```

> **Note:** All CSS and JavaScript are currently embedded within `index.html` in `<style>` and `<script>` tags to keep the project dependency-free and easy to deploy as a single file.

---

## Contact

**Mohd Tadveed Rehan**
Frontend Developer · BCA Student · Hyderabad, India

- 📧 Email: [rehanaisha28@gmail.com](mailto:rehanaisha28@gmail.com)
- 💻 GitHub: [github.com/mohdrehxn](https://github.com/mohdrehxn)
- 🔗 LinkedIn: [linkedin.com/in/mohdrehxn](https://linkedin.com/in/mohdrehxn)
- 🌐 Portfolio: [portfolio-gamma-brown-76.vercel.app](https://portfolio-gamma-brown-76.vercel.app)

---

<p align="center">© 2026 Mohd Tadveed Rehan. All Rights Reserved.</p>