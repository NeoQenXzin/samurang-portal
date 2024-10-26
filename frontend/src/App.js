import { BrowserRouter as Router, Route, Routes, Navigate } from 'react-router-dom';
import HomePage from './Components/Home/HomePage';
import Events from './Components/FormationList';
import Login from './Components/Login/Login';
import Logout from './Components/Login/Logout';

function App() {
  // Fonction pour vérifier si l'utilisateur est authentifié
  const isAuthenticated = () => {
    return !!localStorage.getItem('token');
  };

  // Composant pour protéger les routes
  const PrivateRoute = ({ children }) => {
    return isAuthenticated() ? children : <Navigate to="/" />;
  };

  return (
    <Router>
      <Routes>
        <Route path="/" element={!isAuthenticated() ? <Login /> : <Navigate to="/home" />} />
        <Route 
          path="/home" 
          element={
            <PrivateRoute>
              <HomePage />
            </PrivateRoute>
          } 
        />
        <Route 
          path="/events" 
          element={
            <PrivateRoute>
              <Events />
            </PrivateRoute>
          } 
        />
        <Route path="/logout" element={<Logout />} />
      </Routes>
    </Router>
  );
}

export default App;