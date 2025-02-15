import axios from 'axios';

const API_URL = process.env.REACT_APP_API_URL || "http://localhost:8000";

axios.defaults.baseURL = API_URL;

export const setupAxiosInterceptors = () => {
  axios.interceptors.request.use(
    (config) => {
      const token = localStorage.getItem('token');
      if (token) {
        config.headers.Authorization = `Bearer ${token}`;
      }
      return config;
    },
    (error) => Promise.reject(error)
  );

  axios.interceptors.response.use(
    (response) => response,
    (error) => {
      if (error.response?.status === 401) {
        localStorage.removeItem('token');
        window.location.href = '/';
      }
      return Promise.reject(error);
    }
  );
};