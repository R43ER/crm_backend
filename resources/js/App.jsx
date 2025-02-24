import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Layout from './components/Layout';
import Home from './pages/Home';
import Login from './pages/Login';
import Register from './pages/Register';
import Contacts from './pages/Contacts';
import Companies from './pages/Companies';
import Tasks from './pages/Tasks';
import Deals from './pages/Deals';
import ProtectedRoute from './components/ProtectedRoute';
import Logout from './pages/Logout';

function App() {
  return (
    <Router>
      <Routes>
        {/* Маршруты, доступные без авторизации */}
        <Route index element={<Home />} />
        <Route path="/login" element={<Login />} />
        <Route path="/register" element={<Register />} />

        {/* Защищённые маршруты */}
        <Route element={<ProtectedRoute />}>
          <Route path="/" element={<Layout />}>
            <Route path="logout" element={<Logout />} />
            <Route path="contacts" element={<Contacts />} />
            <Route path="companies" element={<Companies />} />
            <Route path="tasks" element={<Tasks />} />
            <Route path="deals" element={<Deals />} />
            {/* Добавляйте другие защищённые маршруты */}
          </Route>
        </Route>
      </Routes>
    </Router>
  );
}

export default App;
