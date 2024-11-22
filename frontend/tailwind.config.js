/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/**/*.{js,jsx,ts,tsx}",
  ],
  theme: {
    fontFamily: {
      'comfortaa': ['Comfortaa', 'sans-serif'],
      'short-stack': ['Short Stack', 'cursive'],
      'lexend-exa': ['Lexend Exa', 'sans-serif'],
      'gluten': ['Gluten', 'cursive'],
      'gaegu': ['Gaegu', 'cursive'],
      'rock-salt': ['Rock Salt', 'cursive']
    },
    extend: {
      colors: {
        'primary-blue': '#3E61E0',
        'background-light': '#F0F4F7',
        'gray-light': '#D7DADD',
        'dark-blue': '#171B2C',
        'golden': '#DAA520'
      }
    },
  },
  plugins: [],
}

