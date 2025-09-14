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
  // Dodaj custom CSS dla Filament components
  safelist: [
    // Dodaj klasy które mogą być dynamicznie generowane
    'mb-12', 'mb-16', 'mb-18', 'mb-20', 'mb-22', 'mb-24', 'mb-26', 'mb-30',
    'mt-12', 'mt-16', 'mt-18', 'mt-20', 'mt-22', 'mt-24', 'mt-26', 'mt-30',
    'my-12', 'my-16', 'my-18', 'my-20', 'my-22', 'my-24', 'my-26', 'my-30',
    'p-4', 'p-6', 'p-8', 'p-12', 'p-16', 'p-18', 'p-20', 'p-22', 'p-24',
    'px-6', 'px-8', 'px-12', 'px-16', 'px-18', 'px-20', 'px-22', 'px-24',
    'py-4', 'py-6', 'py-8', 'py-12', 'py-16', 'py-18', 'py-20', 'py-22', 'py-24',
    // Dodaj klasy dla ikon plików
    'bg-red-500', 'bg-blue-500', 'bg-green-500', 'bg-yellow-500', 'bg-gray-500',
    'bg-purple-500', 'bg-orange-500', 'text-white', 'w-12', 'h-12', 'w-8', 'h-8',
    'flex', 'items-center', 'justify-center', 'rounded-lg', 'space-x-2', 'space-x-3',
    'text-2xl', 'text-sm', 'text-xs', 'font-medium', 'text-gray-900', 'text-gray-500',
    'bg-gray-50', 'border', 'border-gray-200', 'rounded-lg', 'flex-shrink-0', 'flex-1',
    'min-w-0', 'truncate', 'mt-2'
  ]
}
