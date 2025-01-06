import React, { useState, useEffect } from "react";
import { useNavigate, useLocation } from "react-router-dom";

import { useDispatch } from 'react-redux';
import { clearToken } from '../../store/slices/authSlice';
import { clearProfile } from '../../store/slices/profileSlice';

import "./Navbar.css";

// Import your icons
import logout from "../../assets/icones/samurai.png";

const Navbar = ({ li }) => {
  const [isOpen, setIsOpen] = useState(false);
  const navigate = useNavigate();
  const location = useLocation();

  // Responsive menu management
  useEffect(() => {
    const handleResize = () => {
      if (window.innerWidth >= 1025) {
        setIsOpen(false);
      }
    };

    const handleRouteChange = () => {
      if (window.innerWidth < 1025) {
        setIsOpen(false);
      }
    };

    window.addEventListener("resize", handleResize);
    handleRouteChange(); // Close menu on initial route load for mobile

    return () => {
      window.removeEventListener("resize", handleResize);
    };
  }, [location]);
  const dispatch = useDispatch();

  const handleLogout = () => {
    const token = localStorage.getItem('token');
    if (token) {
      localStorage.removeItem('token');
      dispatch(clearToken());
      dispatch(clearProfile());
      console.log("Déconnexion réussie");
    } else {
      console.log("Aucun token trouvé, l'utilisateur est déjà déconnecté");
    }
    navigate('/', { replace: true });
  };

  const toggleMenu = () => {
    setIsOpen(!isOpen);
  };

  const handleNavigation = (path) => {
    navigate(path);
    if (window.innerWidth < 1025) {
      setIsOpen(false);
    }
  };

  return (
    <>
      <div className={`burger ${isOpen ? "active" : ""}`} onClick={toggleMenu}>
        <span></span>
        <span></span>
        <span></span>
      </div>

      <nav className={`navbar-menu ${isOpen ? "open" : ""}`}>
        <div className="navbar-overlay" onClick={() => setIsOpen(false)}></div>
        <div className="navbar-content">
          <h1 className="navbar-title">Samurang Portal</h1>

          <ul className="navbar__list">
            {li.map(([text, icon, path], index) => (
              <div
                key={index}
                className={`navbar__li-box ${
                  location.pathname === path ? "active" : ""
                }`}
                onClick={() => handleNavigation(path)}
              >
                <img src={icon} alt={text} width="28" height="29" />
                <li className="navbar__li">{text}</li>
              </div>
            ))}
          </ul>

          <div className="navbar-logout" onClick={handleLogout}>
            <img src={logout} alt="Déconnexion" width="28" height="29" />
            <span>Déconnexion</span>
          </div>
        </div>
      </nav>
    </>
  );
};

export default Navbar;
