# Iristick Static WordPress Theme

This package renders WordPress PHP page templates without loading the Svelte runtime.

## Install

1. Upload `iristick-static-theme.zip` in Appearance > Themes > Add New > Upload Theme.
2. Activate **Iristick Static**.
3. Open Settings > Permalinks and click Save Changes once.

The original Svelte CSS classes are retained because the captured styles depend on them. Page markup lives under `templates/pages` as internal PHP templates; the `static` directory contains assets only. Svelte entry, node and chunk JavaScript is not included. Desktop dropdowns, the mobile menu, FAQ accordions, and news search are handled by the standalone vanilla-JavaScript file `assets/js/static-navigation.js`.

Captured Umami analytics, Crisp chat and Svelte hydration are removed during rendering.
