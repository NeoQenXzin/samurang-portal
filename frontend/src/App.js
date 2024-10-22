import './App.css';
import { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import Home from './Components/Home/Home';
import HomePage from './Components/Home/HomePage';
import Events from './Components/FormationList';
// import FormationsList from './Components/FormationList';


function App() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/Home" element={<HomePage />} />
        <Route path="/Events" element={<Events/>} />
      </Routes>
    </Router>
  );
}

export default App;
