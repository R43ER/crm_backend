import React, { useState } from 'react';
import { Modal, Form, Button, Alert } from 'react-bootstrap';
import axios from 'axios';

const PopupCreateContact = ({ show, onClose, onSuccess, crmId }) => {
  const [formData, setFormData] = useState({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    position: '',
    company_id: '',
  });
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  const handleChange = e => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async e => {
    e.preventDefault();
    setError('');
    setLoading(true);
    try {
      const token = localStorage.getItem('token');
      await axios.post('http://'+window.location.hostname+'/api/api/contacts', formData, {
        headers: { Authorization: `Bearer ${token}` },
      });
      setLoading(false);
      // После успешного создания вызываем колбэк для обновления списка
      onSuccess();
      // Закрываем попап
      onClose();
    } catch (err) {
      setLoading(false);
      setError(err.response?.data?.message || 'Ошибка создания контакта');
    }
  };

  return (
    <Modal show={show} onHide={onClose}>
      <Modal.Header closeButton>
        <Modal.Title>Создать контакт</Modal.Title>
      </Modal.Header>
      <Modal.Body>
        {error && <Alert variant="danger">{error}</Alert>}
        <Form onSubmit={handleSubmit}>
          <Form.Group controlId="firstName" className="mb-3">
            <Form.Label>Имя</Form.Label>
            <Form.Control
              type="text"
              name="first_name"
              value={formData.first_name}
              onChange={handleChange}
              required
            />
          </Form.Group>
          <Form.Group controlId="lastName" className="mb-3">
            <Form.Label>Фамилия</Form.Label>
            <Form.Control
              type="text"
              name="last_name"
              value={formData.last_name}
              onChange={handleChange}
              required
            />
          </Form.Group>
          <Form.Group controlId="email" className="mb-3">
            <Form.Label>Email</Form.Label>
            <Form.Control
              type="email"
              name="email"
              value={formData.email}
              onChange={handleChange}
              required
            />
          </Form.Group>
          <Form.Group controlId="phone" className="mb-3">
            <Form.Label>Телефон</Form.Label>
            <Form.Control
              type="text"
              name="phone"
              value={formData.phone}
              onChange={handleChange}
            />
          </Form.Group>
          <Form.Group controlId="position" className="mb-3">
            <Form.Label>Должность</Form.Label>
            <Form.Control
              type="text"
              name="position"
              value={formData.position}
              onChange={handleChange}
            />
          </Form.Group>
          <Form.Group controlId="companyId" className="mb-3">
            <Form.Label>ID компании</Form.Label>
            <Form.Control
              type="text"
              name="company_id"
              value={formData.company_id}
              onChange={handleChange}
              placeholder="Опционально"
            />
          </Form.Group>
          <Button variant="primary" type="submit" disabled={loading}>
            {loading ? 'Создание...' : 'Создать контакт'}
          </Button>
        </Form>
      </Modal.Body>
    </Modal>
  );
};

export default PopupCreateContact;
