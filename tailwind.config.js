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
        primary: {
          DEFAULT: '#F7A923',
          hover: '#E6940F',
          dark: '#D2840E',
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
  daisyui: {
    themes: [
      {
        light: {
          primary: "#F7A923",
          "primary-content": "#2C3E50",
          secondary: "#E6940F",
          accent: "#D2840E",
        },
      },
    ],
  },
}