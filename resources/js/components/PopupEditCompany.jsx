// src/components/PopupEditCompany.jsx
import React, { useState, useEffect } from 'react';
import { Modal, Form, Button, Alert } from 'react-bootstrap';
import axios from 'axios';

const PopupEditCompany = ({ show, onClose, onSuccess, company }) => {
  const [formData, setFormData] = useState({
    name: '',
    phone: '',
    email: '',
    web: '',
    address: '',
  });
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (company) {
      setFormData({
        name: company.name || '',
        phone: company.phone || '',
        email: company.email || '',
        web: company.web || '',
        address: company.address || '',
      });
    }
  }, [company]);

  const handleChange = e =>
    setFormData({ ...formData, [e.target.name]: e.target.value });

  const handleSubmit = async e => {
    e.preventDefault();
    setError('');
    setLoading(true);
    try {
      const token = localStorage.getItem('token');
      await axios.put(
        'http://' + window.location.hostname + `/api/api/companies/${company.id}`,
        formData,
        { headers: { Authorization: `Bearer ${token}` } }
      );
      setLoading(false);
      onSuccess();
      onClose();
    } catch (err) {
      setLoading(false);
      setError(err.response?.data?.message || 'Ошибка обновления компании');
    }
  };

  return (
    <Modal show={show} onHide={onClose}>
      <Modal.Header closeButton>
        <Modal.Title>Редактировать компанию</Modal.Title>
      </Modal.Header>
      <Modal.Body>
        {error && <Alert variant="danger">{error}</Alert>}
        <Form onSubmit={handleSubmit}>
          <Form.Group controlId="editCompanyName" className="mb-3">
            <Form.Label>Название</Form.Label>
            <Form.Control
              type="text"
              name="name"
              value={formData.name}
              onChange={handleChange}
              required
            />
          </Form.Group>
          <Form.Group controlId="editCompanyPhone" className="mb-3">
            <Form.Label>Телефон</Form.Label>
            <Form.Control
              type="text"
              name="phone"
              value={formData.phone}
              onChange={handleChange}
            />
          </Form.Group>
          <Form.Group controlId="editCompanyEmail" className="mb-3">
            <Form.Label>Email</Form.Label>
            <Form.Control
              type="email"
              name="email"
              value={formData.email}
              onChange={handleChange}
              required
            />
          </Form.Group>
          <Form.Group controlId="editCompanyWeb" className="mb-3">
            <Form.Label>Веб-сайт</Form.Label>
            <Form.Control
              type="text"
              name="web"
              value={formData.web}
              onChange={handleChange}
            />
          </Form.Group>
          <Form.Group controlId="editCompanyAddress" className="mb-3">
            <Form.Label>Адрес</Form.Label>
            <Form.Control
              type="text"
              name="address"
              value={formData.address}
              onChange={handleChange}
            />
          </Form.Group>
          <Button variant="primary" type="submit" disabled={loading}>
            {loading ? 'Сохранение...' : 'Сохранить изменения'}
          </Button>
        </Form>
      </Modal.Body>
    </Modal>
  );
};

export default PopupEditCompany;
