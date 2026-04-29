# Mohd Rehan — Creative Developer Portfolio

A sleek, fully responsive personal portfolio website built with pure HTML, CSS, and JavaScript — no frameworks, no build tools, no dependencies.

---

## 🚀 Live Preview

> Open `index.html` directly in a browser, or deploy the folder to any static host (GitHub Pages, Netlify, Vercel, etc.).

---

## 📁 Project Structure

```
/
├── index.html          # Entire portfolio — markup, styles, and scripts in one file
└── images/
    └── profile.png     # Hero section profile photo
```

---

## ✨ Features

- **Dark / Light theme** — persists across sessions via `localStorage`
- **Responsive layout** — mobile-first with a hamburger menu for smaller screens
- **Scroll-reveal animations** — elements fade in as they enter the viewport using `IntersectionObserver`
- **Currency toggle** — switches pricing between ₹ INR and $ USD instantly
- **Floating hero badges** — animated stat badges with CSS keyframe animations
- **Contact form** — client-side validation with inline error messages
- **Profile image fallback** — displays initials ("MR") if `profile.png` fails to load
- **Active nav highlighting** — nav links update automatically as the user scrolls

---

## 🛠️ Tech Stack

| Layer      | Technology                                                        |
|------------|-------------------------------------------------------------------|
| Markup     | HTML5                                                             |
| Styling    | CSS3 (custom properties, CSS Grid, Flexbox, keyframe animations)  |
| Scripting  | Vanilla JavaScript (no frameworks)                                |
| Fonts      | Google Fonts — Syne, DM Sans                                      |
| Payments   | Razorpay (pricing plan links)                                     |

---

## ⚙️ Setup & Deployment

### Run locally

No install needed. Just open the file:

```bash
open index.html        # macOS
start index.html       # Windows
xdg-open index.html    # Linux
```

Or serve it with any local server:

```bash
npx serve .
# or
python -m http.server 8000
```

### Deploy to GitHub Pages

1. Push the project folder to a GitHub repository.
2. Go to **Settings → Pages**.
3. Set the source to the `main` branch, root folder.
4. Your site will be live at `https://<username>.github.io/<repo>/`.

### Deploy to Netlify / Vercel

Drag and drop the project folder onto [netlify.com/drop](https://netlify.com/drop) — done.

### Connect the contact form

The form currently has client-side validation only. To make it actually send emails, wire it up to a free form backend:

- **[Formspree](https://formspree.io)** — change the `fetch` URL to your Formspree endpoint
- **[EmailJS](https://www.emailjs.com)** — call `emailjs.send()` in the submit handler
- **[Web3Forms](https://web3forms.com)** — post to their API with your access key

### Update your contact details

In `index.html`, replace the placeholders with your real info:

```html
<a href="mailto:yourmail@gmail.com">yourmail@gmail.com</a>
<a href="tel:+917893250037">+91 7893250037</a>
```

---

## 🎨 Customisation Guide

### Colours & theme tokens

All colours are defined as CSS custom properties at the top of the `<style>` block under `[data-theme="dark"]` and `[data-theme="light"]`. Change `--accent` to instantly retheme the entire site:

```css
[data-theme="dark"] {
    --accent:  #ff6b4a;   /* ← change this */
    --accent2: #ff9473;   /* ← and this for hover states */
}
```

### Profile photo

Replace `images/profile.png` with your own photo. The image is cropped to a circle, so a square image works best. If the image is missing or fails to load, the initials badge ("MR") displays automatically.

### Projects

Each project card lives inside `.projects-grid`. Copy an existing card block and update the gradient class (`proj-1` → `proj-3` etc.), emoji, tag, title, description, and `href`:

```html
<div class="project-card proj-3 reveal delay-3">
    <div class="bg"></div>
    <div class="proj-emoji" aria-hidden="true">🌐</div>
    <div class="overlay">
        <span class="project-tag">Your Category</span>
        <h3>Your Project Title</h3>
        <p class="project-desc">Short description of the project.</p>
        <a href="https://your-project-url.com" class="project-link">View Project →</a>
    </div>
</div>
```

### Pricing

Update the `data-inr` and `data-usd` attributes on each `.price-amount` element and the Razorpay `href` links on the buttons.

### Stats

Edit the `.num` and `.lbl` spans inside `.stats-grid` in the About section.

---

## 📋 Sections Overview

| Section        | ID / Class       | Description                                      |
|----------------|------------------|--------------------------------------------------|
| Navigation     | `.nav`           | Sticky header with logo, links, theme toggle     |
| Hero           | `.hero`          | Headline, CTA buttons, animated profile photo    |
| About          | `#about`         | Bio, skill tags, stats grid                      |
| Work           | `#work`          | Project cards grid                               |
| Pricing        | `#pricing`       | Three-tier pricing with INR/USD toggle           |
| Testimonials   | `.section`       | Client review cards                              |
| Contact        | `#contact`       | Contact info + validated form                    |
| Footer         | `footer`         | Copyright + back-to-top link                     |

---

## 🐛 Known Limitations

- The contact form ships with client-side validation only — no emails are sent until you wire it to a form backend (see Setup above).
- The currency toggle resets on page refresh (no persistence by design — amounts are `data-*` attributes).

---

## 📄 License

This project is for personal/portfolio use. Feel free to adapt the code for your own portfolio — credit appreciated but not required.

---

*Built with ❤️ by [Mohd Rehan](https://www.linkedin.com/in/mohdrehxn/)*# portfolio
