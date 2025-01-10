// import { createSlice } from '@reduxjs/toolkit';

// const authSlice = createSlice({
//   name: 'auth',
//   initialState: {
//     token: localStorage.getItem('token') || null,
//   },
//   reducers: {
//     setToken: (state, action) => {
//       state.token = action.payload;
//       localStorage.setItem('token', action.payload);
//     },
//     clearToken: (state) => {
//       state.token = null;
//       localStorage.removeItem('token');
//     },
//   },
// });

// export const { setToken, clearToken } = authSlice.actions;
// export default authSlice.reducer;



// store/slices/authSlice.js
import { createSlice } from '@reduxjs/toolkit';

const authSlice = createSlice({
  name: 'auth',
  initialState: {
    token: null,  // On retire l'accès direct à localStorage ici
  },
  reducers: {
    setToken: (state, action) => {
      state.token = action.payload;
      if (typeof window !== 'undefined') {  // Vérification de l'environnement
        localStorage.setItem('token', action.payload);
      }
    },
    clearToken: (state) => {
      state.token = null;
      if (typeof window !== 'undefined') {  // Vérification de l'environnement
        localStorage.removeItem('token');
      }
    },
  },
});

export const { setToken, clearToken } = authSlice.actions;
export default authSlice.reducer;