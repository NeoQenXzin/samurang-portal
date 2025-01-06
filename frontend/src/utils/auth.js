// export const checkAndRefreshToken = () => {
//   const token = localStorage.getItem('token');
//   if (!token) {
//     window.location.href = '/';
//     return false;
//   }
//   return true;
// };
import axios from 'axios';

const API_URL = process.env.REACT_APP_API_URL || "http://localhost:8000";

export const checkAndRefreshToken = () => {
  const token = localStorage.getItem('token');
  if (!token) {
    return false;
  }
  return true;   
};

export const setupAxiosInterceptors = () => {
  // Request interceptor
  axios.interceptors.request.use(
    (config) => {
      const token = localStorage.getItem('token');
      if (token) {
        config.headers.Authorization = `Bearer ${token}`;
      }
      return config;
    },
    (error) => {
      return Promise.reject(error);
    }
  );

  // Response interceptor
  axios.interceptors.response.use(
    (response) => response,
    (error) => {
      if (error.response?.status === 401 || error.response?.status === 403) {
        localStorage.removeItem('token');
        if (window.location.pathname !== '/') {
          window.location.href = '/';
        }
      }
      return Promise.reject(error);
    }
  );
};