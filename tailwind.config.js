/** @type {import('tailwindcss').Config} */
export default {
  // The dark-mode toggle was removed; without this, dark: classes still
  // trigger automatically via the OS/browser's prefers-color-scheme.
  darkMode: 'class',
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './public/**/*.html',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
  // Explicitly generate all group-data-[sidebar-size] variants we need
  safelist: [
    { pattern: /group-data-\[sidebar-size=sm\]:.*/ },
    { pattern: /group-data-\[sidebar-size=lg\]:.*/ },
    { pattern: /:group-data-\[sidebar-size=sm\].*/ },
  ],
}
