import React, { useState } from 'react';
import axios from 'axios';
import { Container, Form, Button } from 'react-bootstrap';
import { useNavigate } from 'react-router-dom';
import { API_BASE_URL } from '../config/api';

function CreateCRM() {
  const [formData, setFormData] = useState({
    name: '',
    subdomain: '',
    avatar: '',
    website: '',
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
      // Отправляем POST-запрос на API для создания новой CRM
      const response = await axios.post('http://'+window.location.hostname+'/api/api/crms', formData);
      setSuccess(response.data.message || 'CRM успешно создана');

      // Можно сохранить данные, если нужно, или перенаправить пользователя
      navigate('/'); // например, редирект на домашнюю страницу
    } catch (err) {
      if (err.response && err.response.data) {
        setError(err.response.data.message || 'Ошибка создания CRM');
      } else {
        setError('Ошибка запроса');
      }
    }
  };

  return (
    <Container className="mt-5">
      <h2>Создание новой CRM</h2>
      {error && <p className="text-danger">{error}</p>}
      {success && <p className="text-success">{success}</p>}
      <Form onSubmit={handleSubmit}>
        <Form.Group controlId="crmName" className="mb-3">
          <Form.Label>Название CRM</Form.Label>
          <Form.Control
            type="text"
            name="name"
            value={formData.name}
            placeholder="Введите название CRM"
            onChange={handleChange}
            required
          />
        </Form.Group>

        <Form.Group controlId="crmSubdomain" className="mb-3">
          <Form.Label>Поддомен</Form.Label>
          <Form.Control
            type="text"
            name="subdomain"
            value={formData.subdomain}
            placeholder="Введите поддомен (например, mycompany)"
            onChange={handleChange}
            required
          />
        </Form.Group>

        <Form.Group controlId="crmAvatar" className="mb-3">
          <Form.Label>Аватар (URL)</Form.Label>
          <Form.Control
            type="text"
            name="avatar"
            value={formData.avatar}
            placeholder="Введите URL для аватара (опционально)"
            onChange={handleChange}
          />
        </Form.Group>

        <Form.Group controlId="crmWebsite" className="mb-3">
          <Form.Label>Сайт</Form.Label>
          <Form.Control
            type="text"
            name="website"
            value={formData.website}
            placeholder="Введите URL сайта (опционально)"
            onChange={handleChange}
          />
        </Form.Group>

        <Button variant="primary" type="submit">
          Создать CRM
        </Button>
      </Form>
    </Container>
  );
}

export default CreateCRM;
