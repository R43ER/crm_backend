import React, { useState } from 'react';
import axios from 'axios';
import { useNavigate } from 'react-router-dom';
import { Container, Form, Button } from 'react-bootstrap';
import { API_BASE_URL } from '../config/api';

function Register() {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    crm_id: '', // обязательно, так как бэкенд требует crm_id
  });
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');
  const navigate = useNavigate();

  // Обработчик изменения значений формы
  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  // Обработчик отправки формы
  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setSuccess('');

    try {
      // Если у вас настроен proxy в package.json, можно использовать относительный URL
      const response = await axios.post('http://'+window.location.hostname+'/api/api/register', formData);
      setSuccess(response.data.message);

      // Сохраняем полученный токен, если API его возвращает
      localStorage.setItem('token', response.data.token);

      // Перенаправляем пользователя на главную страницу или в личный кабинет
      navigate('/');
    } catch (err) {
      if (err.response && err.response.data) {
        setError(err.response.data.message || 'Ошибка регистрации');
      } else {
        setError('Ошибка запроса');
      }
    }
  };

  return (
    <Container className="mt-5">
      <h2>Регистрация</h2>
      {error && <p className="text-danger">{error}</p>}
      {success && <p className="text-success">{success}</p>}
      <Form onSubmit={handleSubmit}>
        <Form.Group controlId="registerName" className="mb-3">
          <Form.Label>Имя</Form.Label>
          <Form.Control
            type="text"
            name="name"
            placeholder="Введите имя"
            value={formData.name}
            onChange={handleChange}
            required
          />
        </Form.Group>
        <Form.Group controlId="registerEmail" className="mb-3">
          <Form.Label>Email</Form.Label>
          <Form.Control
            type="email"
            name="email"
            placeholder="Введите email"
            value={formData.email}
            onChange={handleChange}
            required
          />
        </Form.Group>
        <Form.Group controlId="registerPassword" className="mb-3">
          <Form.Label>Пароль</Form.Label>
          <Form.Control
            type="password"
            name="password"
            placeholder="Введите пароль"
            value={formData.password}
            onChange={handleChange}
            required
          />
        </Form.Group>
        <Form.Group controlId="registerPasswordConfirmation" className="mb-3">
          <Form.Label>Подтверждение пароля</Form.Label>
          <Form.Control
            type="password"
            name="password_confirmation"
            placeholder="Подтвердите пароль"
            value={formData.password_confirmation}
            onChange={handleChange}
            required
          />
        </Form.Group>
        <Form.Group controlId="registerCrmId" className="mb-3">
          <Form.Label>CRM ID</Form.Label>
          <Form.Control
            type="text"
            name="crm_id"
            placeholder="Введите CRM ID"
            value={formData.crm_id}
            onChange={handleChange}
            required
          />
        </Form.Group>
        <Button variant="primary" type="submit">
          Зарегистрироваться
        </Button>
      </Form>
    </Container>
  );
}

export default Register;
