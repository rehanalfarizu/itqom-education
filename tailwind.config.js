/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        primaryDark: '#564AB1',
        primaryLight: '#B0ABDB',
      },
      fontFamily: {
        sans: ['Nunito', 'ui-sans-serif', 'system-ui'],
      },
    },
  },
  plugins: [],
}
