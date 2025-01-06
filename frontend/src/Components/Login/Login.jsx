import React, { useState } from "react";
import axios from "axios";
import { useNavigate } from "react-router-dom";
import backgroundImage from "../../assets/images/Haidong-1.jpg";
import logoFrance from "../../assets/icones/logo-france.png";

// function Login() {
//   const [email, setEmail] = useState("");
//   const [password, setPassword] = useState("");
//   const [error, setError] = useState(null);
//   const navigate = useNavigate();

//   const API_URL = process.env.REACT_APP_API_URL || "http://localhost:8000";

//   const handleSubmit = async (e) => {
//     e.preventDefault();
//     try {
//       const response = await axios.post(`${API_URL}/api/login_check`, {
//         username: email,
//         password: password,
//       });

//       if (response.data.token) {
//         localStorage.setItem("token", response.data.token);
//         navigate("/dashboard");
//       }
//     } catch (err) {
//       setError("Email ou mot de passe incorrect");
//       console.error("Erreur de connexion:", err);
//     }
//   };

import { useDispatch } from 'react-redux';
import { setToken } from '../../store/slices/authSlice';

function Login() {
  const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [error, setError] = useState(null);
    const navigate = useNavigate();
  
    const API_URL = process.env.REACT_APP_API_URL || "http://localhost:8000";
  
  const dispatch = useDispatch();
  
  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const response = await axios.post(`${API_URL}/api/login_check`, {
        username: email,
        password: password,
      });

      if (response.data.token) {
        dispatch(setToken(response.data.token));
        navigate("/dashboard");
      }
    } catch (err) {
      setError("Email ou mot de passe incorrect");
      console.error("Erreur de connexion:", err);
    }
  };
  return (
    <div className="min-h-screen flex items-center  justify-center relative m-0 p-0">
      {/* Background Image */}
      <div
        className="absolute inset-0 bg-cover bg-center z-0 
                    md:bg-contain bg-no-repeat 
                    mobile:w-1/2 mobile:bg-cover mobile:bg-center"
        style={{
          backgroundImage: `url(${backgroundImage})`,
        }}
      />

      {/* Login Form */}
      <div
        className="absolute p-4 inset-y-0 right-0 w-full flex items-center justify-center 
                      lg:w-auto lg:justify-end"
      >
        <div
          className="w-full max-w-md lg:w-[calc(5/7*100vw)] lg:max-h-[70vh] lg:mr-14 
                        bg-white/60 p-8 rounded-xl shadow-lg backdrop-blur-sm relative"
        >
          {/* <div className="min-h-screen flex items-center justify-center bg-[] font-comfortaa">
    <div className="w-full max-w-md p-8 space-y-6 bg-white rounded-xl shadow-lg"> */}
          <h1 className="text-xl font-light text-center text-[#171B2C] font-rock-salt">
            Samurang Portal
          </h1>

          {error && (
            <div
              className="bg-red-100 border border-red-400 text-red-700 px-4 pt-3 rounded relative"
              role="alert"
            >
              {error}
            </div>
          )}
          <div className="pt-8 pb-3 font-bold text-sm font-comfortaa">
            Login
          </div>
          <form
            onSubmit={handleSubmit}
            className="space-y-4 flex flex-col pb-12 px-6 justify-center text-sm"
          >
            <div>
              {/* <label
            htmlFor="email"
            className="block text-xs font-medium py-2 text-[#171B2C]"
          >
            Email
          </label> */}
              <input
                type="email"
                id="email"
                placeholder="Email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
                className="mt-1 mb-4 block w-full px-3 py-3 border border-[#D7DADD] rounded-3xl shadow-sm focus:outline-none focus:ring-[#3E61E0] focus:border-[#3E61E0]"
              />
            </div>

            <div>
              {/* <label
            htmlFor="password"
            className="block text-xs font-medium py-2 text-[#171B2C]"
          >
            Mot de passe
          </label> */}
              <input
                type="password"
                id="password"
                placeholder="Password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                required
                className="mt-1 block w-full px-3 py-3 mb-8 border border-[#D7DADD] rounded-3xl shadow-sm focus:outline-none focus:ring-[#3E61E0] focus:border-[#3E61E0]"
              />
            </div>

            <button
              type="submit"
              className=" mx-auto w-1/2 py-3 px-4 mb-4 bg-[#3E61E0] text-white rounded-3xl hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
              Se connecter
            </button>
          </form>
          {/* Logo France */}
          <div className="absolute bottom-4 right-4 w-8 h-12">
            <img
              src={logoFrance}
              alt="Logo France"
              className="w-full h-full object-contain"
            />
          </div>
        </div>
      </div>
    </div>
  );
}

export default Login;
