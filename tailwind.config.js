module.exports = {
  darkMode: 'class',
  content: [
    './resources/**/*.blade.php',
    './app/**/*.php',
    './vendor/laravel/jetstream/**/*.blade.php',
    './vendor/livewire/**/*.php',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          500: '#6366f1',
        },
      },
    },
  },
  plugins: [require('@tailwindcss/forms')],
}
