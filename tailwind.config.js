export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './resources/css/**/*.css',
    './resources/views/**/*.vue',
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        gray: {
          750: '#1e2736',
        },
      },
      animation: {
        fadeIn: 'fadeIn 0.4s ease-in-out',
        slideDown: 'slideDown 0.3s ease-in-out',
        slideInRight: 'slideInRight 0.4s ease-in-out',
        scaleIn: 'scaleIn 0.3s ease-in-out',
      },
      transitionProperty: {
        height: 'height',
        spacing: 'margin, padding',
      },
    },
  },
  plugins: [require('daisyui')],
}