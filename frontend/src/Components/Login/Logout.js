// // Components/Login/Logout.jsx
// import { useEffect } from 'react';
// import { useNavigate } from 'react-router-dom';
// import axios from 'axios';

// const Logout = () => {
//   const navigate = useNavigate();

//   useEffect(() => {
//     const performLogout = async () => {
//       try {
//         const token = localStorage.getItem('token');
//         if (token) {
//           await axios.post('http://localhost:8000/logout', null, {
//             headers: {
//               Authorization: `Bearer ${token}`
//             }
//           });
//           localStorage.removeItem('token');
//         }
//         navigate('/');
//       } catch (error) {
//         console.error('Erreur lors de la déconnexion', error);
//         navigate('/');
//       }
//     };

//     performLogout();
//   }, [navigate]);

//   return null;
// };

// export default Logout;







import React, { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useDispatch } from 'react-redux';
import { clearProfile } from '../../store/slices/profileSlice';

function Logout() {
  const navigate = useNavigate();
  const dispatch = useDispatch();

  useEffect(() => {
    const handleLogout = () => {
      dispatch(clearProfile());
      localStorage.removeItem('token');
      navigate('/', { replace: true });
    };

    handleLogout();
  }, [dispatch, navigate]);

  return null;
}

export default Logout;