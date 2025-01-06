import { BrowserRouter as Router, Route, Routes, Navigate, useLocation } from 'react-router-dom';
import { useEffect } from 'react';
import { store } from './store/store';
import { persistor } from './store/store';
import { PersistGate } from 'redux-persist/integration/react';
import { Provider, useSelector } from 'react-redux';
import { setupAxiosInterceptors } from './utils/auth';
import Dashboard from './Components/Dashboard/Dashboard';
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

  useEffect(() => {
    setupAxiosInterceptors();
  }, []);

  // const isAuthenticated = () => {
  //   return !!localStorage.getItem('token');
  // };
  const isAuthenticated = useSelector(state => !!state.auth.token);


  const PrivateRoute = ({ children }) => {
    const location = useLocation();

    if (!isAuthenticated) {
      <Navigate to="/" state={{ from: location }} replace />;
      return <Login />
    }


    return (
      <div style={{ display: 'flex' }}>
        <Navbar
          li={[
            ["Dashboard", dojo, "/dashboard"],
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

    <Provider store={store}>
      <PersistGate loading={null} persistor={persistor}>

        <Router>
          <Routes>
            <Route
              path="/"
              element={
                isAuthenticated ?
                  <Navigate to="/dashboard" replace /> :
                  <Login />
              }
            />
            <Route
              path="/dashboard"
              element={
                <PrivateRoute>
                  <Dashboard />
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
            <Route
              path="/logout"
              element={<Logout />}
            />
            <Route
              path="*"
              element={<Navigate to="/" replace />}
            />
          </Routes>
        </Router>
      </PersistGate>
    </Provider>
  );
}

export default App;