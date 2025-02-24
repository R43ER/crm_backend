import React, { useState, useEffect } from 'react';
import { Modal, Form, Button, Alert } from 'react-bootstrap';
import axios from 'axios';

const PopupEditContact = ({ show, onClose, onSuccess, contact }) => {
  const [formData, setFormData] = useState({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    position: '',
    company_id: '',
    // crm_id можно не редактировать, он определяется на сервере
  });
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  // При открытии попапа заполняем форму данными контакта
  useEffect(() => {
    if (contact) {
      setFormData({
        first_name: contact.first_name || '',
        last_name: contact.last_name || '',
        email: contact.email || '',
        phone: contact.phone || '',
        position: contact.position || '',
        company_id: contact.company_id || '',
      });
    }
  }, [contact]);

  const handleChange = e => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async e => {
    e.preventDefault();
    setError('');
    setLoading(true);
    try {
      const token = localStorage.getItem('token');
      await axios.put('http://'+window.location.hostname+`/api/api/contacts/${contact.id}`, formData, {
        headers: { Authorization: `Bearer ${token}` },
      });
      setLoading(false);
      onSuccess(); // обновляем список контактов
      onClose();   // закрываем попап
    } catch (err) {
      setLoading(false);
      setError(err.response?.data?.message || 'Ошибка обновления контакта');
    }
  };

  return (
    <Modal show={show} onHide={onClose}>
      <Modal.Header closeButton>
        <Modal.Title>Редактировать контакт</Modal.Title>
      </Modal.Header>
      <Modal.Body>
        {error && <Alert variant="danger">{error}</Alert>}
        <Form onSubmit={handleSubmit}>
          <Form.Group controlId="editFirstName" className="mb-3">
            <Form.Label>Имя</Form.Label>
            <Form.Control
              type="text"
              name="first_name"
              value={formData.first_name}
              onChange={handleChange}
              required
            />
          </Form.Group>
          <Form.Group controlId="editLastName" className="mb-3">
            <Form.Label>Фамилия</Form.Label>
            <Form.Control
              type="text"
              name="last_name"
              value={formData.last_name}
              onChange={handleChange}
              required
            />
          </Form.Group>
          <Form.Group controlId="editEmail" className="mb-3">
            <Form.Label>Email</Form.Label>
            <Form.Control
              type="email"
              name="email"
              value={formData.email}
              onChange={handleChange}
              required
            />
          </Form.Group>
          <Form.Group controlId="editPhone" className="mb-3">
            <Form.Label>Телефон</Form.Label>
            <Form.Control
              type="text"
              name="phone"
              value={formData.phone}
              onChange={handleChange}
            />
          </Form.Group>
          <Form.Group controlId="editPosition" className="mb-3">
            <Form.Label>Должность</Form.Label>
            <Form.Control
              type="text"
              name="position"
              value={formData.position}
              onChange={handleChange}
            />
          </Form.Group>
          <Form.Group controlId="editCompanyId" className="mb-3">
            <Form.Label>ID компании (опционально)</Form.Label>
            <Form.Control
              type="text"
              name="company_id"
              value={formData.company_id}
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

export default PopupEditContact;
