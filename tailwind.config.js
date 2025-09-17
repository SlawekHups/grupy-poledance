/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './app/Filament/**/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
  ],
  important: true, // Wymuś wszystkie klasy Tailwind nad style Filament
  theme: {
    extend: {
      // Dodaj custom spacing dla lepszej kontroli
      spacing: {
        '18': '4.5rem',
        '22': '5.5rem',
        '26': '6.5rem',
        '30': '7.5rem',
      },
      // Dodaj custom margin/padding utilities
      margin: {
        '18': '4.5rem',
        '22': '5.5rem',
        '26': '6.5rem',
        '30': '7.5rem',
      },
      padding: {
        '18': '4.5rem',
        '22': '5.5rem',
        '26': '6.5rem',
        '30': '7.5rem',
      }
    },
  },
  plugins: [],
  // Dodaj custom CSS dla Filament
  corePlugins: {
    // Zachowaj wszystkie core plugins
    preflight: true,
  },
  // W Tailwind CSS v4 safelist nie jest już potrzebny
  // Klasy są definiowane przez @source inline() w app.css
}
