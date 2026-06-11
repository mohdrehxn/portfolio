<div align="center">

# 🚀 Mohd Tadveed Rehan — Portfolio

**A premium, single-file personal portfolio website with a modern dark aesthetic, smooth animations, and full light/dark mode support.**

[![Live Demo](https://img.shields.io/badge/Live%20Demo-Vercel-black?style=for-the-badge&logo=vercel)](https://portfolio-gamma-brown-76.vercel.app)
[![Repo](https://img.shields.io/badge/GitHub-mohdrehxn%2Fportfolio-181717?style=for-the-badge&logo=github)](https://github.com/mohdrehxn/portfolio)
[![HTML](https://img.shields.io/badge/Built%20With-HTML%20100%25-E34F26?style=for-the-badge&logo=html5&logoColor=white)](https://github.com/mohdrehxn/portfolio)

</div>

---

## 📖 Introduction

This is the personal developer portfolio of **Mohd Tadveed Rehan** — a Frontend Developer and BCA student based in Hyderabad, India. The site showcases skills, featured projects, certifications, services, and contact information in a visually rich, single-page experience.

The entire portfolio is self-contained in a single `index.html` file (2,369 lines / 82 KB) — no build tools, no frameworks, no bundlers. Just clean, well-structured HTML, CSS, and vanilla JavaScript, deployed on Vercel.

> **"Building responsive modern digital experiences."**

---

## 🛠️ Technologies

| Layer | Technology |
|---|---|
| **Markup** | HTML5 (semantic structure) |
| **Styling** | CSS3 — Custom Properties, Flexbox, CSS Grid, Glassmorphism, Backdrop Filter |
| **Scripting** | Vanilla JavaScript (ES6+) |
| **Fonts** | [Space Grotesk](https://fonts.google.com/specimen/Space+Grotesk) (headings) · [Plus Jakarta Sans](https://fonts.google.com/specimen/Plus+Jakarta+Sans) (body) via Google Fonts |
| **Icons** | [Font Awesome 6.4.0](https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css) via CDN |
| **Animation** | CSS keyframes + JS-driven canvas particle system |
| **Deployment** | [Vercel](https://vercel.com) |

No npm, no webpack, no React — purely hand-crafted front-end.

---

## ✨ Features

- **Dark / Light Theme Toggle** — Smooth CSS variable swap with persistent preference, powered by `[data-theme]` attribute
- **Particle Canvas Background** — Animated particle system rendered on an HTML5 `<canvas>` in the hero section
- **Custom Cursor** — Styled cursor ring + dot that reacts to hover states
- **Scroll Progress Bar** — Fixed top bar that fills as the user scrolls down the page
- **Typing Animation** — Rotating role titles with a blinking caret effect in the hero
- **Glassmorphism UI** — Frosted-glass cards with `backdrop-filter: blur()` throughout
- **Animated Skill Bars** — Progress fills triggered by Intersection Observer when sections enter the viewport
- **Project Filter System** — Category-based JavaScript filtering for the projects grid (Frontend, Backend, AI, etc.)
- **Education Timeline** — Vertical timeline with glowing accent dots
- **Liquid Blob Illustration** — CSS `border-radius` morphing animation on the hero image container
- **Stats Counter** — Animated number counters for experience stats
- **Responsive Navigation** — Hamburger menu for mobile with animated bar-to-X transition
- **Fully Responsive** — Fluid grid layouts that reflow from desktop to mobile
- **Reduced Motion Support** — Respects `prefers-reduced-motion` media query

---

## 🔧 Process

The portfolio was built with a **code-first, design-in-browser** approach — no external design tool handoff.

1. **Design System First** — CSS custom properties (`--accent-primary`, `--bg-primary`, `--glass-bg`, etc.) were defined upfront as a single source of truth for both dark and light themes.
2. **Section-by-Section Build** — Each section (Hero → About → Skills → Projects → Certifications → Services → Contact) was built and styled independently using modular CSS blocks, clearly delineated by comments.
3. **Animation Layer** — JS-driven effects (particle canvas, scroll progress, typed text, counters, skill bar fills) were layered in after the static layout was solid, using `IntersectionObserver` to keep performance clean.
4. **Theme Engine** — Light mode was implemented as a CSS variable override on `[data-theme="light"]`, requiring zero duplicate code.
5. **Responsive Pass** — Media queries were applied globally after each major section to ensure consistent reflow down to mobile widths.
6. **Deployment** — The repo was connected directly to Vercel for zero-config continuous deployment on every push to `main`.

---

## ▶️ Running the Project

No installation or build step is required.

### Option 1 — Open Directly (Simplest)

```bash
# Clone the repository
git clone https://github.com/mohdrehxn/portfolio.git

# Navigate into the folder
cd portfolio

# Open in your browser
open index.html        # macOS
start index.html       # Windows
xdg-open index.html    # Linux
```

### Option 2 — Live Server (Recommended for Development)

If you use **VS Code**, install the [Live Server extension](https://marketplace.visualstudio.com/items?itemName=ritwickdey.LiveServer), then right-click `index.html` → **Open with Live Server**.

Or use any static server via Node.js:

```bash
npx serve .
# → Serving at http://localhost:3000
```

### Option 3 — View Live

The site is deployed and live at:

```
https://portfolio-gamma-brown-76.vercel.app
```

No login, no setup — just open and explore.

---

## 🖼️ Preview

| Section | Description |
|---|---|
| **Hero** | Full-viewport intro with particle canvas, typing animation, and liquid blob profile image |
| **About** | Two-column layout with bio text and animated stat counters |
| **Skills** | Categorised skill cards with animated progress bars |
| **Projects** | Filterable project grid with tech tags and live/GitHub links |
| **Education** | Vertical timeline with glowing accent markers |
| **Certifications** | Icon-led certification cards in a responsive two-column grid |
| **Services** | Centred service cards with hover lift effect |
| **Contact** | Split layout with contact info panel and an interactive form |

> **Live Preview →** [portfolio-gamma-brown-76.vercel.app](https://portfolio-gamma-brown-76.vercel.app)

### Color Palette

| Token | Dark Mode | Light Mode |
|---|---|---|
| Background Primary | `#060b12` | `#f4f6fb` |
| Accent Primary | `#ff6b4a` | `#e8512a` |
| Surface | `#0f1a27` | `#ffffff` |
| Text Primary | `#f5f7fb` | `#0f1a27` |

---

## 📁 Project Structure

```
portfolio/
├── index.html          # Entire site — markup, styles, and scripts in one file
├── assets/
│   └── images/
│       └── profile.png # Hero profile image
├── .vscode/            # VS Code workspace settings
├── .hintrc             # webhint configuration
└── TODO.md             # Development notes and planned improvements
```

---

## 📬 Contact

**Mohd Tadveed Rehan**  
Frontend Developer · BCA Student · Hyderabad, India

[![GitHub](https://img.shields.io/badge/GitHub-mohdrehxn-181717?style=flat-square&logo=github)](https://github.com/mohdrehxn)

---

<div align="center">
  Made with ❤️ and pure HTML · CSS · JS
</div>