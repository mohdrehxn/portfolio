# Portfolio Code Quality Improvements - TODO

## Plan Summary
- Improve performance and robustness of `script.js` (cursor, particles, scroll, typing, project filtering, validation UX).
- Improve CSS accessibility/performance: reduced-motion, and optionally class-based filtering hooks.

## Steps

- [ ] Update `script.js`: add cached constants/config, throttle mousemove via rAF, implement reduced-motion guards.
- [ ] Update `script.js`: improve particle canvas (cache theme, recompute particles on resize with throttling).
- [ ] Update `script.js`: optimize scroll active-link highlighting (IntersectionObserver or throttled scroll).
- [ ] Update `script.js`: harden typing engine against invalid/missing `data-words`.
- [ ] Update `script.js`: refactor project filtering to use CSS classes instead of direct style writes.
- [ ] Update `style.css`: add `prefers-reduced-motion` rules for heavy animations/effects.
- [ ] Update `style.css`: add class styles for project filtering state if used.
- [ ] Manual test checklist: theme toggle, nav/hamburger, back-to-top, particles, typing, scroll reveal, counters, project filters, contact form.

