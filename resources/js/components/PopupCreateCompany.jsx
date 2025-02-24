// src/components/PopupCreateCompany.jsx
import React, { useState } from 'react';
import { Modal, Form, Button, Alert } from 'react-bootstrap';
import axios from 'axios';

const PopupCreateCompany = ({ show, onClose, onSuccess }) => {
  const [formData, setFormData] = useState({
    name: '',
    phone: '',
    email: '',
    web: '',
    address: '',
  });
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  const handleChange = e =>
    setFormData({ ...formData, [e.target.name]: e.target.value });

  const handleSubmit = async e => {
    e.preventDefault();
    setError('');
    setLoading(true);
    try {
      const token = localStorage.getItem('token');
      await axios.post(
        'http://' + window.location.hostname + '/api/api/companies',
        formData,
        { headers: { Authorization: `Bearer ${token}` } }
      );
      setLoading(false);
      onSuccess();
      onClose();
    } catch (err) {
      setLoading(false);
      setError(err.response?.data?.message || 'Ошибка создания компании');
    }
  };

  return (
    <Modal show={show} onHide={onClose}>
      <Modal.Header closeButton>
        <Modal.Title>Создать компанию</Modal.Title>
      </Modal.Header>
      <Modal.Body>
        {error && <Alert variant="danger">{error}</Alert>}
        <Form onSubmit={handleSubmit}>
          <Form.Group controlId="companyName" className="mb-3">
            <Form.Label>Название</Form.Label>
            <Form.Control
              type="text"
              name="name"
              value={formData.name}
              onChange={handleChange}
              placeholder="Введите название компании"
              required
            />
          </Form.Group>
          <Form.Group controlId="companyPhone" className="mb-3">
            <Form.Label>Телефон</Form.Label>
            <Form.Control
              type="text"
              name="phone"
              value={formData.phone}
              onChange={handleChange}
              placeholder="Введите телефон"
            />
          </Form.Group>
          <Form.Group controlId="companyEmail" className="mb-3">
            <Form.Label>Email</Form.Label>
            <Form.Control
              type="email"
              name="email"
              value={formData.email}
              onChange={handleChange}
              placeholder="Введите email"
              required
            />
          </Form.Group>
          <Form.Group controlId="companyWeb" className="mb-3">
            <Form.Label>Веб-сайт</Form.Label>
            <Form.Control
              type="text"
              name="web"
              value={formData.web}
              onChange={handleChange}
              placeholder="Введите веб-сайт"
            />
          </Form.Group>
          <Form.Group controlId="companyAddress" className="mb-3">
            <Form.Label>Адрес</Form.Label>
            <Form.Control
              type="text"
              name="address"
              value={formData.address}
              onChange={handleChange}
              placeholder="Введите адрес"
            />
          </Form.Group>
          <Button variant="primary" type="submit" disabled={loading}>
            {loading ? 'Создание...' : 'Создать компанию'}
          </Button>
        </Form>
      </Modal.Body>
    </Modal>
  );
};

export default PopupCreateCompany;
