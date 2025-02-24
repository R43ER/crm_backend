// src/pages/Tasks.jsx
import React, { useState, useEffect, useCallback } from 'react';
import axios from 'axios';
import { Container, Table, Alert, Spinner, Button } from 'react-bootstrap';
import EditableCell from '../components/EditableCell';
import PopupCreateTask from '../components/PopupCreateTask';
import PopupEditTask from '../components/PopupEditTask';
import DeleteConfirmationModal from '../components/DeleteConfirmationModal';
import { API_BASE_URL } from '../config/api';

function Tasks() {
  const [tasks, setTasks] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  // Состояния для попапов редактирования и подтверждения удаления
  const [showCreatePopup, setShowCreatePopup] = useState(false);
  const [showEditPopup, setShowEditPopup] = useState(false);
  const [showDeletePopup, setShowDeletePopup] = useState(false);

  // Храним текущую задачу для редактирования/удаления
  const [selectedTask, setSelectedTask] = useState(null);
  const token = localStorage.getItem('token');

  const fetchTasks = useCallback(async () => {
    try {
      const response = await axios.get(
        'http://' + window.location.hostname + '/api/api/tasks',
        { headers: { Authorization: `Bearer ${token}` } }
      );
      setTasks(response.data);
      setLoading(false);
    } catch (err) {
      setError(err.response?.data?.message || 'Ошибка загрузки заданий');
      setLoading(false);
    }
  }, [token]);

  useEffect(() => {
    fetchTasks();
  }, [fetchTasks]);

  const handleUpdateTask = async (id, field, newValue) => {
    try {
      await axios.put(
        'http://' + window.location.hostname + `/api/api/tasks/${id}`,
        { [field]: newValue },
        { headers: { Authorization: `Bearer ${token}` } }
      );
      setTasks(prev =>
        prev.map(task =>
          task.id === id ? { ...task, [field]: newValue } : task
        )
      );
    } catch (err) {
      setError(err.response?.data?.message || 'Ошибка обновления задания');
    }
  };

  const handleDeleteTask = async () => {
    try {
      await axios.delete(
        'http://' + window.location.hostname + `/api/api/tasks/${selectedTask.id}`,
        { headers: { Authorization: `Bearer ${token}` } }
      );
      setShowDeletePopup(false);
      fetchTasks();
    } catch (err) {
      setError(err.response?.data?.message || 'Ошибка удаления задания');
    }
  };

  return (
    <Container className="mt-5">
      <h2>Задания</h2>
      <Button variant="primary" onClick={() => setShowCreatePopup(true)} className="mb-3">
        Создать задание
      </Button>

      {loading && (
        <div className="text-center my-3">
          <Spinner animation="border" variant="primary" />
        </div>
      )}
      {error && <Alert variant="danger">{error}</Alert>}
      {!loading && !error && (
        <Table striped bordered hover responsive>
          <thead>
            <tr>
              <th>ID</th>
              <th>Текст задания</th>
              <th>Результат</th>
              <th>Тип</th>
              <th>Начало исполнения</th>
              <th>Конец исполнения</th>
              <th>Действия</th>
            </tr>
          </thead>
          <tbody>
            {tasks.map(task => (
              <tr key={task.id}>
                <td>{task.id}</td>
                <td>
                  <EditableCell
                    value={task.task_text}
                    onSave={(newValue) =>
                      handleUpdateTask(task.id, 'task_text', newValue)
                    }
                  />
                </td>
                <td>
                  <EditableCell
                    value={task.result || ''}
                    onSave={(newValue) =>
                      handleUpdateTask(task.id, 'result', newValue)
                    }
                  />
                </td>
                <td>
                  <EditableCell
                    value={task.type}
                    onSave={(newValue) =>
                      handleUpdateTask(task.id, 'type', newValue)
                    }
                  />
                </td>
                <td>
                  <EditableCell
                    value={task.execution_start}
                    onSave={(newValue) =>
                      handleUpdateTask(task.id, 'execution_start', newValue)
                    }
                  />
                </td>
                <td>
                  <EditableCell
                    value={task.execution_end}
                    onSave={(newValue) =>
                      handleUpdateTask(task.id, 'execution_end', newValue)
                    }
                  />
                </td>
                <td>
                  <Button
                    variant="warning"
                    size="sm"
                    onClick={() => {
                      setSelectedTask(task);
                      setShowEditPopup(true);
                    }}
                    className="me-2"
                  >
                    Редактировать
                  </Button>
                  <Button
                    variant="danger"
                    size="sm"
                    onClick={() => {
                      setSelectedTask(task);
                      setShowDeletePopup(true);
                    }}
                  >
                    Удалить
                  </Button>
                </td>
              </tr>
            ))}
          </tbody>
        </Table>
      )}

      <PopupCreateTask
        show={showCreatePopup}
        onClose={() => setShowCreatePopup(false)}
        onSuccess={fetchTasks}
      />

      {selectedTask && (
        <PopupEditTask
          show={showEditPopup}
          onClose={() => setShowEditPopup(false)}
          onSuccess={fetchTasks}
          task={selectedTask}
        />
      )}

      {selectedTask && (
        <DeleteConfirmationModal
          show={showDeletePopup}
          onClose={() => setShowDeletePopup(false)}
          onConfirm={handleDeleteTask}
          contact={selectedTask}
        />
      )}
    </Container>
  );
}

export default Tasks;
