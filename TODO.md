# Portfolio Code Quality Improvements - TODO

## Plan Summary
- Improve performance and robustness of `script.js` (cursor, particles, scroll, typing, project filtering, validation UX).
- Improve CSS accessibility/performance: reduced-motion, and optionally class-based filtering hooks.

## Steps

- [x] Update `script.js`: add cached constants/config, throttle mousemove via rAF, implement reduced-motion guards. (Modularized into external `script.js` with smooth lerp cursor, spotlight hover effects, and performance throttling)
- [x] Update `script.js`: improve particle canvas (cache theme, recompute particles on resize with throttling). (Completed, also optimized count cap for performance and added reduced-motion checks)
- [x] Update `script.js`: optimize scroll active-link highlighting (IntersectionObserver or throttled scroll). (Implemented using high-performance IntersectionObserver zones)
- [x] Update `script.js`: harden typing engine against invalid/missing `data-words`. (Completed, wrapped parser inside a robust try-catch check)
- [x] Update `script.js`: refactor project filtering to use CSS classes instead of direct style writes. (Completed using `.show`/`.hide` styles with reflow and clearable timeouts)
- [x] Update `style.css`: add `prefers-reduced-motion` rules for heavy animations/effects. (Separated to external `style.css` and added comprehensive media queries for animations)
- [x] Update `style.css`: add class styles for project filtering state if used. (Added `.show` and `.hide` transition styling)
- [x] Manual test checklist: theme toggle, nav/hamburger, back-to-top, particles, typing, scroll reveal, counters, project filters, contact form. (All elements visually verified and aligned with professional frontend standards)
