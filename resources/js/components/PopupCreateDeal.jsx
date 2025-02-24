// src/components/PopupCreateDeal.jsx
import React, { useState, useEffect } from 'react';
import { Modal, Form, Button, Alert } from 'react-bootstrap';
import axios from 'axios';
import Select from 'react-select';

const PopupCreateDeal = ({ show, onClose, onSuccess }) => {
  const [formData, setFormData] = useState({
    title: '',
    budget: '',
    status: 'new',
    responsible_user_id: null,
  });
  const [employees, setEmployees] = useState([]);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const token = localStorage.getItem('token');

  useEffect(() => {
    axios.get(`http://${window.location.hostname}/api/api/users`, {
      headers: { Authorization: `Bearer ${token}` },
    })
    .then(response => {
      const options = response.data.map(user => ({
        value: user.id,
        label: user.name,
      }));
      setEmployees(options);
    })
    .catch(err => {
      console.error("Ошибка получения сотрудников:", err);
    });
  }, [token]);

  const handleChange = (e) => {
    setFormData({ 
      ...formData, 
      [e.target.name]: e.target.name === 'budget' ? parseFloat(e.target.value) : e.target.value 
    });
  };

  const handleSelectChange = (selectedOption) => {
    setFormData({ 
      ...formData, 
      responsible_user_id: selectedOption ? selectedOption.value : null 
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setLoading(true);
    try {
      await axios.post(
        `http://${window.location.hostname}/api/api/deals`,
        formData,
        { headers: { Authorization: `Bearer ${token}` } }
      );
      setLoading(false);
      onSuccess();
      onClose();
    } catch (err) {
      setLoading(false);
      setError(err.response?.data?.message || 'Ошибка создания сделки');
    }
  };

  return (
    <Modal show={show} onHide={onClose}>
      <Modal.Header closeButton>
        <Modal.Title>Создать сделку</Modal.Title>
      </Modal.Header>
      <Modal.Body>
        {error && <Alert variant="danger">{error}</Alert>}
        <Form onSubmit={handleSubmit}>
          <Form.Group controlId="dealTitle" className="mb-3">
            <Form.Label>Название</Form.Label>
            <Form.Control
              type="text"
              name="title"
              value={formData.title}
              onChange={handleChange}
              placeholder="Введите название сделки"
              required
            />
          </Form.Group>
          <Form.Group controlId="dealBudget" className="mb-3">
            <Form.Label>Бюджет</Form.Label>
            <Form.Control
              type="number"
              name="budget"
              value={formData.budget}
              onChange={handleChange}
              placeholder="Введите бюджет"
              required
              step="0.01"
            />
          </Form.Group>
          <Form.Group controlId="dealStatus" className="mb-3">
            <Form.Label>Статус</Form.Label>
            <Form.Control
              as="select"
              name="status"
              value={formData.status}
              onChange={handleChange}
              required
            >
              <option value="new">Новая</option>
              <option value="in progress">В работе</option>
              <option value="closed">Закрыта</option>
            </Form.Control>
          </Form.Group>
          <Form.Group controlId="responsibleEmployee" className="mb-3">
            <Form.Label>Ответственный сотрудник</Form.Label>
            <Select
              options={employees}
              onChange={handleSelectChange}
              placeholder="Выберите сотрудника..."
              isClearable
            />
          </Form.Group>
          <Button variant="primary" type="submit" disabled={loading}>
            {loading ? 'Создание...' : 'Создать сделку'}
          </Button>
        </Form>
      </Modal.Body>
    </Modal>
  );
};

export default PopupCreateDeal;
