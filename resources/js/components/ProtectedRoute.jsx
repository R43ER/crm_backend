import React from 'react';
import { Navigate, Outlet } from 'react-router-dom';

const ProtectedRoute = () => {
  // Проверяем, есть ли токен в localStorage
  const token = localStorage.getItem('token');

  // Если токен отсутствует, перенаправляем на страницу логина
  if (!token) {
    return <Navigate to="/login" replace />;
  }

  // Если токен есть, рендерим вложенные маршруты
  return <Outlet />;
};

export default ProtectedRoute;
