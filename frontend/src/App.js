import { BrowserRouter as Router, Route, Routes, Navigate } from 'react-router-dom';
import HomePage from './Components/Home/HomePage';
import Events from './Components/FormationList';
import Training from './Components/Training/Training';
import Profil from './Components/Profil/Profil';
import Login from './Components/Login/Login';
import Logout from './Components/Login/Logout';
import Navbar from './Components/Navbar/Navbar';
import dojo from './assets/icones/dojo-1.png'
import calendar from './assets/icones/calendar.png'
import symbol from './assets/icones/symbol.png'
import dummy from './assets/icones/dummy.png'

function App() {
  const isAuthenticated = () => {
    return !!localStorage.getItem('token');
  };

  const PrivateRoute = ({ children }) => {
    if (!isAuthenticated()) {
      return <Navigate to="/" />;
    }

    return (
      <div style={{ display: 'flex' }}>
        <Navbar
          li={[
            ["Dashboard", dojo, "/home"],
            ["Events", calendar, "/events"],
            ["Training", dummy, "/training"],
            ["Profil", symbol, "/profil"]
          ]}
        />
        <div style={{ flexGrow: 1 }}>
          {children}
        </div>
      </div>
    );
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
        <Route
          path="/training"
          element={
            <PrivateRoute>
              <Training />
            </PrivateRoute>
          }
        />
        <Route
          path="/profil"
          element={
            <PrivateRoute>
              <Profil />
            </PrivateRoute>
          }
        />
        <Route path="/logout" element={<Logout />} />
      </Routes>
    </Router>
  );
}

export default App;