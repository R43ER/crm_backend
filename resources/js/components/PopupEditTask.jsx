// src/components/PopupEditTask.jsx
import React, { useState, useEffect } from 'react';
import { Modal, Form, Button, Alert } from 'react-bootstrap';
import axios from 'axios';

const PopupEditTask = ({ show, onClose, onSuccess, task }) => {
  const [formData, setFormData] = useState({
    task_text: '',
    result: '',
    type: '',
    execution_start: '',
    execution_end: '',
  });
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  
  useEffect(() => {
    if (task) {
      setFormData({
        task_text: task.task_text || '',
        result: task.result || '',
        type: task.type || 'call',
        execution_start: task.execution_start || '',
        execution_end: task.execution_end || '',
      });
    }
  }, [task]);
  
  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };
  
  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setLoading(true);
    try {
      const token = localStorage.getItem('token');
      await axios.put(
        'http://' + window.location.hostname + `/api/api/tasks/${task.id}`,
        formData,
        { headers: { Authorization: `Bearer ${token}` } }
      );
      setLoading(false);
      onSuccess();
      onClose();
    } catch (err) {
      setLoading(false);
      setError(err.response?.data?.message || 'Ошибка обновления задания');
    }
  };

  return (
    <Modal show={show} onHide={onClose}>
      <Modal.Header closeButton>
        <Modal.Title>Редактировать задание</Modal.Title>
      </Modal.Header>
      <Modal.Body>
        {error && <Alert variant="danger">{error}</Alert>}
        <Form onSubmit={handleSubmit}>
          <Form.Group controlId="editTaskText" className="mb-3">
            <Form.Label>Текст задания</Form.Label>
            <Form.Control
              type="text"
              name="task_text"
              value={formData.task_text}
              onChange={handleChange}
              required
            />
          </Form.Group>
          <Form.Group controlId="editTaskType" className="mb-3">
            <Form.Label>Тип задания</Form.Label>
            <Form.Control
              as="select"
              name="type"
              value={formData.type}
              onChange={handleChange}
              required
            >
              <option value="call">Звонок</option>
              <option value="meeting">Встреча</option>
              <option value="email">Email</option>
            </Form.Control>
          </Form.Group>
          <Form.Group controlId="editExecutionStart" className="mb-3">
            <Form.Label>Начало исполнения (Y-m-d H:i:s)</Form.Label>
            <Form.Control
              type="text"
              name="execution_start"
              value={formData.execution_start}
              onChange={handleChange}
              placeholder="2025-03-07 11:00:00 (опционально)"
            />
          </Form.Group>
          <Form.Group controlId="editExecutionEnd" className="mb-3">
            <Form.Label>Конец исполнения (Y-m-d H:i:s)</Form.Label>
            <Form.Control
              type="text"
              name="execution_end"
              value={formData.execution_end}
              onChange={handleChange}
              placeholder="2025-03-07 13:30:00 (опционально)"
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

export default PopupEditTask;
