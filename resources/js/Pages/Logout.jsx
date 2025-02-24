import React, { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import { API_BASE_URL } from '../config/api';

function Logout() {
  const navigate = useNavigate();

  useEffect(() => {
    const token = localStorage.getItem('token');

    // Отправляем POST-запрос на API для выхода
    axios
      .post(
        'http://'+window.location.hostname+'/api/api/logout',
        {},
        { headers: { Authorization: `Bearer ${token}` } }
      )
      .then((response) => {
        // Если запрос выполнен успешно, удаляем токен и перенаправляем на страницу логина
        localStorage.removeItem('token');
        localStorage.removeItem('user'); // если есть сохранённые данные пользователя
        navigate('/login');
      })
      .catch((error) => {
        // В случае ошибки всё равно очищаем токен и перенаправляем
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        navigate('/login');
      });
  }, [navigate]);

  return (
    <div className="text-center mt-5">
      <h2>Выход...</h2>
    </div>
  );
}

export default Logout;
